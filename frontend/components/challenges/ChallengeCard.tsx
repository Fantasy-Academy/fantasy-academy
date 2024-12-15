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
        router.push(`/challenges/${id}`); // Přesměrování na stránku výzvy
    };

    return (
        <div
            className="bg-blueBlack rounded-xl shadow-md cursor-pointer p-2.5 w-full max-w-[300px]"
            onClick={handleClick}
        >
            <div className="flex flex-col gap-2.5">
                <div className="bg-slate-200 rounded aspect-video flex items-center justify-center text-gray-700">
                    img
                </div>
                <h3 className="font-semibold text-white text-center font-alexandria text-base">
                    {title}
                </h3>
                <TimeLabel duration={duration} isCompleted={isCompleted} />
            </div>
        </div>
    );
};

export default ChallengeCard;