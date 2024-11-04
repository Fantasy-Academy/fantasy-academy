import React from 'react';

interface DashboardStatsProps {
    season: number;
    points: number;
    rank: number;
    challenges: { total: number; completed: number };
}

const DashboardStats: React.FC<DashboardStatsProps> = ({ season, points, rank, challenges }) => {
    return (
        <div className='container mx-auto flex flex-col flex-wrap items-start bg-slate-100 p-4 shadow-lg mx-2 my-4 max-w-80'>
            <p><span className='font-bold'>Season:</span> {season}</p>
            <p><span className='font-bold'>Points:</span> {points}</p>
            <p><span className='font-bold'>Rank:</span> {rank}</p>
            <p><span className='font-bold'>Current Challenges:</span> {challenges.total} <span className='font-light text-gray-600'>({challenges.completed} completed)</span></p>
        </div>
    );
};

export default DashboardStats;