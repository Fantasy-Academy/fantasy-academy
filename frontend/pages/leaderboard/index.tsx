'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';

// --- Typy podle API ---
type Skill = {
  name: string;
  percentage: number;
  percentageChange: number | null;
};

type PlayerStatistics = {
  rank: number;                    // celkové umístění
  challengesAnswered: number;      // počet odehraných kol
  points: number;                  // body — použijeme pro badge Rank (Gold/Silver…)
  skills: Skill[];
};

type PlayerSeasonStatistics = {
  seasonNumber: number;
  rank: number;
  challengesAnswered: number;
  points: number;
  skills: Skill[];
};

type PlayerInfo = {
  id: string;
  isMyself: boolean;
  name: string;                    // použijeme jako „Nickname“
  registeredAt: string;
  overallStatistics: PlayerStatistics;
  seasonsStatistics: PlayerSeasonStatistics[];
};

// --- Pomocná funkce: mapování bodů na badge (pokud nemáš z backu) ---
function getTierFromPoints(points: number): 'Bronze' | 'Silver' | 'Gold' | 'Platinum' {
  if (points >= 1000) return 'Platinum';
  if (points >= 600) return 'Gold';
  if (points >= 300) return 'Silver';
  return 'Bronze';
}

// --- PROPS: dodej pole hráčských ID, které chceš načíst ---
type UserTableProps = {
  playerIds: string[]; // např. ['52a9de01-5f68-4c65-8443-ff04e1fe2642', ...]
  apiBase?: string;    // default 'http://localhost:8080'
};

const UserTable: React.FC<UserTableProps> = ({ playerIds, apiBase = 'http://localhost:8080' }) => {
  const { data: session } = useSession();
  const accessToken = (session as any)?.accessToken as string | undefined;

  const [users, setUsers] = useState<PlayerInfo[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError]   = useState<string | null>(null);

  // Pokud budeš mít někdy endpoint /api/leaderboard, stačí nahradit fetch níže jedním voláním.
  // TODO: pokud existuje např. GET /api/leaderboard, zavolej ho místo Promise.all na jednotlivé hráče.

  useEffect(() => {
    const load = async () => {
      if (!accessToken) return;                 // čekáme na session
      if (!playerIds || playerIds.length === 0) {
        setUsers([]);
        return;
      }

      setLoading(true);
      setError(null);
      try {
        const resArr = await Promise.all(
          playerIds.map(id =>
            fetch(`${apiBase}/api/player/${id}`, {
              method: 'GET',
              headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${accessToken}`,
              },
            })
          )
        );

        const failed = resArr.find(r => !r.ok);
        if (failed) {
          const errPayload = await failed.json().catch(() => ({}));
          throw new Error(errPayload?.detail || `Failed to fetch player ${failed.url} (${failed.status})`);
        }

        const data = (await Promise.all(resArr.map(r => r.json()))) as PlayerInfo[];
        setUsers(data);
      } catch (e: any) {
        setError(e?.message || 'Nepodařilo se načíst hráče.');
      } finally {
        setLoading(false);
      }
    };

    load();
  }, [playerIds, apiBase, accessToken]);

  const rows = useMemo(
    () =>
      users.map(u => ({
        nickname: u.name,
        rankBadge: getTierFromPoints(u.overallStatistics?.points ?? 0),
        roundsPlayed: u.overallStatistics?.challengesAnswered ?? 0,
        overallPlacement: u.overallStatistics?.rank ?? '-',
      })),
    [users]
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

          {loading && (
            <div className="p-4 text-center text-gray-600">Načítám…</div>
          )}

          {error && (
            <div className="p-4 text-center text-red-600">{error}</div>
          )}

          {!loading && !error && rows.length === 0 && (
            <div className="p-4 text-center text-gray-600">Žádní hráči k zobrazení.</div>
          )}

          {!loading && !error && rows.map((user, index) => (
            <div
              key={`${user.nickname}-${index}`}
              className={`flex justify-between p-4 border-b ${index % 2 === 0 ? 'bg-gray-100' : 'bg-white'}`}
            >
              <div className="flex-1">{user.nickname}</div>
              <div className="flex-1">
                <span className="inline-block px-2 py-1 text-xs rounded bg-gray-200">
                  {user.rankBadge}
                </span>
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

export default UserTable;