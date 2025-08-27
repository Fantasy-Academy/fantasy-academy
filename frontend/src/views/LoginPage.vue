<template>
  <section class="login-page">
    <LoginForm />
  </section>
</template>

<script>
import LoginForm from '../components/LoginForm.vue';

document.title = 'Fantasy Academy | Signin';

export default {
  name: 'LoginPage',
  components: { LoginForm },
};
</script>