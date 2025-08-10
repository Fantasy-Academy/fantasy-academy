'use client';

import React, { useEffect, useMemo, useState } from 'react';
import { useSession } from 'next-auth/react';
import Achievement from '../../components/common/Achievement';
import TitleContainer from '../../components/containers/TitleContainer';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import TierProgressBar from '../../components/common/TierProgressBar';

type PlayerSkill = { name: string; percentage: number; percentageChange: number | null };
type PlayerStatistics = { rank: number; challengesAnswered: number; points: number; skills: PlayerSkill[] };
type LoggedUserInfo = {
    id: string; name: string; email: string;
    availableChallenges: number; completedChallenges: number;
    registeredAt: string; overallStatistics: PlayerStatistics;
};

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL || 'http://localhost:8080';

const UserProfile: React.FC = () => {
    const { data: session, status } = useSession();
    const accessToken = (session as any)?.accessToken as string | undefined;

    const [me, setMe] = useState<LoggedUserInfo | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        const load = async () => {
            if (!accessToken) return;
            setLoading(true); setError(null);
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
        if (status !== 'loading') load();
    }, [accessToken, status]);

    const achievements = useMemo(
        () => [
            { title: 'Achievement 1', description: 'Description of achievement 1.' },
            { title: 'Achievement 2', description: 'Description of achievement 2.' },
            { title: 'Achievement 3', description: 'Description of achievement 3.' },
            { title: 'Achievement 4', description: 'Description of achievement 4.' },
        ],
        []
    );

    return (
        <BackgroundWrapper>
            <div className="flex flex-col gap-8 justify-center text-left pb-8 px-4 sm:px-8 max-w-5xl mx-auto mt-8">
                <div className="p-6 flex-1 bg-white rounded">
                    {!session && (
                        <h1 className="text-4xl text-charcoal font-bold tracking-wider uppercase whitespace-nowrap">
                            No user
                        </h1>
                    )}

                    {session && (
                        <>
                            {loading && <div className="text-coolGray">Načítám profil…</div>}
                            {error && <div className="text-vibrantCoral">{error}</div>}

                            <div className="flex flex-row gap-4 items-start w-full">
                                <div className="w-28 h-28 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                                    <img src="" alt="profile picture" className="w-full h-full object-cover" />
                                </div>

                                <div className="flex flex-col flex-1">
                                    <h1 className="text-5xl text-vibrantCoral font-bold tracking-wider uppercase font-bebasNeue leading-10">
                                        {me?.name ?? session.user?.name ?? 'User'}
                                    </h1>

                                    <div className="pt-2 text-xl text-coolGray grid grid-cols-2 gap-x-6 gap-y-1 w-full">
                                        <h3>
                                            Rank: <span className="text-charcoal font-semibold">#{me?.overallStatistics?.rank ?? '—'}</span>
                                        </h3>
                                        <h3>
                                            Points: <span className="text-charcoal font-semibold">{me?.overallStatistics?.points ?? '—'}</span>
                                        </h3>
                                        <h3>
                                            Rounds Played: <span className="text-charcoal font-semibold">{me?.overallStatistics?.challengesAnswered ?? '—'}</span>
                                        </h3>
                                        <h3>
                                            Completed Challenges: <span className="text-charcoal font-semibold">{me?.completedChallenges ?? '—'}</span>
                                        </h3>
                                        <h3 className="col-span-2">
                                            Member since:{' '}
                                            <span className="text-charcoal font-semibold">
                                                {me?.registeredAt ? new Date(me.registeredAt).toLocaleDateString() : '—'}
                                            </span>
                                        </h3>
                                    </div>

                                    {typeof me?.overallStatistics?.points === 'number' && (
                                        <div className="mt-4">
                                            <TierProgressBar points={me.overallStatistics.points} />
                                        </div>
                                    )}
                                </div>
                            </div>
                        </>
                    )}
                </div>

                {/* Achievements – zatím mock */}
                <div className="w-full flex flex-col gap-8">
                    <div>
                        <hr className="h-1 bg-charcoal" />
                        <TitleContainer
                            title="Your Achievements"
                            content={
                                <div className="flex flex-row flex-wrap">
                                    {achievements.map((a, i) => (
                                        <div key={i}>
                                            <Achievement title={a.title} description={a.description} />
                                        </div>
                                    ))}
                                </div>
                            }
                        />
                    </div>
                </div>
            </div>
        </BackgroundWrapper>
    );
};

export default UserProfile;