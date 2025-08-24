import { apiFetch } from '@/api/http';

export async function apiRequestResetCode(email) {
  await apiFetch('/api/forgotten-password/request-reset-code', {
    method: 'PUT',
    auth: false,
    body: { email },
  });
}

export function apiResetPassword({ code, newPassword }) {
  return apiFetch('/api/forgotten-password/reset', {
    method: 'PUT',
    auth: false,
    body: { code, newPassword },
  });
}