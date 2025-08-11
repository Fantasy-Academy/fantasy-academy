'use client';
import React from 'react';
import { useRouter } from 'next/navigation';
import TimeLabel from '../timeLabel/TimeLabel';
import type { ChallengeCard } from '../../types/challengeCard.types';
import Image from 'next/image';

interface ChallengeCardProps {
    challengeCard: ChallengeCard
}

const ChallengeCard: React.FC<ChallengeCardProps> = ({ challengeCard }) => {
    const router = useRouter();

    const openModal = () => {
        router.push(`/challenges?id=${challengeCard.id}`, { scroll: false });
    };

    return (
        <div>
            <hr className="border-0 h-[1px] bg-black" />
            <div
                className="group rounded px-2 py-2 cursor-pointer transition-bg duration-[120ms] hover:bg-coolGray flex flex-row gap-6 mt-3 mx-2 w-full"
                onClick={openModal}
            >
                <div className='flex flex-col gap-4 w-fit'>
                    <div className="bg-slate-200 flex items-center justify-center text-gray-700 w-full sm:w-[200px] h-[150px]">
                        <Image
                            src={challengeCard.image}
                            alt={challengeCard.title}
                            width={200}
                            height={150}
                            className="object-cover w-full h-full"
                        />
                    </div>
                    <TimeLabel duration={challengeCard.duration} isCompleted={challengeCard.isCompleted} />
                </div>
                <div className='flex flex-col'>
                    <h3 className="font-sourceSans3 text-2xl font-bold text-vibrantCoral group-hover:text-white">
                        {challengeCard.title}
                    </h3>
                    <div
                        className='font-sourceSans3 text-md font-normal text-black group-hover:text-white'
                        dangerouslySetInnerHTML={{ __html: challengeCard.shortDescription }}
                    />
                </div>
            </div>
        </div>
    );
};

export default ChallengeCard;
