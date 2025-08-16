import { ref } from 'vue';
import { apiListChallenges } from '@/api/challenges';

function normalizeList(raw) {
  const list = Array.isArray(raw) ? raw : (Array.isArray(raw?.member) ? raw.member : []);
  return list.map(item => ({
    id: item.id,
    name: item.name,
    shortDescription: item.shortDescription || '',
    description: item.description || '',
    image: item.image ?? null,
    maxPoints: item.maxPoints ?? null,
    addedAt: item.addedAt,
    startsAt: item.startsAt,
    expiresAt: item.expiresAt,
    answeredAt: item.answeredAt ?? null,
    isStarted: !!item.isStarted,
    isExpired: !!item.isExpired,
    isAnswered: !!item.isAnswered,
    isEvaluated: !!item.isEvaluated,
  }));
}

export function useChallenges() {
  const challenges = ref([]);
  const loading = ref(false);
  const error = ref(null);

  async function loadChallenges(page = 1) {
    loading.value = true;
    error.value = null;
    try {
      const data = await apiListChallenges({ page });
      challenges.value = normalizeList(data);
    } catch (err) {
      error.value = err?.message || 'Nepodařilo se načíst výzvy';
    } finally {
      loading.value = false;
    }
  }

  return { challenges, loading, error, loadChallenges };
}