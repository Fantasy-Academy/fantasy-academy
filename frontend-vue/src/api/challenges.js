import { apiFetch } from './http';

// GET /api/challenges  (vrací pole nebo Hydra { member: [...] })
export function apiListChallenges({ page = 1 } = {}) {
  const qs = new URLSearchParams({ page }).toString();
  return apiFetch(`/api/challenges?${qs}`, { method: 'GET', auth: true });
}

// GET /api/challenges/{id}
export function apiGetChallengeDetail(id) {
  return apiFetch(`/api/challenges/${id}`, { method: 'GET', auth: true });
}

// PUT /api/challenges/answer
export function apiAnswerChallenge(payload) {
  return apiFetch('/api/challenges/answer', { method: 'PUT', auth: true, body: payload });
}