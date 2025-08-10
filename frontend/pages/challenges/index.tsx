'use client';

import React, { useEffect, useState, useCallback } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { useSession } from 'next-auth/react';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import ChallengeModal from '../../components/modal/ChallangeModal';

type Challenge = {
    id: string;
    name: string;
    shortDescription: string;
    description: string;
    image: string | null;
    addedAt: string;
    startsAt: string;
    expiresAt: string;
    answeredAt: string | null;
    isStarted: boolean;
    isExpired: boolean;
    isAnswered: boolean;
    isEvaluated: boolean;
};

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL;

const Challenges = () => {
    const [tab, setTab] = useState<'current' | 'completed' | "time's up">('current');
    const [challenges, setChallenges] = useState<Challenge[]>([]);
    const router = useRouter();
    const searchParams = useSearchParams();
    const challengeId = searchParams.get('id');

    const { data: session, status } = useSession();
    const token = (session as any)?.accessToken as string | undefined;

    const fetchChallenges = useCallback(async () => {
        if (!token) return;
        try {
            const res = await fetch(`${API_BASE}/api/challenges`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${token}`,
                    'Cache-Control': 'no-cache',
                },
                cache: 'no-store',
            });
            if (!res.ok) throw new Error('Chyba při načítání výzev');
            const data = await res.json();
            setChallenges(data.member || data);
        } catch (err) {
            console.error(err);
        }
    }, [token]);

    useEffect(() => {
        if (status === 'authenticated') {
            fetchChallenges();
        }
    }, [status, fetchChallenges]);

    const filteredChallenges = challenges.filter((challenge) => {
        const isCompleted = challenge.isAnswered;
        const duration = new Date(challenge.expiresAt).getTime() - Date.now();
        const durationInMinutes = Math.max(Math.floor(duration / 60000), 0);

        if (tab === 'current') return durationInMinutes > 0 && !isCompleted;
        if (tab === 'completed') return isCompleted;
        if (tab === "time's up") return durationInMinutes === 0 && !isCompleted;
        return false;
    });

    const selectedChallenge = challenges.find((c) => c.id === challengeId) || null;

    const openModal = (id: string) => {
        router.push(`/challenges?id=${id}`, { scroll: false });
    };

    const closeModal = () => {
        router.replace('/challenges', { scroll: false });
    };

    const handleSubmitSuccess = async () => {
        if (challengeId) {
            setChallenges((prev) =>
                prev.map((c) =>
                    c.id === challengeId ? { ...c, isAnswered: true, answeredAt: new Date().toISOString() } : c
                )
            );
        }
        await fetchChallenges();
        closeModal();
    };

    function formatDuration(ms: number) {
        if (ms <= 0) return 'Expired';

        const totalMinutes = Math.floor(ms / 60000);
        const days = Math.floor(totalMinutes / (60 * 24));
        const hours = Math.floor((totalMinutes % (60 * 24)) / 60);
        const minutes = totalMinutes % 60;

        const parts = [];
        if (days > 0) parts.push(`${days}d`);
        if (hours > 0) parts.push(`${hours}h`);
        if (minutes > 0) parts.push(`${minutes}m`);

        return parts.join(' ');
    }

    return (
        <BackgroundWrapper>
            <div className="min-h-screen py-8">
                {/* Tab Navigation */}
                <div className="w-fit mb-8 mx-auto px-8 py-4">
                    <div className="max-w-[1200px] mx-auto flex justify-center gap-12 text-lg">
                        {['current', 'completed', "time's up"].map((type) => (
                            <button
                                key={type}
                                className={`py-2 px-4 transition-colors border-b-4 ${tab === type
                                        ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                        : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                                    }`}
                                onClick={() => setTab(type as typeof tab)}
                            >
                                {type === "time's up" ? "Time's up" : type.charAt(0).toUpperCase() + type.slice(1)}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Challenge List */}
                <div className="flex flex-col items-center">
                    <div className="w-full max-w-[1200px] mx-auto">
                        <div className="flex flex-col gap-4">
                            {filteredChallenges.map((challenge) => (
                                <div key={challenge.id} onClick={() => openModal(challenge.id)}>
                                    <ChallengeCard
                                        challengeCard={{
                                            id: challenge.id,
                                            title: challenge.name,
                                            shortDescription: challenge.shortDescription,
                                            description: challenge.description,
                                            image: challenge.image ?? '',
                                            duration: Math.max(
                                                Math.floor((new Date(challenge.expiresAt).getTime() - Date.now()) / 60000),
                                                0
                                            ),
                                            isCompleted: challenge.isAnswered,
                                        }}
                                    />
                                </div>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Modal */}
                {selectedChallenge && challengeId && (
                    <ChallengeModal
                        challengeId={challengeId}
                        onClose={closeModal}
                        onSubmitSuccess={handleSubmitSuccess}
                        apiBase={API_BASE}
                    />
                )}
            </div>
        </BackgroundWrapper>
    );
};

export default Challenges;