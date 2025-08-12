<template>
  <section class="login-page">
    <h1>Login</h1>
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