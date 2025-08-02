'use client';
import React, { useEffect } from 'react';
import { useSession } from 'next-auth/react';
import { useRouter } from 'next/router';

import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import TitleContainer from '../../components/containers/TitleContainer';
import Achievement from '../../components/common/Achievement';

export default function Dashboard() {
  const { data: session, status } = useSession();
  const router = useRouter();

  useEffect(() => {
    // pokud uživatel není přihlášen a session už je načtena, přesměrování
    if (status === 'unauthenticated') {
      router.push('/login');
    }
  }, [status, router]);

  const skills = [
    { title: 'Skill 1', description: 'Description of skill 1.' },
    { title: 'Skill 2 wider title.', description: 'Description of skill 2.' },
    { title: 'Skill 3', description: 'Description of skill 3.' },
    { title: 'Skill 4', description: 'Description of skill 4.' },
    { title: 'Skill 5', description: 'Description of skill 5.' },
  ];

  return (
    <BackgroundWrapper>
      <div className="flex items-center justify-center min-h-screen px-4">
        <div className="flex flex-col w-full max-w-[860px] mx-auto py-6 gap-6">

          {/* Hlavní sekce: profil + statistiky */}
          <div className="flex flex-col lg:flex-row text-white gap-6">

            {/* Profil box */}
            <div className="p-6 flex-1 bg-white rounded w-full">
              {session ? (
                <div>
                  <h1 className="text-3xl sm:text-5xl text-vibrantCoral font-bold tracking-wider uppercase font-bebasNeue leading-tight">
                    {session.user?.name}
                  </h1>
                  <div className="pt-3 text-base sm:text-xl text-coolGray">
                    <h3>Rank #132</h3>
                    <h3>Round Played: 24</h3>
                    <h3
                      className="text-sm text-gray-400 mt-2 truncate overflow-hidden whitespace-nowrap"
                      title={session.accessToken}
                    >
                      Access token: {session.accessToken}
                    </h3>
                  </div>
                </div>
              ) : (
                <h1 className="text-2xl sm:text-4xl text-charcoal font-bold tracking-wider uppercase">
                  Loading...
                </h1>
              )}
            </div>

            {/* Statistiky */}
            <div className="flex flex-col flex-grow gap-3 w-full">
              <h3 className="bg-charcoal px-4 py-2 rounded text-base sm:text-lg">Season: 1</h3>
              <h3 className="bg-charcoal px-4 py-2 rounded text-base sm:text-lg">Points: 1000</h3>
              <h3 className="bg-charcoal px-4 py-2 rounded text-base sm:text-lg">Current challenges: 6</h3>
              <h3 className="bg-charcoal px-4 py-2 rounded text-base sm:text-lg">Completed challenges: 4</h3>
            </div>
          </div>

          {/* Sekce skills */}
          <TitleContainer
            title=""
            content={
              <div className="flex flex-wrap gap-4">
                {skills.map((skill, index) => (
                  <div key={index} className="w-full sm:w-1/2 lg:w-1/3">
                    <Achievement title={skill.title} description={skill.description} />
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