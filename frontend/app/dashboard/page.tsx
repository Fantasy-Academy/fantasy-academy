'use client';
import React from 'react';
import { useSession } from 'next-auth/react';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import TitleContainer from '../../components/containers/TitleContainer';
import Achievement from '../../components/common/Achievement';

export default function Dashboard() {
    const { data: session } = useSession();

    const skills = [
        { title: 'Skill 1', description: 'Description of skill 1.' },
        { title: 'Skill 2', description: 'Description of skill 2.' },
        { title: 'Skill 3', description: 'Description of skill 3.' },
        { title: 'Skill 4', description: 'Description of skill 4.' },
        { title: 'Skill 5', description: 'Description of skill 5.' },
    ];

    return (
        <BackgroundWrapper>
            {/* Full height and centered content */}
            <div className="flex items-center justify-center h-screen">
                {/* Card container */}
                <div className="flex flex-col w-full max-w-[860px] mx-auto bg-[#363636]/50 backdrop-blur-md rounded-3xl shadow-lg p-6">
                    {/* Header Section */}
                    <div className="flex flex-col sm:flex-row text-white mb-6">
                        <div className="p-4 flex-1">
                            {session ? (
                                <div>
                                    <h1 className="text-4xl text-white font-bold tracking-wider uppercase font-alexandria leading-10 whitespace-nowrap">
                                        Welcome back<br />
                                        <span className="capitalize text-emerald-300">{session.user?.name}</span>
                                    </h1>
                                    <div className="pt-2 text-xl">
                                        <h3>Rank #132</h3>
                                        <h3>Round Played: 24</h3>
                                    </div>
                                </div>
                            ) : (
                                <h1 className="text-4xl text-white font-bold tracking-wider uppercase whitespace-nowrap">
                                    You are not logged in
                                </h1>
                            )}
                        </div>
                        <div className="flex flex-col bg-blueBlack flex-grow p-6 rounded-2xl shadow-md text-xl">
                            <h3 className="pb-2.5">
                                <span className="font-semibold">Season:</span> 1
                            </h3>
                            <h3 className="pb-2.5">
                                <span className="font-semibold">Points:</span> 1000
                            </h3>
                            <h3 className="pb-2.5">
                                <span className="font-semibold">Current challenges:</span> 6
                            </h3>
                            <h3 className="pb-2.5">
                                <span className="font-semibold">Completed challenges:</span> 4
                            </h3>
                        </div>
                    </div>
                    {/* Skills Section */}
                    <TitleContainer
                        title="Your Skills"
                        content={
                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                {skills.map((skill, index) => (
                                    <div key={index}>
                                        <Achievement
                                            title={skill.title}
                                            description={skill.description}
                                        />
                                    </div>
                                ))}
                            </div>
                        }
                    />
                </div>
            </div>
        </BackgroundWrapper>
    );
}