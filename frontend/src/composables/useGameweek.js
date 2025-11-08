import { ref, computed } from 'vue';
import { useChallenges } from './useChallenges';

export function useGameweek() {
  const { challenges, loadChallenges } = useChallenges();

  // Pokud ještě nemáme výzvy, načteme
  if (!challenges.value || challenges.value.length === 0) {
    loadChallenges();
  }

  // Aktuální běžící gameweek
  const currentGameweek = computed(() => {
    const active = challenges.value?.find(c => c.isStarted && !c.isExpired);
    return active?.gameweek ?? null;
  });

  // Nejbližší nadcházející gameweek
  const nextGameweek = computed(() => {
    const next = challenges.value?.find(c => !c.isStarted && !c.isExpired);
    return next?.gameweek ?? null;
  });

  return {
    currentGameweek,
    nextGameweek,
    challenges
  };
}