import { API_BASE_URL } from '../constants/config';
import { getToken, clearToken } from '../services/tokenService';

export async function apiFetch(path, { method='GET', headers={}, body, auth=true } = {}) {
  const token = getToken();
  const finalHeaders = { 'Content-Type': 'application/json', ...headers };
  if (auth && token) finalHeaders.Authorization = `Bearer ${token}`;

  const url = `${API_BASE_URL}${path}`;
  const res = await fetch(url, { method, headers: finalHeaders, body: body ? JSON.stringify(body) : undefined });

  if (res.status === 401) clearToken();

  const ct = res.headers.get('content-type') || '';
  const isJson = ct.includes('application/json') || ct.includes('application/ld+json');
  const hasBody = res.status !== 204 && res.status !== 205;
  const data = hasBody ? (isJson ? await res.json() : await res.text()) : null;

  if (!res.ok) {
    const message =
      (isJson && (data?.detail || data?.message || data?.title)) ||
      (typeof data === 'string' && data) ||
      res.statusText || 'Request failed';
    const err = new Error(message);
    err.status = res.status;
    err.data = data;
    throw err;
  }

  return data;
}