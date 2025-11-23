import { ref, computed, onMounted } from 'vue';
import { apiFetch } from '@/api/http';
import { useAuth } from './useAuth';

export function useGameweek() {
  const { token } = useAuth();

  const gameweeks = ref({
    current: null,
    next: null,
    previous: null,
  });

  const loading = ref(false);
  const error = ref(null);

  async function loadGameweeks() {
    loading.value = true;
    error.value = null;

    try {
      const data = await apiFetch('/api/gameweeks', {
        method: 'GET',
        headers: token?.value
          ? { Authorization: `Bearer ${token.value}` }
          : {},
      });

      console.log("🔍 Raw response from /api/gameweeks:", data);

      gameweeks.value = data;
    } catch (e) {
      console.error('❌ Error loading gameweeks:', e);
      error.value = e.message;
    } finally {
      loading.value = false;
    }
  }

  // 🔥 Auto-load when imported
  onMounted(loadGameweeks);

  // Computed getters
  const currentGameweek = computed(() => gameweeks.value?.current ?? null);
  const nextGameweek = computed(() => gameweeks.value?.next ?? null);
  const previousGameweek = computed(() => gameweeks.value?.previous ?? null);

  return {
    gameweeks,
    currentGameweek,
    nextGameweek,
    previousGameweek,
    loading,
    error,
    loadGameweeks,
  };
} 