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
  const headers = {
    'Content-Type': 'application/json',
    ...(opts.headers || {}),
  };

  if (opts.auth) {
    const token = getToken();               // ← používáme sjednoceně accessToken
    if (token) headers.Authorization = `Bearer ${token}`;
  }

  const res = await fetch(url, {
    method: opts.method || 'GET',
    headers,
    body: opts.body ? JSON.stringify(opts.body) : undefined,
  });

  const ct = res.headers.get('content-type') || '';
  if (!res.ok) {
    let msg = `${res.status} ${res.statusText}`;
    try {
      if (ct.includes('application/json')) {
        const j = await res.json();
        msg = j?.detail || j?.title || j?.message || msg;
      } else {
        const t = await res.text();
        msg = t || msg;
      }
    } catch {}
    const err = new Error(msg);
    err.status = res.status;
    throw err;
  }

  if (!ct.includes('application/json')) return undefined;
  return await res.json();
}