'use client';

import React, { useEffect, useState, useCallback } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { useSession } from 'next-auth/react';
import ChallengeCard from '../../components/challenges/ChallengeCard';
import BackgroundWrapper from '../../layouts/BackgroundWrapper';
import ChallengeModal from '../../components/modal/ChallangeModal';
import Head from "next/head";
import Link from "next/link";


type Challenge = {
  id: string;
  name: string;
  shortDescription: string;
  description: string;
  image: string | null;
  addedAt: string;
  startsAt: string;
  expiresAt: string;
  answeredAt: string | null;
  isStarted: boolean;
  isExpired: boolean;
  isAnswered: boolean;
  isEvaluated: boolean;
};

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_URL;

const Challenges = () => {
  const [tab, setTab] = useState<'current' | 'completed' | "time's up">('current');
  const [challenges, setChallenges] = useState<Challenge[]>([]);
  const router = useRouter();
  const searchParams = useSearchParams();
  const challengeId = searchParams.get('id');

  const { data: session, status } = useSession();
  const token = (session as any)?.accessToken as string | undefined;
  const isAuthed = status === 'authenticated' && !!token;

  const fetchChallenges = useCallback(async () => {
    try {
      const res = await fetch(`${API_BASE}/api/challenges`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          ...(isAuthed ? { Authorization: `Bearer ${token}` } : {}),
          'Cache-Control': 'no-cache',
        },
        cache: 'no-store',
      });
      if (!res.ok) throw new Error('Chyba při načítání výzev');
      const data = await res.json();
      setChallenges(data.member || data);
    } catch (err) {
      console.error(err);
    }
  }, [isAuthed, token]);

  useEffect(() => {
    fetchChallenges();
  }, [fetchChallenges]);

  // přihlášený: všechny taby; nepřihlášený: jen "current"
  const availableTabs: Array<'current' | 'completed' | "time's up"> = isAuthed
    ? ['current', 'completed', "time's up"]
    : ['current'];

  // když se stav auth změní a aktuální tab není dostupný, vrať na 'current'
  useEffect(() => {
    if (!availableTabs.includes(tab)) setTab('current');
  }, [availableTabs, tab]);

  const filteredChallenges = challenges.filter((challenge) => {
    const isCompleted = challenge.isAnswered;
    const duration = new Date(challenge.expiresAt).getTime() - Date.now();
    const durationInMinutes = Math.max(Math.floor(duration / 60000), 0);

    if (tab === 'current') return durationInMinutes > 0 && !isCompleted;
    if (tab === 'completed') return isCompleted;
    if (tab === "time's up") return durationInMinutes === 0 && !isCompleted;
    return false;
  });

  const selectedChallenge = challenges.find((c) => c.id === challengeId) || null;

  const openModal = (id: string) => {
    if (!isAuthed) return; // bezpečnostní pojistka
    router.push(`/challenges?id=${id}`, { scroll: false });
  };

  const closeModal = () => {
    router.replace('/challenges', { scroll: false });
  };

  const handleSubmitSuccess = async () => {
    if (challengeId) {
      setChallenges((prev) =>
        prev.map((c) =>
          c.id === challengeId ? { ...c, isAnswered: true, answeredAt: new Date().toISOString() } : c
        )
      );
    }
    await fetchChallenges();
    closeModal();
  };

  return (
    <BackgroundWrapper>
      <Head>
        <title>Challenges | Fantasy Academy</title>
      </Head>
      <div className="min-h-screen py-8">
        {/* Tab Navigation */}
        <div className="w-fit mb-8 mx-auto px-8 py-4">
          <div className="max-w-[1200px] mx-auto flex justify-center gap-12 text-lg">
            {(['current', 'completed', "time's up"] as const).map((type) => {
              const isDisabled = !availableTabs.includes(type);
              return (
                <button
                  key={type}
                  className={`py-2 px-4 border-b-4 transition-colors font-bold ${
                    tab === type && !isDisabled
                      ? 'text-vibrantCoral border-vibrantCoral'
                      : isDisabled
                      ? 'text-gray-400 border-transparent cursor-not-allowed'
                      : 'text-charcoal hover:text-vibrantCoral border-transparent'
                  }`}
                  onClick={() => !isDisabled && setTab(type)}
                  disabled={isDisabled}
                  aria-disabled={isDisabled}
                  title={isDisabled ? 'Přihlas se pro další záložky' : undefined}
                >
                  {type === "time's up" ? "Time's up" : type.charAt(0).toUpperCase() + type.slice(1)}
                </button>
              );
            })}
          </div>
        </div>

        {/* Info pro nepřihlášené */}
        {!isAuthed && (
          <p className="text-center text-sm text-gray-600 mb-4">
            For more details please login{' '}
            <Link href="/signup" className="text-vibrantCoral underline hover:no-underline">
              Signup / Login
            </Link>.
          </p>
        )}

        {/* Challenge List */}
        <div className="flex flex-col items-center">
          <div className="w-full max-w-[1200px] mx-auto">
            <div className="flex flex-col gap-4">
              {filteredChallenges.map((challenge) => (
                <div
                  key={challenge.id}
                  // když není přihlášený, zablokuj interakci a dej „disabled“ vzhled na hover
                  className={`${!isAuthed ? 'pointer-events-none opacity-90' : ''}`}
                  onClick={() => openModal(challenge.id)}
                  title={!isAuthed ? 'Přihlas se pro otevření výzvy' : undefined}
                >
                  <ChallengeCard
                    challengeCard={{
                      id: challenge.id,
                      title: challenge.name,
                      shortDescription: challenge.shortDescription,
                      description: challenge.description,
                      image: challenge.image ?? '',
                      duration: Math.max(
                        Math.floor((new Date(challenge.expiresAt).getTime() - Date.now()) / 60000),
                        0
                      ),
                      isCompleted: challenge.isAnswered,
                    }}
                  />
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Modal jen pro přihlášené */}
        {isAuthed && selectedChallenge && challengeId && (
          <ChallengeModal
            challengeId={challengeId}
            onClose={closeModal}
            onSubmitSuccess={handleSubmitSuccess}
            apiBase={API_BASE}
          />
        )}
      </div>
    </BackgroundWrapper>
  );
};

export default Challenges;