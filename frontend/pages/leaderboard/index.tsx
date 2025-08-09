// components/leaderboard/LeaderboardTable.tsx
'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';
import Link from 'next/link';

type PlayerSkill = { name: string; percentage: number; percentageChange: number | null };
type LeaderboardEntry = {
  playerId: string;
  playerName: string;
  isMyself: boolean;
  rank: number;
  points: number;
  challengesCompleted: number;
  skills: PlayerSkill[];
};
type HydraCollection<T> = { member: T[] };

function getTierFromPoints(points: number): 'Bronze' | 'Silver' | 'Gold' | 'Platinum' {
  if (points >= 1000) return 'Platinum';
  if (points >= 600) return 'Gold';
  if (points >= 300) return 'Silver';
  return 'Bronze';
}

type LeaderboardTableProps = { apiBase?: string };

const LeaderboardTable: React.FC<LeaderboardTableProps> = ({ apiBase = 'http://localhost:8080' }) => {
  const { data: session } = useSession();
  const accessToken = (session as any)?.accessToken as string | undefined;

  const [rows, setRows] = useState<LeaderboardEntry[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError]   = useState<string | null>(null);

  // serverový čas z hlavičky Date (UTC)
  const [serverDateHeader, setServerDateHeader] = useState<string | null>(null);

  useEffect(() => {
    const load = async () => {
      if (!accessToken) return;
      setLoading(true);
      setError(null);
      try {
        const res = await fetch(`${apiBase}/api/leaderboards`, {
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${accessToken}` },
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to fetch leaderboards (${res.status})`);
        }

        // ⬅️ serverový UTC čas
        const dateHeader = res.headers.get('date'); // např. "Wed, 14 Aug 2024 15:20:10 GMT"
        setServerDateHeader(dateHeader);

        const data = await res.json();
        const list: LeaderboardEntry[] = Array.isArray(data)
          ? data
          : (data as HydraCollection<LeaderboardEntry>).member ?? [];

        setRows(list);
      } catch (e: any) {
        setError(e?.message || 'Nepodařilo se načíst leaderboard.');
      } finally {
        setLoading(false);
      }
    };

    load();
  }, [apiBase, accessToken]);

  const mapped = useMemo(
    () =>
      rows.map((u) => ({
        id: u.playerId,
        nickname: u.playerName,
        rankBadge: getTierFromPoints(u.points ?? 0),
        roundsPlayed: u.challengesCompleted ?? 0,
        overallPlacement: u.rank ?? '-',
        isMyself: u.isMyself,
      })),
    [rows]
  );

  // formátování jako UTC „HH:MM:SS UTC, DD.MM.YYYY“
  const lastUpdatedUTC = (() => {
    if (!serverDateHeader) return '—';
    const d = new Date(serverDateHeader); // toto je už UTC
    const time = d.toISOString().slice(11, 19); // HH:MM:SS
    const date = d.toISOString().slice(0, 10).split('-').reverse().join('.'); // DD.MM.YYYY
    return `${time} UTC, ${date}`;
  })();

  return (
    <section className="px-4 py-8">
      <div className="mx-auto w-full max-w-[1200px] bg-white rounded-xl shadow-md overflow-hidden">
        {/* Header */}
        <div className="px-6 py-5 border-b border-gray-200 flex items-end justify-between">
          <h2 className="font-bebasNeue tracking-wider text-3xl text-charcoal uppercase">
            Leaderboard
          </h2>
          <p className="text-sm text-coolGray">
            Poslední aktualizace: <span className="text-charcoal">{lastUpdatedUTC}</span>
          </p>
        </div>

        {/* Header řádek tabulky */}
        <div className="grid grid-cols-4 gap-4 px-6 py-3 bg-charcoal text-white text-sm font-semibold">
          <div>Nickname</div>
          <div>Rank</div>
          <div>Rounds Played</div>
          <div>Overall Placement</div>
        </div>

        {loading && <div className="px-6 py-4 text-center text-gray-600">Načítám…</div>}
        {error && <div className="px-6 py-4 text-center text-vibrantCoral">{error}</div>}
        {!loading && !error && mapped.length === 0 && (
          <div className="px-6 py-4 text-center text-gray-600">Zatím žádná data v leaderboardu.</div>
        )}

        {!loading && !error && mapped.map((user, index) => (
          <div
            key={user.id}
            className={`grid grid-cols-4 gap-4 px-6 py-4 border-t ${
              index % 2 === 0 ? 'bg-gray-50' : 'bg-white'
            }`}
          >
            <div className="flex items-center">
              <Link
                href={`/players/${user.id}`}
                className={`hover:underline ${user.isMyself ? 'font-semibold' : ''}`}
              >
                {user.nickname}
              </Link>
              {user.isMyself && (
                <span className="ml-2 text-[11px] px-2 py-[2px] rounded bg-gray-200 uppercase tracking-wide">
                  You
                </span>
              )}
            </div>
            <div className="flex items-center">
              <span className="inline-block px-2 py-1 text-xs rounded bg-gray-200">
                {user.rankBadge}
              </span>
            </div>
            <div className="flex items-center">{user.roundsPlayed}</div>
            <div className="flex items-center">{user.overallPlacement}</div>
          </div>
        ))}
      </div>
    </section>
  );
};

export default LeaderboardTable;