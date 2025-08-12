import { apiFetch } from './http';

// POST /api/login -> { accessToken: '...', ... }
export function apiLogin(email, password) {
  return apiFetch('/api/login', {
    method: 'POST',
    auth: false, // login nevyžaduje token
    body: { email, password },
  });
}

// GET /api/me -> objekt uživatele
export function apiGetMe() {
  return apiFetch('/api/me', { method: 'GET', auth: true });
}