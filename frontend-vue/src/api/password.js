import { apiFetch } from '@/api/http';

export async function apiRequestResetCode(email) {
  console.log('[apiRequestResetCode] sending', email);
  const res = await apiFetch('/api/forgotten-password/request-reset-code', {
    method: 'PUT',
    body: { email }
  });
  console.log('[apiRequestResetCode] response', res);
  return res;
}

export function apiResetPassword({ code, newPassword }) {
  return apiFetch('/api/forgotten-password/reset', {
    method: 'PUT',
    auth: false,
    body: { code, newPassword },
  });
}