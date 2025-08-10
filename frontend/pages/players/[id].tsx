// pages/players/[id].tsx
'use client';

import React, { useEffect, useState } from 'react';
import { useSearchParams, useRouter } from 'next/navigation';
import { useSession } from 'next-auth/react';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';

type PlayerSkill = { name: string; percentage: number; percentageChange: number | null };
type PlayerStatistics = { rank: number; challengesAnswered: number; points: number; skills: PlayerSkill[] };
type PlayerInfo = {
  id: string;
  isMyself: boolean;
  name: string;
  registeredAt: string;
  overallStatistics: PlayerStatistics;
  seasonsStatistics: Array<{
    seasonNumber: number;
    rank: number;
    challengesAnswered: number;
    points: number;
    skills: PlayerSkill[];
  }>;
};

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL || 'http://localhost:8080';

const PlayerProfilePage: React.FC = () => {
  const { data: session } = useSession();
  const accessToken = (session as any)?.accessToken as string | undefined;

  const searchParams = useSearchParams();
  const router = useRouter();
  const id = searchParams.get('id') || '';

  // fallback pro /players/<id> bez query
  const fallbackId =
    typeof window !== 'undefined'
      ? window.location.pathname.split('/').filter(Boolean).pop() ?? ''
      : '';

  const playerId = id || fallbackId;

  const [player, setPlayer] = useState<PlayerInfo | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError]     = useState<string | null>(null);

  useEffect(() => {
    const load = async () => {
      if (!accessToken || !playerId) return;
      setLoading(true); setError(null);
      try {
        const res = await fetch(`${API_BASE}/api/player/${playerId}`, {
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${accessToken}` },
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to load player (${res.status})`);
        }
        const data = (await res.json()) as PlayerInfo;
        setPlayer(data);
      } catch (e: any) {
        setError(e?.message || 'Nepodařilo se načíst profil hráče.');
      } finally {
        setLoading(false);
      }
    };
    load();
  }, [playerId, accessToken]);

  return (
    <BackgroundWrapper>
      <div className="max-w-5xl mx-auto px-4 sm:px-8 py-8">
        <button
          onClick={() => router.back()}
          className="mb-4 text-sm text-charcoal hover:underline"
        >
          ← Back
        </button>

        <div className="bg-white rounded shadow p-6">
          {loading && <div className="text-coolGray">Načítám…</div>}
          {error && <div className="text-vibrantCoral">{error}</div>}
          {!loading && !error && player && (
            <>
              <div className="flex items-center gap-4">
                <div className="w-24 h-24 rounded-full bg-gray-200 overflow-hidden" />
                <div>
                  <h1 className="text-3xl font-bold text-charcoal">
                    {player.name} {player.isMyself && <span className="ml-2 px-2 py-0.5 text-xs bg-gray-200 rounded">You</span>}
                  </h1>
                  <p className="text-coolGray">
                    Member since: <span className="text-charcoal font-medium">{new Date(player.registeredAt).toLocaleDateString()}</span>
                  </p>
                </div>
              </div>

              <div className="grid md:grid-cols-3 gap-4 mt-6">
                <div className="p-4 bg-gray-50 rounded">
                  <p className="text-sm text-coolGray">Rank</p>
                  <p className="text-2xl font-semibold text-charcoal">#{player.overallStatistics?.rank ?? '—'}</p>
                </div>
                <div className="p-4 bg-gray-50 rounded">
                  <p className="text-sm text-coolGray">Points</p>
                  <p className="text-2xl font-semibold text-charcoal">{player.overallStatistics?.points ?? '—'}</p>
                </div>
                <div className="p-4 bg-gray-50 rounded">
                  <p className="text-sm text-coolGray">Rounds Played</p>
                  <p className="text-2xl font-semibold text-charcoal">
                    {player.overallStatistics?.challengesAnswered ?? '—'}
                  </p>
                </div>
              </div>

              {player.overallStatistics?.skills?.length ? (
                <div className="mt-6">
                  <h2 className="text-xl font-semibold text-charcoal mb-3">Skills</h2>
                  <div className="space-y-3">
                    {player.overallStatistics.skills.map(s => (
                      <div key={s.name}>
                        <div className="flex justify-between text-sm text-charcoal mb-1">
                          <span className="font-medium">{s.name}</span>
                          <span>
                            {s.percentage}%{' '}
                            {typeof s.percentageChange === 'number' && (
                              <span className={s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrantCoral'}>
                                ({s.percentageChange >= 0 ? '+' : ''}{s.percentageChange})
                              </span>
                            )}
                          </span>
                        </div>
                        <div className="w-full bg-gray-100 rounded h-2">
                          <div
                            className="h-2 rounded bg-vibrantCoral"
                            style={{ width: `${Math.max(0, Math.min(100, s.percentage))}%` }}
                          />
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              ) : null}
            </>
          )}
        </div>
      </div>
    </BackgroundWrapper>
  );
};

export default PlayerProfilePage;