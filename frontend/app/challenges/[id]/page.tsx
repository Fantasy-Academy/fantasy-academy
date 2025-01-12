'use client';
import React from 'react';
import { useParams } from 'next/navigation';
import TimeLabel from '../../../components/timeLabel/TimeLabel';
import BackgroundWrapper from '../../../layouts/BackgroundWrapper';
import { challenges } from '../../../data/challenges';
import ChallengeQuestBtns from '../../../components/challenges/ChallengeQuestBtns';
import ChallengeCard from '../../../components/challenges/ChallengeCard';

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
            <div className="flex items-center justify-center min-h-screen px-12">
                <div className="flex flex-col items-center justify-center gap-8 w-full max-w-[1100px]">
                    {/* Karta Challenge */}
                    <div className="w-full pb-6">
                        <ChallengeCard
                            id={challenge.id}
                            title={challenge.title}
                            description={challenge.description}
                            duration={challenge.duration}
                            isCompleted={challenge.isCompleted}
                            isTimeUp={challenge.duration === 0 && !challenge.isCompleted}
                        />
                    </div>

                    {/* Guide Row */}
                    <div className="flex flex-col text-charcoal w-full">
                        <h3 className="font-bold text-4xl font-bebasNeue">Guide</h3>
                        <p className="text-base font-nunito">{challenge.guide}</p>
                    </div>

                    {/* ChallengeQuestBtns */}
                    <div className="w-full">
                        <ChallengeQuestBtns />
                    </div>
                </div>
            </div>
        </BackgroundWrapper>
    );
};

export default ChallengeDetail;