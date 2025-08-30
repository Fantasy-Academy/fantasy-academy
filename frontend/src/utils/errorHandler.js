// src/utils/errorHandler.js
/**
 * Vytáhne rozumnou hlášku pro uživatele z různých tvarů chyb (Hydra, API Platform, RFC7807, plain text).
 * Vrací { userMessage, code, status, raw }.
 */
export function toFriendlyError(err) {
  const fallback = 'Something went wrong. Please try again.';
  const out = { userMessage: fallback, code: 'unknown', status: undefined, raw: err };

  // 1) Náš apiFetch hází Error s .status a .data (json/string)
  const status = err?.status;
  out.status = status;

  // Pomocné vytažení textu
  const fromKnownFields = (obj) =>
    obj?.detail || obj?.title || obj?.message || obj?.description || null;

  // Zkus JSON payload
  let data = err?.data;
  try {
    if (typeof data === 'string' && data.trim().startsWith('{')) {
      data = JSON.parse(data);
    }
  } catch {
    /* ignore parse */
  }

  // 2) API Platform / Hydra / RFC7807
  if (data && typeof data === 'object') {
    // a) Validace (violations)
    if (Array.isArray(data.violations) && data.violations.length) {
      const first = data.violations[0];
      out.userMessage = first?.message || 'Please check your input.';
      out.code = 'validation_error';
      return out;
    }

    // b) message/detail/title/description
    const msg = fromKnownFields(data);
    if (msg) {
      out.userMessage = msg;
      out.code = 'api_error';
    }
  }

  // 3) Specifické statusy
  if (status === 400 && out.userMessage === fallback) {
    out.userMessage = 'Please check your input.';
    out.code = 'bad_request';
  }
  if (status === 401) {
    out.userMessage = 'Invalid credentials.';
    out.code = 'unauthorized';
  }
  if (status === 403) {
    out.userMessage = 'You do not have permission to do this.';
    out.code = 'forbidden';
  }
  if (status === 404) {
    out.userMessage = 'We couldn’t find what you were looking for.';
    out.code = 'not_found';
  }
  if (status >= 500) {
    out.userMessage = 'Something went wrong on our side. Please try again later.';
    out.code = 'server_error';
  }

  // 4) Fallback na err.message, pokud nic
  if (out.userMessage === fallback && err?.message) {
    out.userMessage = err.message;
  }

  return out;
}