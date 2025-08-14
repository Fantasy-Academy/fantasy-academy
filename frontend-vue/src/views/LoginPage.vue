<template>
  <section class="login-page">
    <h1 class="mb-6 text-2xl font-semibold">Login</h1>
    <LoginForm />
  </section>
</template>

<script>
import LoginForm from '../components/LoginForm.vue';

export default {
  name: 'LoginPage',
  components: { LoginForm },
};
</script>