<template>
  <form @submit.prevent="onSubmit" class="max-w-sm mx-auto space-y-4 p-6">
    <!-- Email -->
    <div>
      <label class="block text-sm font-medium mb-1">E-mail</label>
      <input
        type="email"
        v-model="email"
        placeholder="name@example.com"
        class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      />
    </div>

    <!-- Password -->
    <div>
      <label class="block text-sm font-medium mb-1">Heslo</label>
      <input
        type="password"
        v-model="password"
        placeholder="••••••••"
        class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
      />
    </div>

    <!-- Error -->
    <p v-if="error" role="alert" class="text-sm text-red-600">{{ error }}</p>

    <!-- Submit -->
    <button
      type="submit"
      :disabled="loading"
      class="w-full rounded-lg bg-blue-600 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-60"
    >
      {{ loading ? 'Přihlašuji…' : 'Přihlásit se' }}
    </button>
  </form>
</template>

<script>
import { ref } from 'vue';
import { useAuth } from '../composables/useAuth';
import { useRouter } from 'vue-router';
import { validateLoginForm } from '../validators/authValidators';

export default {
  name: 'LoginForm',
  setup() {
    const { login, loading, error } = useAuth();
    const router = useRouter();
    const email = ref('');
    const password = ref('');

    const onSubmit = async () => {
      console.log('[LoginForm] Submit clicked', { email: email.value, password: password.value });

      error.value = validateLoginForm({ email: email.value, password: password.value });
      if (error.value) {
        console.warn('[LoginForm] Validation failed:', error.value);
        return;
      }

      try {
        console.log('[LoginForm] Calling login...');
        const user = await login(email.value, password.value);
        console.log('[LoginForm] Login successful, user:', user);
        router.push("/dashboard");

      } catch (err) {
        console.error('[LoginForm] Login failed:', err);
        error.value = 'Incorrect email or password';
      }
    };

    return { email, password, onSubmit, loading, error };
  },
};
</script>