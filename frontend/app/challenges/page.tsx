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
            <div className="flex items-start min-h-screen">
                <div className="flex flex-col items-center p-4 w-full max-w-[1200px] mx-auto mt-8">
                    <div className="w-full">
                        {/* Tabs (Current, Completed, Time's Up) */}
                        <div className="relative flex justify-between  mb-2 bg-charcoal px-4 py-4 mb-8">
                            {/* Highlight for active tab */}
                            <div
                                className="absolute top-2 transform bg-white shadow-sharp h-[70%] text-charcoal font-regular font-sourceSans3 transition-all duration-300 ease-in-out"
                                style={{
                                    width: '30.33%',
                                    transform: `translateX(${tab === 'completed' ? '109%' : tab === "time's up" ? '216%' : '2%'})`,
                                }}
                            />
                            <div
                                className={`relative z-10 text-center text-charcoal font-bold font-sourceSans3 cursor-pointer px-4 py-2 flex-1 ${tab === 'current' ? 'font-semibold text-black' : 'font-light text-white'
                                    }`}
                                onClick={() => setTab('current')}
                            >
                                On The Field
                            </div>
                            <div
                                className={`relative z-10 text-center text-charcoal font-bold font-sourceSans3 cursor-pointer px-4 py-2 flex-1 ${tab === 'completed' ? 'font-semibold text-black' : 'font-light text-white'
                                    }`}
                                onClick={() => setTab('completed')}
                            >
                                Completed
                            </div>
                            <div
                                className={`relative z-10 text-center text-charcoal font-bold font-sourceSans3 cursor-pointer px-4 py-2 flex-1 ${tab === "time's up" ? 'font-semibold text-black' : 'font-light text-white'
                                    }`}
                                onClick={() => setTab("time's up")}
                            >
                                Time's Up
                            </div>
                        </div>

                        {/* Challenge Cards */}
                        <div className="p-2">
                            {filteredChallenges.length > 0 ? (
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-[4px]">
                                    {filteredChallenges.map((challenge) => (
                                        <ChallengeCard
                                            key={challenge.id}
                                            id={challenge.id}
                                            title={challenge.title}
                                            description={challenge.description}
                                            duration={challenge.duration}
                                            isCompleted={challenge.isCompleted}
                                            isTimeUp={challenge.duration === 0 && !challenge.isCompleted}
                                        />
                                    ))}
                                </div>
                            ) : (
                                <p className="text-center text-gray-700 font-light">No challenges found.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </BackgroundWrapper>
    );
};

export default Challenges;