// src/composables/useProfile.js
import { ref } from 'vue';
import { apiGetMe } from '@/api/auth';
import { useRouter } from 'vue-router';
import { toFriendlyError } from '@/utils/errorHandler';

const me = ref(null);
const loading = ref(false);
const error = ref('');

export function useProfile() {
  const router = useRouter();

  async function load() {
    loading.value = true;
    error.value = '';

    try {
      me.value = await apiGetMe();
    } catch (e) {
      const fe = toFriendlyError(e);
      console.warn('[useProfile] load FAIL', {
        status: e?.status,
        message: fe.userMessage,
        rawMessage: e?.message,
        data: e?.data,
      });

      error.value = fe.userMessage || 'Nepodařilo se načíst profil.';

      // Pokud je uživatel odhlášený (401), přesměrujeme na login
      if (e?.status === 401) {
        router.push({ path: '/login', query: { redirect: '/profile' } });
      }
    } finally {
      loading.value = false;
    }
  }

  return { me, loading, error, load };
}