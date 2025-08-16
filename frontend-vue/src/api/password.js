import { apiFetch } from '@/api/http';

export async function apiRequestResetCode(email) {
  if (!email) throw new Error('Email is required');
  return apiFetch('/api/forgotten-password/request-reset-code', {
    method: 'PUT',
    auth: false,
    body: { email },
  });
}