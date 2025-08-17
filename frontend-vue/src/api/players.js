import { apiFetch } from '@/api/http';

export async function apiGetPlayer(id) {
  if (!id) throw new Error('Player id is required');
  return apiFetch(`/api/player/${encodeURIComponent(id)}`, { auth: true });
}