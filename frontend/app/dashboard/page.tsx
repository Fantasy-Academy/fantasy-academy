import React from 'react';
import SkillCard from '../../components/dashboard/SkillCard';
import DashboardStats from '../../components/dashboard/DashboardStats'; // Adjust the import path if necessary

export default function Dashboard() {
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

    return (
        <div className='flex flex-col p-4'>
            <h1 className="text-3xl font-bold">Dashboard</h1>
            <DashboardStats 
                season={season} 
                points={points} 
                rank={rank} 
                challenges={challenges} 
            />
            <h1 className="text-3xl font-bold mt-8">Skills</h1>
            <div className='container mx-auto flex flex-row flex-wrap items-start gap-1'>
                {skills.map((skill) => (
                    <SkillCard key={skill.label} label={skill.label} percentage={skill.percentage} />
                ))}
            </div>
        </div>
    );
}