// src/composables/useMembership.js
import { computed } from 'vue';
import { useProfile } from './useProfile';
import { useRouter } from 'vue-router';

export function useMembership() {
  const { me } = useProfile();
  const router = useRouter();

  // True if user has an active membership
  const isMember = computed(() => me.value?.isMember ?? false);

  // Membership expiry (informational display)
  const membershipExpiresAt = computed(() => me.value?.membershipExpiresAt ?? null);

  /**
   * Call a function only if user is a member.
   * Otherwise, redirect to subscription page.
   * Useful for gating button click handlers.
   *
   * Usage: requireMembership(() => router.push('/dashboard'))
   */
  function requireMembership(callback) {
    if (isMember.value) {
      callback();
    } else {
      router.push('/subscription');
    }
  }

  return {
    isMember,
    membershipExpiresAt,
    requireMembership,
  };
}