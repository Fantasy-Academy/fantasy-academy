// src/api/subscription.js
import { apiFetch } from './http';

/**
 * GET /api/subscription/status
 * Returns: { isActive, status, planId, currentPeriodEnd, canceledAt, willCancelAtPeriodEnd }
 */
export function apiGetSubscriptionStatus() {
  return apiFetch('/api/subscription/status', { auth: true });
}

/**
 * POST /api/subscription/checkout
 * @param {'monthly'|'yearly'} plan
 * @param {string} successUrl - full URL including {CHECKOUT_SESSION_ID} placeholder
 * @param {string} cancelUrl  - full URL to return to on cancel
 * Returns: { checkoutUrl, sessionId }
 */
export function apiCreateCheckout({ plan, successUrl, cancelUrl }) {
  return apiFetch('/api/subscription/checkout', {
    method: 'POST',
    auth: true,
    body: { plan, successUrl, cancelUrl },
  });
}

/**
 * POST /api/subscription/portal
 * @param {string} returnUrl - full URL to return to after portal actions
 * Returns: { portalUrl }
 */
export function apiCreatePortal({ returnUrl }) {
  return apiFetch('/api/subscription/portal', {
    method: 'POST',
    auth: true,
    body: { returnUrl },
  });
}