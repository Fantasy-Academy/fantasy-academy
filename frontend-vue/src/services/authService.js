import { apiLogin, apiGetMe } from '../api/auth';
import { setToken, clearToken, getToken } from './tokenService';

export async function loginAndLoadUser({ email, password }) {
  // 1) přihlášení -> získáme JWT
  const loginRes = await apiLogin(email, password);
  const token = loginRes?.accessToken || loginRes?.token;

  if (!token) throw new Error('Chybí access token v odpovědi serveru.');
  setToken(token);

  // 2) načtení profilu
  const me = await apiGetMe();
  // volitelně si můžeš uložit i do localStorage:
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