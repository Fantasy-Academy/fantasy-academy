'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';
import { useRouter } from 'next/navigation';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import Head from "next/head";


/** --- Pomocný progress bar: postup k dalšímu tieru podle bodů --- */
const TierProgressBar: React.FC<{ points: number }> = ({ points }) => {
  const tiers = [
    { name: 'Bronze', min: 0, max: 299 },
    { name: 'Silver', min: 300, max: 599 },
    { name: 'Gold', min: 600, max: 999 },
    { name: 'Platinum', min: 1000, max: Infinity },
  ];

  const { current, next } = useMemo(() => {
    const t = tiers.findLast(t => points >= t.min) ?? tiers[0];
    const idx = tiers.findIndex(x => x.name === t.name);
    const nextT = tiers[idx + 1] ?? tiers[idx];
    return { current: t, next: nextT };
  }, [points]);

  const bandSize = current.max === Infinity ? 1 : (current.max - current.min + 1);
  const inBand = current.max === Infinity ? 1 : Math.max(0, Math.min(points - current.min, bandSize));
  const pct = current.max === Infinity ? 100 : Math.round((inBand / bandSize) * 100);

  return (
    <div className="w-full">
      <Head>
        <title>Dashboard | Fantasy Academy</title>
      </Head>
      <div className="flex items-end justify-between mb-1">
        <div className="text-sm text-coolGray">
          Tier: <span className="text-charcoal font-semibold">{current.name}</span>
        </div>
        <div className="text-sm text-coolGray">
          {current.name !== 'Platinum' ? (
            <>
              Next: <span className="text-charcoal font-semibold">{next.name}</span> •{' '}
              <span className="text-charcoal font-semibold">{Math.max(0, next.min - points)} FAP</span> to go
            </>
          ) : (
            <span className="text-charcoal font-semibold">Max Tier</span>
          )}
        </div>
      </div>
      <div className="h-2 w-full rounded bg-gray-200 overflow-hidden">
        <div
          className="h-full bg-vibrantCoral transition-[width] duration-500 ease-out"
          style={{ width: `${pct}%` }}
          role="progressbar"
          aria-valuenow={pct}
          aria-valuemin={0}
          aria-valuemax={100}
        />
      </div>
    </div>
  );
};

/** --- Typy podle API --- */
type PlayerSkill = { name: string; percentage: number; percentageChange: number | null };
type PlayerStatistics = { rank: number; challengesAnswered: number; points: number; skills: PlayerSkill[] };
type LoggedUserInfo = {
  id: string;
  name: string;
  email: string;
  availableChallenges: number;
  completedChallenges: number;
  registeredAt: string;
  overallStatistics: PlayerStatistics;
};

type ChallengeItem = {
  id: string;
  isAnswered: boolean;
  isExpired?: boolean;
  expiresAt: string;
};

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL;

