import { ref, computed } from 'vue';
import { getTokenExpiration } from '@/services/tokenService';
import { loginAndLoadUser, registerAndLoadUser, logoutUser, getStoredUser } from '@/services/authService';
import { toFriendlyError } from '@/utils/errorHandler';

const user = ref(getStoredUser());
const loading = ref(false);
const error = ref(null);

let intervalCheck = null;

function startExpCheck() {
  stopExpCheck();

  intervalCheck = setInterval(() => {
    const exp = getTokenExpiration();

    if (!exp) return; // bez exp → nikdy neodhlašuj
    if (Date.now() >= exp) {
      console.warn("[useAuth] Token expired → auto logout");
      safeLogout();
    }
  }, 1000); // kontrola 1× za sekundu
}

function stopExpCheck() {
  if (intervalCheck) {
    clearInterval(intervalCheck);
    intervalCheck = null;
  }
}

function safeLogout() {
  stopExpCheck();
  logoutUser();
  user.value = null;
  window.location.href = "/login";
}

export function useAuth() {
  const isAuthenticated = computed(() => !!user.value);

  async function login(email, password) {
    loading.value = true;
    error.value = null;

    try {
      const me = await loginAndLoadUser({ email, password });
      user.value = me;
      startExpCheck();        // spustí kontrolu jen pokud je user přihlášen
      return me;
    } catch (e) {
      error.value = toFriendlyError(e).userMessage || "Login failed";
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
      startExpCheck();
      return me;
    } catch (e) {
      error.value = toFriendlyError(e).userMessage || "Registration failed";
      throw e;
    } finally {
      loading.value = false;
    }
  }

  return {
    user,
    loading,
    error,
    isAuthenticated,
    login,
    register,
    logout: safeLogout,
  };
}

// spustit kontrolu, pokud byl user v localStorage
if (user.value) {
  startExpCheck();
}