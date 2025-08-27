import { apiFetch } from '@/api/http';

// PUT /api/me/edit-profile  { name, userId? }
export function apiEditProfile(payload) {
  // OpenAPI říká, že očekává body: { name: string, userId?: uuid|null }
  return apiFetch('/api/me/edit-profile', {
    method: 'PUT',
    auth: true,
    body: payload,
  });
}

// PUT /api/me/change-password  { newPassword }
export function apiChangePassword(newPassword) {
  return apiFetch('/api/me/change-password', {
    method: 'PUT',
    auth: true,
    body: { newPassword },
  });
}