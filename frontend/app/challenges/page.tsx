'use client';
import React, { useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import Link from 'next/link';
import { challenges } from '../../data/challenges';

const Challenges = () => {
    const [tab, setTab] = useState<'current' | 'completed' | "time's up">('current');
    const router = useRouter();
    const searchParams = useSearchParams();
    const challengeId = searchParams.get('id');

    const filteredChallenges = challengesData.filter((challenge) => {
        if (tab === 'current') {
            return challenge.duration > 0 && !challenge.isCompleted;
        }
        if (tab === 'completed') {
            return challenge.isCompleted;
        }
        if (tab === "time's up") {
            return challenge.duration === 0 && !challenge.isCompleted;
        }
        return false;
    });

    // Najdeme challenge podle ID z URL
    const selectedChallenge = challengesData.find((challenge) => challenge.id === challengeId);

    // Zavření modalu a reset URL
    const closeModal = () => {
        router.push('/challenges', { scroll: false });
    };

    return (
        <BackgroundWrapper>
            <div className="min-h-screen py-8">
                {/* Sticky navigační lišta */}
                <div className="w-fit mb-8 mx-auto px-8 py-4">
                    <div className="max-w-[1200px] mx-auto flex justify-center gap-12 text-lg">
                        <button
                            className={`py-2 px-4 transition-colors border-b-4 ${tab === 'current'
                                ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                                }`}
                            onClick={() => setTab('current')}
                        >
                            Current
                        </button>
                        <button
                            className={`py-2 px-4 transition-colors border-b-4 ${tab === 'completed'
                                ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                                }`}
                            onClick={() => setTab('completed')}
                        >
                            Completed
                        </button>
                        <button
                            className={`py-2 px-4 transition-colors border-b-4 ${tab === "time's up"
                                ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                                }`}
                            onClick={() => setTab("time's up")}
                        >
                            Time&apos;s up
                        </button>
                    </div>
                </div>

                <div className="flex flex-col items-center">
                    <div className="w-full max-w-[1200px] mx-auto">
                        <div className="flex flex-col gap-4">
                            {filteredChallenges.map((challenge) => (
                                <ChallengeCard key={challenge.id} challengeCard={challenge} />
                            ))}
                        </div>
                    </div>
                </div>

                {selectedChallenge && (
                    <ChallengeModal
                        title={selectedChallenge.title}
                        description={selectedChallenge.description}
                        duration={selectedChallenge.duration}
                        isCompleted={selectedChallenge.isCompleted}
                        onClose={closeModal}
                    />
                )}
            </div>
        </BackgroundWrapper>
    );
};

export default Challenges;