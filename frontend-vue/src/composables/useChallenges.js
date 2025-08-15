import { ref } from 'vue';
import { apiFetch } from '../api/http';

export function useChallenges() {
  const challenges = ref([]);
  const loading = ref(false);
  const error = ref(null);

  async function loadChallenges() {
    loading.value = true;
    error.value = null;
    try {
      challenges.value = await apiFetch('/api/challenges', { auth: true });
    } catch (err) {
      error.value = err.message || 'Nepodařilo se načíst výzvy';
    } finally {
      loading.value = false;
    }
  }

  return {
    challenges,
    loading,
    error,
    loadChallenges,
  };
}