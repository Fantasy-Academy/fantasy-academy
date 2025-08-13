import { apiFetch } from './http';


export function apiLogin(email, password) {
  return apiFetch('/api/login', {
    method: 'POST',
    auth: false,
    body: { email, password },
  });
}

export function apiRegister({ name, email, password }) {
  return apiFetch('/api/register', {
    method: 'POST',
    body: { name, email, password },
    auth: false,
  });
}

//objekt uživatele
export function apiGetMe() {
  return apiFetch('/api/me', { method: 'GET', auth: true });
}