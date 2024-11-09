import React from 'react';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import Link from 'next/link';
import { challenges } from '../../mockData/challenges';

const Challenges = () => {
    const renderChallenges = (isActive: boolean) => {
        return challenges
            .filter(challenge => challenge.isActive === isActive)
            .map(challenge => (
                <Link key={challenge.id} href={`/challenges/${challenge.id}`}>
                    <ChallengeCard
                        title={challenge.title}
                        description={challenge.description}
                        duration={challenge.duration}
                        isActive={challenge.isActive}
                        isCompleted={challenge.isCompleted}
                    />
                </Link>
            ));
    };

    return (
        <div className="flex flex-col p-4">
            <h1 className="text-3xl font-bold text-black text-center">Current Challenges</h1>
            <div className="container mx-auto flex flex-wrap items-center justify-center md:justify-start">
                {renderChallenges(true)}
            </div>

            <h1 className="text-3xl font-bold text-black mt-8 text-center">Expired Challenges</h1>
            <div className="container mx-auto flex flex-wrap items-start justify-center sm:justify-start">
                {renderChallenges(false)}
            </div>
        </div>
    );
};

export default Challenges;