import React from 'react';

interface ChallengeCardProps {
    title: string;
    description: string;
    duration: number;
    isActive: boolean;
    isCompleted: boolean; 
}

const ChallengeCard: React.FC<ChallengeCardProps> = ({ title, description, duration, isActive, isCompleted }) => {
    return (
        <div 
            className={`container w-full max-w-56 rounded text-white p-4 shadow-lg mx-2 my-4 
                ${isActive ? 'bg-slate-100' : 'bg-gray-300 opacity-75'}`}
        >
            <div className={`h-32 ${isActive ? 'bg-white' : 'bg-gray-400'} rounded`} />
            <h3 className={`font-bold text-base pt-2 ${isActive ? 'text-black' : 'text-gray-600'}`}>{title}</h3>
            <p className={`text-sm ${isActive ? 'text-slate-900' : 'text-gray-500'}`}>{description}</p>
            {isActive && (
                <div className='flex justify-end mt-2'>
                    <div className={`rounded-xl px-4 py-2 inline-flex justify-center shadow-lg ${isCompleted ? 'bg-green-500' : 'bg-gray-800'}`}>
                        <p className='text-white text-sm font-bold'>{isCompleted ? 'Completed' : `${duration} h`}</p>
                    </div>
                </div>
            )}
        </div>
    );
};

export default ChallengeCard;