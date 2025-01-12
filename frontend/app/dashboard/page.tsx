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
        { title: 'Skill 2 wider title.', description: 'Description of skill 2.' },
        { title: 'Skill 3', description: 'Description of skill 3.' },
        { title: 'Skill 4', description: 'Description of skill 4.' },
        { title: 'Skill 5', description: 'Description of skill 5.' },
    ];

    return (
        <BackgroundWrapper>
            {/* Full height and centered content */}
            <div className="flex items-center justify-center h-screen">
                {/* Card container */}
                <div className="flex flex-col w-full max-w-[860px] mx-auto p-6">
                    {/* Header Section */}
                    <div className="flex flex-col sm:flex-row text-white mb-6">
                        <div className="p-6 flex-1 bg-white rounded">
                            {session ? (
                                <div>
                                    <h1 className="text-5xl text-vibrantCoral font-bold tracking-wider uppercase font-bebasNeue leading-10 whitespace-nowrap">
                                        {session.user?.name}
                                    </h1>
                                    <div className="pt-2 text-xl text-coolGray">
                                        <h3>Rank #132</h3>
                                        <h3>Round Played: 24</h3>
                                    </div>
                                </div>
                            ) : (
                                <h1 className="text-4xl text-charcoal font-bold tracking-wider uppercase whitespace-nowrap">
                                    You are not logged in
                                </h1>
                            )}
                        </div>
                        <div className="flex flex-col flex-grow p-6 gap-0.5 font-sourceSans3 text-base">
                            <h3 className="pb-2.5 bg-charcoal px-4 py-2 rounded">
                                Season: 1
                            </h3>
                            <h3 className="pb-2.5 bg-charcoal px-4 py-2 rounded">
                                Points: 1000
                            </h3>
                            <h3 className="pb-2.5 bg-charcoal px-4 py-2 rounded">
                                Current challenges: 6
                            </h3>
                            <h3 className="pb-2.5 bg-charcoal px-4 py-2 rounded">
                                Completed challenges: 4
                            </h3>
                        </div>
                    </div>
                    {/* Skills Section */}
                    <TitleContainer
                    title=''
                        content={
                            <div className='flex flex-row flex-wrap'>
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
        </BackgroundWrapper>
    );
}