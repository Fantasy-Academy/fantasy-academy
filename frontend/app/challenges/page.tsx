'use client';
import React, { useState } from 'react';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import { challenges as challengesData } from '../../data/challenges';

const Challenges = () => {
    const [tab, setTab] = useState<'current' | 'completed' | "time's up">('current');

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

    return (
        <BackgroundWrapper>
            <div className="min-h-screen py-8">
                {/* Sticky navigační lišta */}
                <div className="w-fit mb-8 mx-auto px-8 py-4">
                    {/* Vnitřní wrapper, který zajistí vycentrování obsahu */}
                    <div className="max-w-[1200px] mx-auto flex justify-center gap-16 text-lg">
                        <button
                            className={`py-2 px-2 transition-colors border-b-4 ${
                                tab === 'current'
                                    ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                    : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                            }`}
                            onClick={() => setTab('current')}
                        >
                            Current
                        </button>
                        <button
                            className={`py-2 transition-colors border-b-4 ${
                                tab === 'completed'
                                    ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                    : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                            }`}
                            onClick={() => setTab('completed')}
                        >
                            Completed
                        </button>
                        <button
                            className={`py-2 transition-colors border-b-4 ${
                                tab === "time's up"
                                    ? 'text-vibrantCoral font-bold border-vibrantCoral'
                                    : 'text-charcoal font-bold hover:text-vibrantCoral border-transparent'
                            }`}
                            onClick={() => setTab("time's up")}
                        >
                            Time&apos;s up
                        </button>
                    </div>
                </div>

                {/* Obsah s challenge kartami */}
                <div className="flex flex-col items-center">
                    <div className="w-full max-w-[1200px] mx-auto">
                        <div className="flex flex-col gap-4">
                            {filteredChallenges.map((challenge) => (
                                <ChallengeCard key={challenge.id} {...challenge} />
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </BackgroundWrapper>
        </BackgroundWrapper>
    );
};

export default Challenges;