import { apiFetch } from '@/api/http';

export async function apiGetLeaderboards({ page = 1, auth = true } = {}) {
  const data = await apiFetch(`/api/leaderboards?page=${encodeURIComponent(page)}`, { auth });

  // Hydra i plain varianty
  const items =
    Array.isArray(data) ? data :
    Array.isArray(data?.member) ? data.member :
    Array.isArray(data?.['hydra:member']) ? data['hydra:member'] :
    [];

  const totalItems =
    typeof data?.totalItems === 'number' ? data.totalItems :
    typeof data?.['hydra:totalItems'] === 'number' ? data['hydra:totalItems'] :
    items.length;

  // hydra:view -> last page (volitelné)
  const view = data?.view || data?.['hydra:view'] || null;
  let lastPage = 1;
  if (view?.last) {
    const m = view.last.match(/[\?&]page=(\d+)/i);
    lastPage = m ? parseInt(m[1], 10) || 1 : 1;
  }

  return { items, totalItems, lastPage };
}