"use client";

import React from 'react';
import { useParams } from 'next/navigation';
import { challenges } from '../../../mockData/challenges';

const ChallengeDetail = () => {
    const { id } = useParams();

    const challenge = challenges.find(ch => ch.id === Number(id));

    return (
        <div className="flex flex-col items-center p-4">
            {challenge ? (
                <div className="max-w-2xl w-full bg-white shadow-md rounded-lg p-6">
                    <div className="h-48 bg-gray-300 rounded-lg mb-4"></div>

                    <h1 className="text-2xl font-bold text-gray-800">{challenge.title}</h1>
                    <p className="text-gray-600 mt-2">{challenge.description}</p>
                    <p className="text-lg font-semibold mt-4">
                        Duration: {challenge.isActive ? `${challenge.duration} hours` : 'Expired'}
                    </p>
                </div>
            ) : (
                <p>Loading...</p>
            )}
        </div>
    );
};

export default ChallengeDetail;