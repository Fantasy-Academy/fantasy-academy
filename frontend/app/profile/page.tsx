'use client';
import React from 'react';
import Btn from '../../components/common/Btn';
import { useSession, signOut } from 'next-auth/react';
import Achievement from '../../components/common/Achievement';
import TitleContainer from '../../components/containers/TitleContainer';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';

const UserProfile: React.FC = () => {
    const { data: session } = useSession();

    const achievements = [
        { title: 'Achievement 1', description: 'Description of achievement 1.' },
        { title: 'Achievement 2', description: 'Description of achievement 2.' },
        { title: 'Achievement 3', description: 'Description of achievement 3.' },
        { title: 'Achievement 4', description: 'Description of achievement 4.' },
        { title: 'Achievement 5', description: 'Description of achievement 5.' },
    ];
    const skills = [
        { title: 'Skill 1', description: 'Description of skill 1.' },
        { title: 'Skill 2', description: 'Description of skill 2.' },
        { title: 'Skill 3', description: 'Description of skill 3.' },
        { title: 'Skill 4', description: 'Description of skill 4.' },
        { title: 'Skill 5', description: 'Description of skill 5.' },
    ];

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