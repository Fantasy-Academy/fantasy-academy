import { API_BASE_URL } from '../constants/config';
import { getToken, clearToken } from '../services/tokenService';

export async function apiFetch(path, { method = 'GET', headers = {}, body, auth = true } = {}) {
  const token = getToken();

  const finalHeaders = {
    'Content-Type': 'application/json',
    ...headers,
  };

  if (auth && token) {
    finalHeaders.Authorization = `Bearer ${token}`;
  }

  const res = await fetch(`${API_BASE_URL}${path}`, {
    method,
    headers: finalHeaders,
    body: body ? JSON.stringify(body) : undefined,
  });

  // 401 -> odhlásíme uživatele
  if (res.status === 401) {
    clearToken();
  }

  const isJson = res.headers.get('content-type')?.includes('application/json');
  const data = isJson ? await res.json() : await res.text();

  if (!res.ok) {
    const message = (isJson && data?.message) || res.statusText || 'Request failed';
    throw new Error(message);
  }

  return data;
}
