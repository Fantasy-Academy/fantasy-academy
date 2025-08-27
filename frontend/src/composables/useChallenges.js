// src/composables/useChallenges.js
import { ref } from 'vue';
import { apiListChallenges } from '@/api/challenges';

function normalizeList(raw) {
  const list = Array.isArray(raw) ? raw : [];
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
  const totalCount = ref(0);
  const loading = ref(false);
  const error = ref(null);

  async function loadChallenges({ page = 1, auth = true, params = {} } = {}) {
    loading.value = true;
    error.value = null;
    try {
      const { items, total } = await apiListChallenges({ page, auth, params });
      challenges.value = normalizeList(items);
      totalCount.value = total;
    } catch (err) {
      error.value = err?.message || 'Nepodařilo se načíst výzvy';
    } finally {
      loading.value = false;
    }
  }

  return { challenges, totalCount, loading, error, loadChallenges };
}