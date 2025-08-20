import { apiFetch } from './http';

// GET /api/challenges  (vrací pole nebo Hydra { member / hydra:member, hydra:totalItems })
export async function apiListChallenges({ page = 1, auth = true, params = {} } = {}) {
  const query = new URLSearchParams({ page: String(page), ...params });
  const data = await apiFetch(`/api/challenges?${query.toString()}`, { method: 'GET', auth });

  // extract items
  const items =
    Array.isArray(data) ? data :
    Array.isArray(data?.member) ? data.member :
    Array.isArray(data?.['hydra:member']) ? data['hydra:member'] :
    [];

  // extract total
  const total =
    typeof data?.totalItems === 'number' ? data.totalItems :
    typeof data?.['hydra:totalItems'] === 'number' ? data['hydra:totalItems'] :
    items.length;

  return { items, total };
}

// GET /api/challenges/{id}
export function apiGetChallengeDetail(id) {
  return apiFetch(`/api/challenges/${id}`, { method: 'GET', auth: true });
}

// PUT /api/challenges/answer
export function apiAnswerChallenge(payload) {
  return apiFetch('/api/challenges/answer', { method: 'PUT', auth: true, body: payload });
}