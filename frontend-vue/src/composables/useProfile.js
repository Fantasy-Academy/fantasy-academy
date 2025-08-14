import { ref } from 'vue';
import { apiGetMe } from '@/api/auth';
import { useRouter } from 'vue-router';

const me = ref(null);
const loading = ref(false);
const error = ref('');

export function useProfile() {
  const router = useRouter();

  async function load() {
    loading.value = true; error.value = '';
    try {
      me.value = await apiGetMe();
    } catch (e) {
      error.value = e?.message || 'Nepodařilo se načíst profil.';
      if (e?.status === 401) {
        router.push({ path: '/login', query: { redirect: '/profile' } });
      }
    } finally {
      loading.value = false;
    }
  }

  return { me, loading, error, load };
}