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
      console.warn('[usePlayerAnswers] ❌ playerId není definováno');
      return;
    }

    loading.value = true;
    error.value = null;

    const url = `/api/players/${playerId}/answers`;
    const headers = {
      'Content-Type': 'application/json',
      ...(token?.value ? { Authorization: `Bearer ${token.value}` } : {})
    };

    console.group(`[usePlayerAnswers] 🔍 Odesílám request`);
    console.log('➡ URL:', url);
    console.log('➡ Headers:', headers);

    try {
      const response = await apiFetch(url, { headers });

      console.log('⬅ Response status:', response.status);
      
      // Čteme jako text (kvůli 204/500 odpovědím bez těla)
      const rawText = await response.text();
      console.log('⬅ Response raw text:', rawText);

      let data = null;
      try {
        data = rawText ? JSON.parse(rawText) : null;
        console.log('⬅ Response JSON parsed:', data);
      } catch (err) {
        console.warn('⚠ JSON parsing failed:', err);
      }

      if (!response.ok) {
        throw new Error(data?.detail || `Error ${response.status}: Unable to fetch answers`);
      }

      // API dokumentace uvádí Player.answers → očekáváme např. data.challenges
      answers.value = data?.challenges || [];
      console.log('✅ Ukládám answers:', answers.value);

    } catch (e) {
      console.error('❌ API ERROR:', e);
      error.value = e.message;
    } finally {
      loading.value = false;
      console.groupEnd();
    }
  }

  return { answers, loading, error, loadPlayerAnswers };
}
