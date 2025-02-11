'use client';
import React from 'react';
import TimeLabel from '../timeLabel/TimeLabel';
import { useRouter } from 'next/navigation';

interface ChallengeCardProps {
    id: string;
    title: string;
    description: string;
    duration: number;
    isCompleted: boolean;
    isTimeUp?: boolean;
}

const ChallengeCard: React.FC<ChallengeCardProps> = ({ id, title, description, duration, isCompleted, isTimeUp }) => {
    const router = useRouter();

    const handleClick = () => {
        router.push(`/challenges/${id}`);
    };

    return (
        <div>
            <hr className="border-0 h-[1px] bg-black"/>
            <div
                className={`group rounded px-2 py-2 cursor-pointer transition-bg duration-[120ms] hover:bg-vibrantCoral flex flex-row gap-6  mt-3 mx-2 w-full`}
                onClick={handleClick}
            >
                <div className='flex flex-col gap-4 w-fit'>
                    <div className="bg-slate-200 flex items-center justify-center text-gray-700 w-full sm:w-[200px] h-[150px]">
                        img
                    </div>
                    <TimeLabel duration={duration} isCompleted={isCompleted} />
                </div>
                <div className='flex flex-col'>
                    <h3 className="font-sourceSans3 text-2xl font-bold text-vibrantCoral group-hover:text-white">
                        {title}
                    </h3>
                    <p className='font-sourceSans3 text-md font-normal text-black group-hover:text-white'>
                        {description}
                    </p>
                </div>
            </div>
        </div>
    )
};

export default ChallengeCard;