'use client';
import React, { useState } from 'react';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import { challenges as challengesData } from '../../data/challenges';

const Challenges = () => {
    const [tab, setTab] = useState<'current' | 'completed' | 'expired'>('current');

    const filteredChallenges = challengesData.filter((challenge) => {
        if (tab === 'current') {
            return challenge.duration > 0 && !challenge.isCompleted;
        }
        if (tab === 'completed') {
            return challenge.isCompleted;
        }
        if (tab === 'expired') {
            return challenge.duration === 0 && !challenge.isCompleted;
        }
        return false;
    });

    return (
        <BackgroundWrapper>
            <div className="flex items-start min-h-screen">
                <div className="flex flex-col items-center p-4 w-full max-w-[860px] mx-auto">

                    <h1 className="text-4xl font-bold text-white font-alexandria mb-4 w-full text-left">
                        CHALLENGES
                    </h1>

                    <div className="w-full">
                        {/* Tabs (Current, Completed, Expired) */}
                        <div className="flex justify-start mb-4 bg-[#363636]/50 backdrop-blur-md rounded-3xl p-1">
                            <div
                                className={`text-white cursor-pointer px-4 py-2 ${tab === 'current' ? 'font-semibold underline' : 'font-light'}`}
                                onClick={() => setTab('current')}
                            >
                                Current
                            </div>
                            <div
                                className={`text-white cursor-pointer px-4 py-2 ${tab === 'completed' ? 'font-semibold underline' : 'font-light'}`}
                                onClick={() => setTab('completed')}
                            >
                                Completed
                            </div>
                            <div
                                className={`text-white cursor-pointer px-4 py-2 ${tab === 'expired' ? 'font-semibold underline' : 'font-light'}`}
                                onClick={() => setTab('expired')}
                            >
                                Expired
                            </div>
                        </div>

                        {/* Challenge Cards */}
                        <div className="p-4 w-full bg-[#363636]/50 backdrop-blur-md rounded-3xl shadow-lg rounded-tl-3xl">
                            {filteredChallenges.length > 0 ? (
                                <div className="flex flex-col sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                    {filteredChallenges.map((challenge) => (
                                        <ChallengeCard
                                            key={challenge.id}
                                            id={challenge.id}  
                                            title={challenge.title}
                                            duration={challenge.duration}
                                            isCompleted={challenge.isCompleted}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <p className="text-center text-white font-light">No challenges found.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </BackgroundWrapper>
        </BackgroundWrapper>
    );
};

export default Challenges;