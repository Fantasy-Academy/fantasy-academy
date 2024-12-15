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
        <BackgroundWrapper>
            <div className="flex flex-col items-start justify-center text-left pb-8 min-h-screen px-4 sm:px-8 max-w-5xl mx-auto">
                <div className="flex flex-row justify-between mb-8 mt-16 w-full px-4">
                    <div>
                        <h2 className="text-5xl font-bold text-white mb-2 font-alexandria">{session?.user?.name}</h2>
                        <h3 className="text-white text-lg">Rank #132</h3>
                        <h3 className="text-white text-lg">Round Played: 24</h3>
                    </div>

                    <div>
                        <Btn
                            type="button"
                            className='bg-cyan-400'
                            text="Logout"
                            onClick={() => signOut({ callbackUrl: 'http://localhost:3000/login' })}
                        />
                    </div>
                </div>

                <div className="space-y-8 w-full">
                    <TitleContainer
                        title="Your Achievements"
                        content={
                            <div className="flex gap-4 flex-wrap">
                                {achievements.map((achievement, index) => (
                                    <div key={index}>
                                        <Achievement
                                            title={achievement.title}
                                            description={achievement.description}
                                        />
                                    </div>
                                ))}
                            </div>
                        }
                    />
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
        </BackgroundWrapper>
    );

};

export default UserProfile;