export default function Dashboard() {
  const { data: session, status } = useSession();
  const router = useRouter();
  const accessToken = (session as any)?.accessToken as string | undefined;

  const [me, setMe] = useState<LoggedUserInfo | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [counts, setCounts] = useState({ available: 0, completed: 0, expired: 0 });

  useEffect(() => {
    if (status === 'unauthenticated') router.push('/login');
  }, [status, router]);

  // Načtení profilu
  useEffect(() => {
    const loadMe = async () => {
      if (!accessToken || status !== 'authenticated') return;
      setLoading(true);
      setError(null);
      try {
        const res = await fetch(`${API_BASE}/api/me`, {
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${accessToken}` },
        });
        if (!res.ok) {
          const err = await res.json().catch(() => ({}));
          throw new Error(err?.detail || `Failed to load /api/me (${res.status})`);
        }
        const data = (await res.json()) as LoggedUserInfo;
        setMe(data);
      } catch (e: any) {
        setError(e?.message || 'Nepodařilo se načíst profil.');
      } finally {
        setLoading(false);
      }
    };
    loadMe();
  }, [accessToken, status]);

  // Načtení challenge pro počítadla
  useEffect(() => {
    const loadCounts = async () => {
      if (!accessToken || status !== 'authenticated') return;
      try {
        const res = await fetch(`${API_BASE}/api/challenges`, {
          headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${accessToken}` },
        });
        if (!res.ok) return;

        const payload = await res.json();
        const list: ChallengeItem[] = Array.isArray(payload) ? payload : payload.member ?? [];
        const now = Date.now();

        let available = 0, completed = 0, expired = 0;
        for (const ch of list) {
          const isCompleted = !!ch.isAnswered;
          const isExpired = typeof ch.isExpired === 'boolean'
            ? ch.isExpired
            : new Date(ch.expiresAt).getTime() <= now;

          if (isCompleted) completed++;
          else if (isExpired) expired++;
          else available++;
        }

        setCounts({ available, completed, expired });
      } catch {
        // ignorujeme chybu
      }
    };
    loadCounts();
  }, [accessToken, status]);

  const Skeleton = () => (
    <div className="animate-pulse space-y-4">
      <div className="h-6 bg-gray-200 rounded w-1/3" />
      <div className="h-10 bg-gray-200 rounded w-2/3" />
      <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div className="h-20 bg-gray-100 rounded" />
        <div className="h-20 bg-gray-100 rounded" />
        <div className="h-20 bg-gray-100 rounded" />
        <div className="h-20 bg-gray-100 rounded" />
      </div>
      <div className="h-2 bg-gray-200 rounded w-full" />
    </div>
  );

  return (
    <BackgroundWrapper>
      <div className="flex items-center justify-center min-h-[calc(100vh-80px)] px-4">
        <div className="flex flex-col w-full max-w-[1000px] mx-auto py-8 gap-6">
          <section className="bg-white rounded-xl shadow-md p-6 sm:p-8">
            {status === 'loading' && <Skeleton />}
            {status === 'authenticated' && loading && <Skeleton />}
            {status === 'authenticated' && error && <p className="text-vibrantCoral">{error}</p>}

            {status === 'authenticated' && !loading && !error && (
              <div className="flex flex-col gap-6">
                {/* Header */}
                <div className="flex items-center gap-4">
                  <div className="w-20 h-20 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                    <img src="" alt="profile picture" className="w-full h-full object-cover" />
                  </div>
                  <div className="min-w-0 flex-1">
                    <h1 className="text-3xl sm:text-5xl text-vibrantCoral font-bold tracking-wider uppercase font-bebasNeue leading-tight truncate">
                      {me?.name ?? session?.user?.name ?? 'User'}
                    </h1>
                    <p className="text-coolGray">
                      Member since:{' '}
                      <span className="text-charcoal font-semibold">
                        {me?.registeredAt ? new Date(me.registeredAt).toLocaleDateString() : '—'}
                      </span>
                    </p>
                  </div>
                </div>

                {/* Stat karty */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                  <div className="bg-gray-50 rounded-lg p-4">
                    <p className="text-sm text-coolGray">Global Rank</p>
                    <p className="text-2xl text-charcoal font-bold mt-1">
                      #{me?.overallStatistics?.rank ?? '—'}
                    </p>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-4">
                    <p className="text-sm text-coolGray">Points</p>
                    <p className="text-2xl text-charcoal font-bold mt-1">
                      {me?.overallStatistics?.points ?? '—'}
                    </p>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-4">
                    <p className="text-sm text-coolGray">Rounds Played</p>
                    <p className="text-2xl text-charcoal font-bold mt-1">
                      {me?.overallStatistics?.challengesAnswered ?? '—'}
                    </p>
                  </div>
                  <div className="bg-gray-50 rounded-lg p-4">
                    <p className="text-sm text-coolGray">Completed</p>
                    <p className="text-2xl text-charcoal font-bold mt-1">
                      {me?.completedChallenges ?? '—'}
                    </p>
                  </div>
                </div>

                {/* Progress k dalšímu tieru */}
                {typeof me?.overallStatistics?.points === 'number' && (
                  <div className="mt-2">
                    <TierProgressBar points={me.overallStatistics.points} />
                  </div>
                )}

                {/* Informace o challengích */}
                <div className="mt-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                  <div className="bg-white border rounded-lg p-4">
                    <p className="text-sm text-coolGray">Available Challenges</p>
                    <p className="text-xl text-charcoal font-semibold">{counts.available}</p>
                    {counts.expired > 0 && (
                      <p className="text-xs text-coolGray mt-1">Expired (unanswered): {counts.expired}</p>
                    )}
                  </div>
                  <div className="bg-white border rounded-lg p-4">
                    <p className="text-sm text-coolGray">Completed Challenges</p>
                    <p className="text-xl text-charcoal font-semibold">{counts.completed}</p>
                  </div>
                  <div className="bg-white border rounded-lg p-4">
                    <p className="text-sm text-coolGray">Your Email</p>
                    <p className="text-xl text-charcoal font-semibold truncate">
                      {me?.email ?? session?.user?.email ?? '—'}
                    </p>
                  </div>
                </div>
              </div>
            )}
          </section>
        </div>
      </div>
    </BackgroundWrapper>
  );
}
