<template>
  <div class="mx-auto max-w-md px-4 py-10">
    <h1 class="mb-6 text-2xl font-semibold">Signup</h1>
    <SignupForm />
    <p class="mt-6 text-center text-sm">
      Already have an account?
      <router-link class="text-blue-600 hover:underline" to="/login">
        Login
      </router-link>
    </p>
  </div>
</template>

<script setup>
import SignupForm from '../components/SignupForm.vue';
</script>