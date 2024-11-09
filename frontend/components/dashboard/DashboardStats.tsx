import React from 'react';

interface DashboardStatsProps {
    season: number;
    points: number;
    rank: number;
    challenges: { total: number; completed: number };
}

const DashboardStats: React.FC<DashboardStatsProps> = ({ season, points, rank, challenges }) => {
    return (
        <div className="container flex flex-col items-start bg-slate-100 px-8 py-4 shadow-lg mx-2 my-4 max-w-80 sm:mx-0 text-left">
            <p><span className="font-bold">Season:</span> {season}</p>
            <p><span className="font-bold">Points:</span> {points}</p>
            <p><span className="font-bold">Rank:</span> {rank}</p>
            <p><span className="font-bold">Current Challenges:</span> {challenges.total} <span className="font-light text-gray-600">({challenges.completed} completed)</span></p>
        </div>
    );
};

export default DashboardStats;