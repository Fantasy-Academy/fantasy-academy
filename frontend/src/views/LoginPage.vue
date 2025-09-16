<template>
  <AuthLayout>
    <section class="login-page">
      <LoginForm />
    </section>
  </AuthLayout>
</template>

<script>
import LoginForm from '../components/LoginForm.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

document.title = 'Fantasy Academy | Signin';

export default {
  name: 'LoginPage',
  components: { LoginForm, AuthLayout },
};
</script>