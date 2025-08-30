<!-- src/views/ResetPasswordPage.vue -->
<template>
  <section class="mx-auto max-w-md px-4 py-10">
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <h1 class="font-bebas-neue text-3xl tracking-wide text-blue-black">Reset Password</h1>
      <p class="mt-1 text-sm text-cool-gray font-alexandria">
        Code: <strong>{{ code || '—' }}</strong>, Email: <strong>{{ email || '—' }}</strong>
      </p>

      <!-- Error -->
      <div
        v-if="error"
        class="mt-3 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral"
      >
        {{ error }}
      </div>

      <!-- Success -->
      <div
        v-if="success"
        class="mt-3 rounded-xl border border-pistachio/30 bg-pistachio/10 p-3 text-pistachio"
      >
        Password changed. Logging you in…
      </div>

      <!-- Form -->
      <form class="mt-5 space-y-3" @submit.prevent="onSubmit" novalidate>
        <div>
          <label class="block text-sm font-medium text-blue-black mb-1">New password</label>
          <input
            v-model.trim="password"
            type="password"
            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-golden-yellow"
            :class="passwordError ? 'border-vibrant-coral' : 'border-charcoal/20'"
            placeholder="••••••••"
          />
          <p v-if="passwordError" class="mt-1 text-sm text-vibrant-coral">{{ passwordError }}</p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-black px-4 py-2 font-semibold text-white shadow-main hover:opacity-90 disabled:opacity-60"
        >
          {{ loading ? 'Saving…' : 'Set new password' }}
        </button>
      </form>
    </div>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiResetPassword } from '@/api/password';
import { apiLogin } from '@/api/auth';          
import { setToken } from '@/services/tokenService';
import { useAuth } from '@/composables/useAuth';
import { toFriendlyError } from '@/utils/errorHandler';

const route = useRoute();
const router = useRouter();
const { refreshMe } = useAuth?.() ?? {};

const code = computed(() => route.query.code?.toString() || '');
const email = computed(() => route.query.email?.toString() || '');

const password = ref('');
const loading = ref(false);
const success = ref(false);
const error = ref('');
const passwordError = ref('');

document.title = 'Fantasy Academy | Reset Password';

function validate() {
  passwordError.value = '';
  if (!password.value || password.value.length < 6) {
    passwordError.value = 'Password must have at least 6 characters.';
  }
  return !passwordError.value;
}

async function onSubmit() {
  error.value = '';
  success.value = false;
  if (!validate()) return;

  if (!code.value) {
    error.value = 'Missing reset code in URL.';
    return;
  }

  try {
    loading.value = true;
    await apiResetPassword({ code: code.value, newPassword: password.value });
    success.value = true;

    if (email.value) {
      const { token } = await apiLogin({ email: email.value, password: password.value });
      setToken(token);
      await refreshMe?.();
      router.replace('/dashboard');
    } else {
      router.replace('/login');
    }
  } catch (e) {
    const fe = toFriendlyError(e);
    error.value = fe.userMessage || 'Reset failed. Please try again.';
  } finally {
    loading.value = false;
  }
}
</script>