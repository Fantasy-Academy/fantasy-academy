'use client';
import React from 'react';
import TimeLabel from '../common/timeLabel/TimeLabel';
import { useRouter } from 'next/navigation';

interface ChallengeCardProps {
    id: string;
    title: string;
    duration: number;
    isCompleted: boolean;
}

const ChallengeCard: React.FC<ChallengeCardProps> = ({ id, title, duration, isCompleted }) => {
    const router = useRouter();

    const handleClick = () => {
        router.push(`/challenges/${id}`);
    };

    return (
        <div
            className="bg-blueBlack rounded-xl shadow-md cursor-pointer p-4 sm:p-6 w-full flex flex-col sm:flex-row gap-4"
            onClick={handleClick}
        >
            <div className="bg-slate-200 rounded aspect-video flex items-center justify-center text-gray-700 h-40 sm:h-48">
                img
            </div>
            <div className="flex flex-col w-full text-left justify-between">
                <h3 className="text-xl sm:text-3xl font-semibold text-white font-alexandria">
                    {title}
                </h3>
                <div className="flex justify-between items-center mt-4 sm:mt-2">
                    <div></div> {/* Prázdný blok pro vyrovnání */}
                    <TimeLabel duration={duration} isCompleted={isCompleted} />
                </div>
            </div>
        </div>
    );
};

export default ChallengeCard;