'use client';
import React from 'react';
import { useParams } from 'next/navigation';
import TimeLabel from '../../../components/common/timeLabel/TimeLabel';
import BackgroundWrapper from '../../../layouts/BackgroundWrapper';
import { challenges } from '../../../data/challenges';

const ChallengeDetail = () => {
    const { id } = useParams();
    const challenge = challenges.find((challenge) => challenge.id === id);

    if (!challenge) {
        return (
            <BackgroundWrapper>
                <div className="flex items-center justify-center min-h-screen text-white">
                    <p>Challenge not found.</p>
                </div>
            </BackgroundWrapper>
        );
    }

    return (
        <BackgroundWrapper>
            <div className="flex items-center justify-center min-h-screen px-4">
                <div className="text-white flex flex-col gap-4 p-4 bg-[#363636]/50 backdrop-blur-md rounded-3xl shadow-lg max-w-[1000px] w-full mx-auto">
                    {/* IMG Row */}
                    <div className="flex flex-col md:flex-row bg-blueBlack rounded-xl shadow-md p-4 gap-4">
                        <div
                            className="bg-slate-200 rounded flex items-center justify-center text-gray-700 w-full md:w-[450px] h-[225px]"
                        >
                            IMG
                        </div>
                        <div
                            className="flex flex-col w-full"
                            style={{ maxWidth: '472px' }}
                        >
                            <h3 className="font-bold text-3xl font-alexandria">{challenge.title}</h3>
                            <p className="mt-2 text-sm md:text-base">{challenge.description}</p>
                        </div>
                    </div>

                    {/* Guide Row */}
                    <div className="flex flex-col gap-1">
                        <h3 className="font-bold text-3xl font-alexandria">Guide</h3>
                        <p className="text-sm md:text-base">{challenge.guide}</p>
                    </div>

                    {/* Time Label */}
                    <div className="flex justify-end">
                        <div className="inline-flex">
                            <TimeLabel duration={challenge.duration} isCompleted={challenge.isCompleted} />
                        </div>
                    </div>
                </div>
                <div className='flex flex-col'>

                </div>
            </div>
        </BackgroundWrapper>
    );
};

export default ChallengeDetail;