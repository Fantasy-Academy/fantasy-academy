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
import { validateLoginForm } from '../validators/authValidators';

export default {
  name: 'LoginForm',
  setup() {
    const { login, loading, error } = useAuth();
    const email = ref('');
    const password = ref('');

    const onSubmit = async () => {
      error.value = validateLoginForm({ email: email.value, password: password.value });
      if (error.value) return;

      try {
        await login(email.value, password.value);
        // redirect: router.push({ name: 'Dashboard' })
      } catch {
        error.value = 'Incorrect email or password';
      }
    };

    return { email, password, onSubmit, loading, error };
  },
};
</script>