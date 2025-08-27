import { apiFetch } from './http';

export function apiLogin({ email, password } = {}) {
  if (!email || !password) {
    console.warn('[apiLogin] missing email/password in payload');
  }
  return apiFetch('/api/login', {
    method: 'POST',
    auth: false,
    body: { email, password },
  });
}

export function apiGetMe() {
  return apiFetch('/api/me', { auth: true });
}

export function apiRegister({ name, email, password }) {
  return apiFetch('/api/register', {
    method: 'POST',
    auth: false,
    body: { name, email, password },
  });
}