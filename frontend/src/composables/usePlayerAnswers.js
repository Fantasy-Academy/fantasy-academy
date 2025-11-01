// src/composables/usePlayerAnswers.js
import { apiFetch } from '../api/http';
import { ref } from 'vue';
import { useAuth } from './useAuth';

export function usePlayerAnswers() {
  const { token } = useAuth() || {};
  const answers = ref([]);           // rovnou pole -> lepší pro v-for
  const loading = ref(false);
  const error = ref(null);

  async function loadPlayerAnswers(playerId) {
    if (!playerId) return;

    loading.value = true;
    error.value = null;

    try {
      const data = await apiFetch(`/api/players/${playerId}/answers`, {
        method: 'GET',
        auth: true, // pokud používáš token přes apiFetch automaticky
        headers: token?.value ? { Authorization: `Bearer ${token.value}` } : {},
      });

      // Tady už je data JS objekt (např. {challenges: [...]})
      console.log('✅ Data z API:', data);

      answers.value = data?.challenges || [];

      console.log('📌 Uložené answers:', answers.value);

    } catch (e) {
      console.error('❌ API ERROR:', e);
      error.value = e.message;
    } finally {
      loading.value = false;
    }
  }

  return { answers, loading, error, loadPlayerAnswers };
}