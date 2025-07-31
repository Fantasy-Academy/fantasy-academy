import React from 'react';

// Mock data for users
const mockUsers = [
    { nickname: 'Joe Doe', rank: 'Gold', roundsPlayed: 25, overallPlacement: 5 },
    { nickname: 'Jane Smith', rank: 'Silver', roundsPlayed: 30, overallPlacement: 10 },
    { nickname: 'Tom Brown', rank: 'Platinum', roundsPlayed: 20, overallPlacement: 3 },
    { nickname: 'Alice Green', rank: 'Gold', roundsPlayed: 15, overallPlacement: 8 },
    { nickname: 'Bob White', rank: 'Bronze', roundsPlayed: 10, overallPlacement: 20 },
];

const UserTable: React.FC = () => {
    return (
        <div className="px-4 py-8">
            <div className="overflow-x-auto bg-white shadow-md rounded">
                <div className="min-w-full">
                    <div className="flex justify-between p-4 bg-gray-800 text-white">
                        <div className="flex-1 text-left">Nickname</div>
                        <div className="flex-1 text-left">Rank</div>
                        <div className="flex-1 text-left">Rounds Played</div>
                        <div className="flex-1 text-left">Overall Placement</div>
                    </div>
                    {mockUsers.map((user, index) => (
                        <div 
                            key={index} 
                            className={`flex justify-between p-4 border-b ${index % 2 === 0 ? 'bg-gray-100' : 'bg-white'}`}
                        >
                            <div className="flex-1">{user.nickname}</div>
                            <div className="flex-1">{user.rank}</div>
                            <div className="flex-1">{user.roundsPlayed}</div>
                            <div className="flex-1">{user.overallPlacement}</div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default UserTable;