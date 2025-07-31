'use client';
import React from 'react';
import Btn from '../../components/button/Btn';
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
        <BackgroundWrapper>
            <div className="flex flex-col gap-8 justify-center text-left pb-8 px-4 sm:px-8 max-w-5xl mx-auto mt-8">
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
                            No user
                        </h1>
                    )}
                </div>
                <div className="w-full flex flex-col gap-8">
                    <div>
                        <hr className='h-1 bg-charcoal' />
                        <TitleContainer
                            title="Your Achievements"
                            content={
                                <div className="flex flex-row gap-4 flex-wrap">
                                    {achievements.map((achievement, index) => (
                                        <div key={index}>
                                            <Achievement
                                                title={achievement.title}
                                                description={achievement.description}
                                            />
                                        </div>
                                    ))}
                                    <div className='flex flex-col justify-end mb-2'>
                                        <Btn>Show more</Btn>
                                    </div>
                                </div>
                            }
                        />
                    </div>
                    <div>
                        <hr className='h-1 bg-charcoal' />
                        <TitleContainer
                            title="Your Skills"
                            content={
                                <div className="flex gap-4 flex-wrap">
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
            </div>
        </BackgroundWrapper>
    );

};

export default UserProfile;