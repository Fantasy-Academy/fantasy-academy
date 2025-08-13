<template>
  <form @submit.prevent="onSubmit">
    <input type="email" v-model="email" placeholder="Email" />
    <input type="password" v-model="password" placeholder="Password" />

    <button type="submit" :disabled="loading">Login</button>

    <p v-if="error" role="alert" style="color:red">{{ error }}</p>
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