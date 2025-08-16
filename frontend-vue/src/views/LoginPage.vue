<template>
  <section class="login-page">
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