import { getToken } from '@/services/tokenService';
import * as Sentry from '@sentry/vue';

const BASE_URL =
  import.meta.env.VITE_BACKEND_URL ??
  import.meta.env.VITE_API_BASE_URL ??
  '';

const API_DEBUG = String(import.meta.env.VITE_API_DEBUG || '').toLowerCase() === 'true';

// helper pro bezpečné spojení base + path
function joinUrl(base, path) {
  if (!base) return path; // s dev proxy stačí relativní /api/...
  return `${base.replace(/\/+$/, '')}${path.startsWith('/') ? '' : '/'}${path}`;
}

let REQ_ID = 0;

export async function apiFetch(path, opts = {}) {
  const reqId = ++REQ_ID;
  const url = joinUrl(BASE_URL, path);

  const {
    method = 'GET',
    auth = false,
    body,
    headers: extraHeaders = {},
    credentials,
    signal,
  } = opts;

  const headers = {
    'Content-Type': 'application/json',
    ...extraHeaders,
  };

  if (auth) {
    const token = getToken();
    if (token) headers.Authorization = `Bearer ${token}`;
  }

  if (API_DEBUG) {
    const authHeader = headers.Authorization ? `${headers.Authorization.slice(0, 16)}…` : undefined;
    // group pro přehlednost
    // (collapsed, ať to nekazí konzoli)
    console.groupCollapsed(`%c[apiFetch#${reqId}] → ${method} ${url}`, 'color:#888');
    console.log('request', {
      url,
      method,
      headers: { ...headers, ...(authHeader ? { Authorization: authHeader } : {}) },
      body: body ?? null,
      credentials: credentials ?? undefined,
    });
    console.groupEnd();
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

  // ----- ERROR BRANCH -----
  if (!res.ok) {
    let message = `${res.status} ${res.statusText}`;
    let data = null;
    let text = null;

    try {
      if (ct.includes('application/json')) {
        data = await res.json();
        message = data?.detail || data?.title || data?.message || message;
      } else {
        text = await res.text();
        message = text || message;
      }
    } catch { /* noop */ }

    // viditelný log u chyb
    const allow = res.headers.get('Allow') || undefined;
    const maskedAuth = headers.Authorization ? `${headers.Authorization.slice(0, 16)}…` : undefined;

    console.error('[apiFetch ERROR]', {
      id: reqId,
      url,
      method,
      status: res.status,
      statusText: res.statusText,
      message,
      responseJson: data,
      responseText: text,
      allow,
      sentBody: body ?? null,
      sentHeaders: { ...headers, ...(maskedAuth ? { Authorization: maskedAuth } : {}) },
    });

    const err = new Error(message);
    err.status = res.status;
    err.data = data ?? text;
    err.allow = allow;

    // Report API errors to Sentry (if initialized)
    if (import.meta.env.VITE_SENTRY_DSN) {
      Sentry.captureException(err, {
        contexts: {
          api_request: {
            url,
            method,
            status: res.status,
            statusText: res.statusText,
            request_id: reqId,
          },
        },
        tags: {
          api_endpoint: path,
          http_status: res.status,
        },
        extra: {
          responseData: data,
          responseText: text,
          sentBody: body,
          allow,
        },
      });
    }

    throw err;
  }

  // ----- SUCCESS BRANCH -----
  if (res.status === 204) {
    if (API_DEBUG) console.info('[apiFetch OK]', { id: reqId, url, method, status: 204 });
    return { ok: true, status: 204 };
  }

  if (!ct.includes('application/json') || contentLength === '0') {
    if (API_DEBUG) console.info('[apiFetch OK]', { id: reqId, url, method, status: res.status, note: 'no-json' });
    return { ok: true, status: res.status };
  }

  try {
    const json = await res.json();
    if (API_DEBUG) {
      console.info('[apiFetch OK]', { id: reqId, url, method, status: res.status, json });
    }
    return json;
  } catch {
    if (API_DEBUG) {
      console.info('[apiFetch OK]', { id: reqId, url, method, status: res.status, note: 'json-parse-failed' });
    }
    return { ok: true, status: res.status };
  }
}