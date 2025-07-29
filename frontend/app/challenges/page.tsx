'use client';
import React, { useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import ChallengeModal from '../../components/modal/ChallangeModal';

type Challenge = {
    id: string;
    name: string;
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

const Challenges = () => {
    const [tab, setTab] = useState<'current' | 'completed' | "time's up">('current');
    const [challenges, setChallenges] = useState<Challenge[]>([]);
    const router = useRouter();
    const searchParams = useSearchParams();
    const challengeId = searchParams.get('id');

    useEffect(() => {
        const fetchChallenges = async () => {
            try {
                const res = await fetch('http://localhost:8000/api/challenges', {
                    headers: {
                        Authorization: `Bearer ${localStorage.getItem('token')}`, // pokud vyžaduješ autentizaci
                    },
                });

                if (!res.ok) throw new Error('Chyba při načítání výzev');
                const data = await res.json();

                // API vrací `member` pokud je to Hydra (v opačném případě přímo pole)
                setChallenges(data.member || data);
            } catch (err) {
                console.error(err);
            }
        };

        fetchChallenges();
    }, []);

    const filteredChallenges = challenges.filter((challenge) => {
        const isCompleted = challenge.isAnswered;
        const duration = new Date(challenge.expiresAt).getTime() - Date.now();
        const durationInMinutes = Math.max(Math.floor(duration / 60000), 0);

        if (tab === 'current') {
            return durationInMinutes > 0 && !isCompleted;
        }
        if (tab === 'completed') {
            return isCompleted;
        }
        if (tab === "time's up") {
            return durationInMinutes === 0 && !isCompleted;
        }
        return false;
    });

    const selectedChallenge = challenges.find((challenge) => challenge.id === challengeId);

    const closeModal = () => {
        router.push('/challenges', { scroll: false });
    };

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
                                <ChallengeCard key={challenge.id} challengeCard={{
                                    id: challenge.id,
                                    title: challenge.name,
                                    description: challenge.description,
                                    image: challenge.image ?? '',
                                    duration: Math.max(Math.floor((new Date(challenge.expiresAt).getTime() - Date.now()) / 60000), 0),
                                    isCompleted: challenge.isAnswered,
                                }} />
                            ))}
                        </div>
                    </div>
                </div>

                {/* Modal */}
                {selectedChallenge && (
                    <ChallengeModal
                        title={selectedChallenge.name}
                        description={selectedChallenge.description}
                        duration={Math.max(Math.floor((new Date(selectedChallenge.expiresAt).getTime() - Date.now()) / 60000), 0)}
                        isCompleted={selectedChallenge.isAnswered}
                        onClose={closeModal}
                    />
                )}
            </div>
        </BackgroundWrapper>
    );
};

export default Challenges;