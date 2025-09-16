<template>
  <AuthLayout>
    <SignupForm />
  </AuthLayout>
</template>

<script setup>
import SignupForm from '../components/SignupForm.vue';
import AuthLayout from '../layouts/AuthLayout.vue';

document.title = 'Fantasy Academy | Signup';
</script>