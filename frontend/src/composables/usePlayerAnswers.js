// src/composables/usePlayerAnswers.js
import { apiFetch } from '../api/http';
import { ref } from 'vue';
import { useAuth } from './useAuth';

export function usePlayerAnswers() {
  const { token } = useAuth() || {};
  const answers = ref([]);
  const loading = ref(false);
  const error = ref(null);

  async function loadPlayerAnswers(playerId) {
    if (!playerId) {
      console.warn('[usePlayerAnswers] Missing playerId');
      return;
    }

    loading.value = true;
    error.value = null;

    console.groupCollapsed(
      `%c[usePlayerAnswers] Fetching answers for player ${playerId}`,
      'color:#4fa3ff'
    );
    console.log('➡️ endpoint:', `/api/players/${playerId}/answers`);

    try {
      const data = await apiFetch(`/api/players/${playerId}/answers`, {
        method: 'GET',
        auth: true,
        headers: token?.value
          ? { Authorization: `Bearer ${token.value}` }
          : {},
      });

      console.log("📥 RAW response:", data);
      console.log("🟦 Full challenge JSON:", JSON.stringify(data, null, 2));

      // Backend returns:
      // { id, challenges: [ { challengeId, challengeName, points, questions[], gameweek } ] }
      answers.value = Array.isArray(data?.challenges) ? data.challenges : [];

      console.log("📌 Stored challenges:", answers.value);

      // EXTRA: show correct answers to confirm structure
      answers.value.forEach((c, ci) => {
        c.questions?.forEach((q, qi) => {
          console.log(
            `%c[CHECK correctAnswer] challenge=${ci} question=${qi}`,
            "color:#ff00ff;font-weight:bold"
          );
          console.log("correctAnswer object:", q.correctAnswer);
        });
      });

      console.groupEnd();
    } catch (e) {
      console.error('❌ API ERROR:', e);
      error.value = e.message || 'Failed to load player answers';
      console.groupEnd();
    } finally {
      loading.value = false;
    }
  }

  return {
    answers,
    loading,
    error,
    loadPlayerAnswers
  };
}