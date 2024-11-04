import React from 'react';

interface SkillCardProps {
    label: string;
    percentage: number;
}

const SkillCard: React.FC<SkillCardProps> = ({ label, percentage }) => {
    return (
            <div className='flex flex-col mx-2 my-4'>
                <h3 className='text-xs'>{label}</h3>
                <div className='container bg-gray-800 rounded p-8 shadow-lg'>
                    <p className='text-white font-bold text-2xl'>{percentage}%</p>
                </div>
            </div>
    );
};

export default SkillCard;