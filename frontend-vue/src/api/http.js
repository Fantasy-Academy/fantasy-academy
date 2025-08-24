// src/api/http.js
import { getToken } from '@/services/tokenService';

const BASE_URL =
  import.meta.env.VITE_BACKEND_URL ??
  import.meta.env.VITE_API_BASE_URL ??
  ''; // ← nikdy nenechá "undefined"

// helper pro bezpečné spojení base + path
function joinUrl(base, path) {
  if (!base) return path; // když používáš dev proxy, stačí relativní /api/...
  return `${base.replace(/\/+$/, '')}${path.startsWith('/') ? '' : '/'}${path}`;
}

export async function apiFetch(path, opts = {}) {
  const url = joinUrl(BASE_URL, path);
  const {
    method = 'GET',
    auth = false,             // default bez tokenu; u privátních volání pošli auth:true
    body,
    headers: extraHeaders = {},
    credentials,              // volitelné přeposlání (např. 'include')
    signal,                   // volitelně AbortController
  } = opts;

  const headers = {
    'Content-Type': 'application/json',
    ...extraHeaders,
  };

  if (auth) {
    const token = getToken();
    if (token) headers.Authorization = `Bearer ${token}`;
  }

  const res = await fetch(url, {
    method,
    headers,
    body: body != null ? JSON.stringify(body) : undefined,
    credentials,
    signal,
  });

  const ct = res.headers.get('content-type') || '';
  const contentLength = res.headers.get('content-length');

  if (!res.ok) {
    // snaž se vyčíst message z JSONu/Plain textu
    let message = `${res.status} ${res.statusText}`;
    let data = null;

    try {
      if (ct.includes('application/json')) {
        data = await res.json();
        message = data?.detail || data?.title || data?.message || message;
      } else {
        const text = await res.text();
        message = text || message;
      }
    } catch {
      /* noop */
    }

    const err = new Error(message);
    err.status = res.status;
    err.data = data;
    err.allow = res.headers.get('Allow') || undefined; // u 405 pomůže
    throw err;
  }

  // 204 No Content – úspěch bez těla
  if (res.status === 204) return { ok: true, status: 204 };

  // Některé 2xx bez těla (nebo bez JSONu)
  if (!ct.includes('application/json') || contentLength === '0') {
    return { ok: true, status: res.status };
  }

  // Bezpečně zkus JSON
  try {
    return await res.json();
  } catch {
    return { ok: true, status: res.status };
  }
}