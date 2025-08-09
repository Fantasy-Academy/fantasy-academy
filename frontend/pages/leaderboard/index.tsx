// components/leaderboard/LeaderboardTable.tsx
'use client';
import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';
import Link from 'next/link';

// --- Typy podle /api/leaderboards ---
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

  useEffect(() => {
    const load = async () => {
      if (!accessToken) return;
      setLoading(true); setError(null);
      try {
        const res = await fetch(`${apiBase}/api/leaderboards`, {
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${accessToken}` },
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to fetch leaderboards (${res.status})`);
        }
        const data = await res.json();
        const list: LeaderboardEntry[] = Array.isArray(data) ? data : (data as HydraCollection<LeaderboardEntry>).member ?? [];
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

  return (
    <div className="px-4 py-8">
      <div className="overflow-x-auto bg-white shadow-md rounded">
        <div className="min-w-full">
          <div className="flex justify-between p-4 bg-gray-800 text-white">
            <div className="flex-1 text-left">Nickname</div>
            <div className="flex-1 text-left">Rank</div>
            <div className="flex-1 text-left">Rounds Played</div>
            <div className="flex-1 text-left">Overall Placement</div>
          </div>

          {loading && <div className="p-4 text-center text-gray-600">Načítám…</div>}
          {error && <div className="p-4 text-center text-red-600">{error}</div>}
          {!loading && !error && mapped.length === 0 && (
            <div className="p-4 text-center text-gray-600">Zatím žádná data v leaderboardu.</div>
          )}

          {!loading && !error && mapped.map((user, index) => (
            <div
              key={user.id}
              className={`flex justify-between p-4 border-b ${index % 2 === 0 ? 'bg-gray-100' : 'bg-white'}`}
            >
              <div className="flex-1">
                <Link href={`/players/${user.id}`} className={`hover:underline ${user.isMyself ? 'font-semibold' : ''}`}>
                  {user.nickname}
                </Link>
                {user.isMyself && <span className="ml-2 text-xs px-2 py-0.5 rounded bg-gray-200">You</span>}
              </div>
              <div className="flex-1">
                <span className="inline-block px-2 py-1 text-xs rounded bg-gray-200">{user.rankBadge}</span>
              </div>
              <div className="flex-1">{user.roundsPlayed}</div>
              <div className="flex-1">{user.overallPlacement}</div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default LeaderboardTable;