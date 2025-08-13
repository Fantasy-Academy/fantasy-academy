import { apiLogin, apiGetMe, apiRegister } from '../api/auth';
import { setToken, clearToken, getToken } from './tokenService';

export async function loginAndLoadUser({ email, password }) {
  const loginRes = await apiLogin(email, password);
  console.debug('[authService] /api/login response:', loginRes);

  const token = loginRes?.token; // ⬅️ tady taky "token"
  if (!token) {
    const detail = loginRes?.detail || loginRes?.message;
    throw new Error(detail || 'Chybí token v odpovědi /api/login.');
  }

  setToken(token);
  const me = await apiGetMe();
  localStorage.setItem('user', JSON.stringify(me));
  return me;
}

export async function registerAndLoadUser({ name, email, password }) {
  // 1) registrace (204 No Content)
  await apiRegister({ name, email, password });

  // 2) login – musí vrátit { token }
  const loginRes = await apiLogin(email, password);
  console.debug('[authService] /api/login response:', loginRes); // ⬅️ uvidíš, co přesně vrací

  const token = loginRes?.token; // ⬅️ SPECIFIKACE říká "token"
  if (!token) {
    // vypiš detail z backendu, pokud je
    const detail = loginRes?.detail || loginRes?.message;
    throw new Error(detail || 'Chybí token v odpovědi /api/login.');
  }

  setToken(token);

  // 3) načti profil
  const me = await apiGetMe();
  localStorage.setItem('user', JSON.stringify(me));
  return me;
}

export function logoutUser() {
  clearToken();
  localStorage.removeItem('user');
}

export function getStoredUser() {
  const raw = localStorage.getItem('user');
  return raw ? JSON.parse(raw) : null;
}

export function isLoggedIn() {
  return !!getToken();
}