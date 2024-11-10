import React from 'react';
import { useSession } from 'next-auth/react';
import SkillCard from '../../components/dashboard/SkillCard';
import DashboardStats from '../../components/dashboard/DashboardStats'; // Adjust the import path if necessary

export default function Dashboard() {
    const { data: session } = useSession();

    const skills = [
        { label: "Mistr kotle", percentage: 95 },
        { label: "Lucky 7", percentage: 88 },
        { label: "The manager", percentage: 75 },
        { label: "Skill", percentage: 80 },
    ];

    const season = 1;
    const points = 1000;
    const rank = 5;
    const challenges = { total: 6, completed: 4 };

    if (!session) {
        return <div>Please log in to view this page.</div>;
    }

    // Můžete používat session data (např. token) k získání chráněných dat
    const { token } = session;

    return (

        <div className="flex flex-col p-4 items-center sm:items-start">
            <h1>Welcome, {session.user.name}!</h1>
            <p>Your token: {token}</p>
            <h1 className="text-3xl font-bold text-center sm:text-left">Your Stats</h1>
            <DashboardStats
                season={season}
                points={points}
                rank={rank}
                challenges={challenges}
            />
            <h1 className="text-3xl font-bold mt-8 text-center sm:text-left">Skills</h1>
            <div className="container flex flex-row flex-wrap items-start gap-1 justify-center sm:justify-start">
                {skills.map((skill) => (
                    <SkillCard key={skill.label} label={skill.label} percentage={skill.percentage} />
                ))}
            </div>
        </div>
    );
}