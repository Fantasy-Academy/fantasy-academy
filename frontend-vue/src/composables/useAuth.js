// src/composables/useAuth.js
import { ref, computed } from 'vue';
import {
  loginAndLoadUser,
  logoutUser,
  getStoredUser,
  registerAndLoadUser,
} from '../services/authService';

const user = ref(getStoredUser());
const loading = ref(false);
const error = ref(null);

export function useAuth() {
  const isAuthenticated = computed(() => !!user.value);

async function login(email, password) {
  loading.value = true; error.value = null;
  console.log('[useAuth] login() args:', { email, hasPw: !!password });

  try {
    const me = await loginAndLoadUser({ email, password });
    user.value = me;
    const token = localStorage.getItem('accessToken');
    console.info('[useAuth] login OK', { user: { id: me?.id, email: me?.email }, hasToken: !!token });
    return me;
  } catch (e) {
    console.warn('[useAuth] login FAIL', { status: e?.status, message: e?.message, data: e?.data });
    error.value = e?.message || 'Přihlášení selhalo';
    throw e;
  } finally {
    loading.value = false;
  }
}

  async function register(name, email, password) {
    loading.value = true;
    error.value = null;
    try {
      const me = await registerAndLoadUser({ name, email, password });
      user.value = me;

      const token = localStorage.getItem('accessToken');
      console.info('[useAuth] register OK', { user: { id: me?.id, name: me?.name, email: me?.email }, hasToken: !!token });

      return me;
    } catch (e) {
      console.warn('[useAuth] register FAIL', { status: e?.status, message: e?.message, data: e?.data });
      error.value = e?.message || 'Registrace selhala';
      throw e;
    } finally {
      loading.value = false;
    }
  }

  function logout() {
    const token = localStorage.getItem('accessToken');
    console.info('[useAuth] logout', { hadToken: !!token });
    logoutUser();
    user.value = null;
  }

  return {
    user,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout,
  };
}