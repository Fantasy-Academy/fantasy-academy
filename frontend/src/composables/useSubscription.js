// src/composables/useSubscription.js
import { ref, computed } from 'vue';
import {
  apiGetSubscriptionStatus,
  apiCreateCheckout,
  apiCreatePortal,
} from '@/api/subscription';
import { toFriendlyError } from '@/utils/errorHandler';

// Module-level state — shared across all components that call useSubscription()
// Same pattern as useProfile.js uses for `me`
const status = ref(null);
const loading = ref(false);
const error = ref(null);

export function useSubscription() {

  // ─── Computed getters ──────────────────────────────────────────────────────

  const isActive = computed(() => status.value?.isActive ?? false);
  const willCancel = computed(() => status.value?.willCancelAtPeriodEnd ?? false);
  const currentPeriodEnd = computed(() => status.value?.currentPeriodEnd ?? null);
  const subscriptionStatus = computed(() => status.value?.status ?? null);

  // ─── Load status ───────────────────────────────────────────────────────────

  async function loadStatus() {
    loading.value = true;
    error.value = null;
    try {
      status.value = await apiGetSubscriptionStatus();
    } catch (e) {
      const fe = toFriendlyError(e);
      error.value = fe.userMessage || 'Failed to load subscription status.';
      console.warn('[useSubscription] loadStatus FAIL', e);
    } finally {
      loading.value = false;
    }
  }

  // ─── Redirect to Stripe Checkout ───────────────────────────────────────────

  async function redirectToCheckout(plan) {
    // plan must be 'monthly' or 'yearly'
    if (!['monthly', 'yearly'].includes(plan)) {
      console.error('[useSubscription] Invalid plan:', plan);
      return;
    }

    loading.value = true;
    error.value = null;

    // Build URLs — the success URL includes Stripe's placeholder for session ID
    const base = window.location.origin;
    const successUrl = `${base}/subscription/success`;
    const cancelUrl = `${base}/subscription/cancel`;

    try {
      const { checkoutUrl } = await apiCreateCheckout({ plan, successUrl, cancelUrl });
      // IMPORTANT: window.location.href, NOT router.push() — this is an external URL
      window.location.href = checkoutUrl;
    } catch (e) {
      const fe = toFriendlyError(e);
      error.value = fe.userMessage || 'Failed to start checkout. Please try again.';
      loading.value = false; // only reset on error; on success the page navigates away
    }
  }

  // ─── Redirect to Stripe Billing Portal ────────────────────────────────────

  async function redirectToPortal() {
    loading.value = true;
    error.value = null;

    const returnUrl = `${window.location.origin}/subscription`;

    try {
      const { portalUrl } = await apiCreatePortal({ returnUrl });
      window.location.href = portalUrl;
    } catch (e) {
      const fe = toFriendlyError(e);
      error.value = fe.userMessage || 'Failed to open billing portal. Please try again.';
      loading.value = false;
    }
  }

  // ─── Poll for subscription after Stripe redirects back ────────────────────
  // Stripe's webhook is async — the DB may not be updated immediately.
  // Poll up to `maxAttempts` times with `intervalMs` delay between each.

  async function pollUntilActive({ maxAttempts = 8, intervalMs = 2000 } = {}) {
    for (let i = 0; i < maxAttempts; i++) {
      await loadStatus();
      if (status.value?.isActive) return true;
      // Wait before next attempt (except after the last one)
      if (i < maxAttempts - 1) {
        await new Promise(resolve => setTimeout(resolve, intervalMs));
      }
    }
    return false; // timed out
  }

  return {
    status,
    loading,
    error,
    isActive,
    willCancel,
    currentPeriodEnd,
    subscriptionStatus,
    loadStatus,
    redirectToCheckout,
    redirectToPortal,
    pollUntilActive,
  };
}