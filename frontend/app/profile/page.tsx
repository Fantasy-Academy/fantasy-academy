import React from 'react';
import ProfileSection from '../../components/profile/ProfileSection';

const UserProfile: React.FC = () => {
    const userData = {
        nickname: "Joe Doe",
        skills: ["Skill 1", "Skill 2", "Skill 3"],
        rank: "Gold",
        achievements: ["Completed 50 challenges", "Achieved a perfect score"],
        points: 1500,
        challenges: ["Daily Login", "Weekly Coding Challenge"],
    };

    return (
        <div className="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">{userData.nickname}</h2>
            <ProfileSection title="Rank" items={[userData.rank]} />
            <ProfileSection title="Skills" items={userData.skills} />
            <ProfileSection title="Achievements" items={userData.achievements} />
            <ProfileSection title="Points" items={[userData.points.toString()]} />
            <ProfileSection title="Challenges" items={userData.challenges} />
        </div>
    );
};

export default UserProfile;