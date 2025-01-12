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

    // Dynamické třídy na základě stavu
    const titleColor = isTimeUp
        ? 'text-white' // Pro "TIME'S UP"
        : isCompleted
            ? 'text-charcoal' // Pro dokončené výzvy
            : 'text-vibrantCoral'; // Výchozí barva
    const descriptionColor = isTimeUp
        ? 'text-white' // Pro "TIME'S UP"
        : isCompleted
            ? 'text-pistachio' // Pro dokončené výzvy
            : 'text-charcoal'; // Výchozí barva
    const bgColor = isTimeUp ? 'bg-vibrantCoral' : 'bg-white'; // Pozadí pro "TIME'S UP"

    return (
        <div
            className={`rounded px-4 py-4 cursor-pointer shadow-none transition-shadow duration-[230ms] hover:shadow-sharp flex flex-col sm:flex-row gap-4 ${bgColor} my-0.5 mx-2`}
            onClick={handleClick}
        >
            {/* Obrazová část */}
            <div
                className="bg-slate-200 flex items-center justify-center text-gray-700 w-full sm:w-[200px] h-[150px]"
            >
                img
            </div>

            {/* Textová část */}
            <div className="flex flex-col w-full justify-between">
                <div className="flex flex-col">
                    {/* Titulek */}
                    <h3 className={`text-lg sm:text-xl lg:text-2xl font-bold font-sourceSans3 ${titleColor}`}>
                        {title}
                    </h3>
                    {/* Popis */}
                    <p className={`text-sm sm:text-base font-regular font-nunito ${descriptionColor}`}>
                        {description}
                    </p>
                </div>

                {/* Časová informace */}
                <div className="flex justify-end items-center mt-4 sm:mt-2">
                    <TimeLabel duration={duration} isCompleted={isCompleted} />
                </div>
            </div>
        </div>
    );
};

export default ChallengeCard;