import { ref, computed } from 'vue';
import { loginAndLoadUser, logoutUser, getStoredUser, isLoggedIn } from '../services/authService';

const user = ref(getStoredUser());
const loading = ref(false);
const error = ref(null);

export function useAuth() {
  const isAuthenticated = computed(() => isLoggedIn());

  async function login(email, password) {
    loading.value = true;
    error.value = null;
    try {
      const me = await loginAndLoadUser({ email, password });
      user.value = me;
      return me;
    } catch (e) {
      error.value = e.message || 'Přihlášení selhalo';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  function logout() {
    logoutUser();
    user.value = null;
  }

  return {
    user,
    loading,
    error,
    isAuthenticated,
    login,
    logout,
  };
}