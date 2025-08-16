<template>
  <section class="mx-auto max-w-md px-4 py-10">
    <!-- Card -->
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <header class="mb-6">
        <h1 class="font-bebas-neue text-3xl tracking-wide text-blue-black">Forgot Password</h1>
        <p class="mt-1 text-sm font-alexandria text-cool-gray">
          Enter your email and we’ll send you a reset code.
        </p>
      </header>

      <!-- Success -->
      <div
        v-if="success"
        class="mb-4 rounded-xl border border-pistachio/30 bg-pistachio/10 p-3 text-pistachio"
      >
        We’ve sent a reset code to <strong>{{ email }}</strong>. Check your inbox (and spam).
      </div>

      <!-- Error -->
      <div
        v-if="formError"
        class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral"
      >
        {{ formError }}
      </div>

      <!-- Form -->
      <form @submit.prevent="onSubmit" novalidate class="space-y-4">
        <div>
          <label class="mb-1 block text-sm font-medium text-blue-black">E-mail</label>
          <input
            v-model.trim="email"
            type="email"
            placeholder="name@example.com"
            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-golden-yellow"
            :class="emailError ? 'border-vibrant-coral' : 'border-charcoal/20'"
            autocomplete="email"
          />
          <p v-if="emailError" class="mt-1 text-sm text-vibrant-coral">{{ emailError }}</p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-black px-4 py-2 font-semibold text-white shadow-main hover:opacity-90 disabled:opacity-60"
        >
          {{ loading ? 'Sending…' : 'Send reset code' }}
        </button>
      </form>

      <!-- Links -->
      <div class="mt-6 flex items-center justify-between text-sm">
        <router-link to="/login" class="font-alexandria text-blue-black hover:underline">
          Back to login
        </router-link>
        <router-link to="/signup" class="font-alexandria text-vibrant-coral hover:underline">
          Create account
        </router-link>
      </div>
    </div>

    <!-- Tip box -->
    <p class="mx-auto mt-4 max-w-md text-center text-xs font-alexandria text-cool-gray">
      Once you have the code, go to the <router-link to="/reset-password" class="text-blue-black underline">Reset password</router-link> page to finish the process.
    </p>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { apiRequestResetCode } from '@/api/password';

const email = ref('');
const loading = ref(false);
const success = ref(false);
const formError = ref('');
const emailError = ref('');

function isValidEmail(v) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
}

async function onSubmit() {
  formError.value = '';
  emailError.value = '';
  success.value = false;

  if (!email.value) {
    emailError.value = 'Please enter your email.';
    return;
  }
  if (!isValidEmail(email.value)) {
    emailError.value = 'Invalid email format.';
    return;
  }

  loading.value = true;
  try {
    await apiRequestResetCode(email.value);
    success.value = true;
  } catch (e) {
    // API podle OpenAPI vrací 400/422 s detail/violations; zachytíme message z apiFetch
    formError.value = e?.message || 'Failed to request reset code.';
  } finally {
    loading.value = false;
  }
}
</script>