'use client';
import React from 'react';
import ProfileSection from '../../components/profile/ProfileSection';
import { useSession, signOut, getSession } from 'next-auth/react';
import Btn from '../../components/common/Btn';

const UserProfile: React.FC = () => {
    const { data: session } = useSession();
    console.log(JSON.stringify(session));

    const userData = {
        skills: ["Skill 1", "Skill 2", "Skill 3"],
        rank: "Gold",
        achievements: ["Completed 50 challenges", "Achieved a perfect score"],
        points: 1500,
        challenges: ["Daily Login", "Weekly Coding Challenge"],
    };

    return (
        <div className="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow-md">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">{session?.user?.name}</h2>
            <ProfileSection title="Rank" items={[userData.rank]} />
            <ProfileSection title="Skills" items={userData.skills} />
            <ProfileSection title="Achievements" items={userData.achievements} />
            <ProfileSection title="Points" items={[userData.points.toString()]} />
            <ProfileSection title="Challenges" items={userData.challenges} />
            <Btn
                type="button"
                text="Logout"
                onClick={() => signOut({ callbackUrl: 'http://localhost:3000/login' })}
            />
        </div>
    );
};

export default UserProfile;