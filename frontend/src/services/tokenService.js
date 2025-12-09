const TOKEN_KEY = 'accessToken';
import { decodeJwt } from '@/utils/jwt';


export function getToken() {
  return localStorage.getItem(TOKEN_KEY) || null;
}

export function setToken(token) {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
}

export function getTokenExpiration() {
  const token = getToken();
  if (!token) return null;

  const payload = decodeJwt(token);
  return payload?.exp ? payload.exp * 1000 : null;
}