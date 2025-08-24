<template>
  <section class="mx-auto max-w-md px-4 py-10">
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <header class="mb-6">
        <h1 class="font-bebas-neue text-3xl tracking-wide text-blue-black">Reset password</h1>
        <p class="mt-1 text-sm font-alexandria text-cool-gray">
          Enter your new password to complete the reset process.
        </p>
      </header>

      <!-- Success -->
      <div
        v-if="success"
        class="mb-4 rounded-xl border border-pistachio/30 bg-pistachio/10 p-3 text-pistachio"
      >
        Password has been changed. You can now
        <router-link to="/login" class="underline">log in</router-link>.
      </div>

      <!-- Error -->
      <div
        v-if="formError"
        class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral"
      >
        {{ formError }}
      </div>

      <!-- Form -->
      <form @submit.prevent="onSubmit" novalidate class="space-y-4" v-if="!success">
        <div>
          <label class="mb-1 block text-sm font-medium text-blue-black">New password</label>
          <input
            type="password"
            v-model="password"
            placeholder="Min. 6 chars, uppercase and number"
            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-golden-yellow"
            :class="passwordError ? 'border-vibrant-coral' : 'border-charcoal/20'"
            autocomplete="new-password"
          />
          <p v-if="passwordError" class="mt-1 text-sm text-vibrant-coral">{{ passwordError }}</p>
        </div>

        <div>
          <label class="mb-1 block text-sm font-medium text-blue-black">Confirm password</label>
          <input
            type="password"
            v-model="confirm"
            placeholder="Repeat new password"
            class="w-full rounded-lg border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-golden-yellow"
            :class="confirmError ? 'border-vibrant-coral' : 'border-charcoal/20'"
            autocomplete="new-password"
          />
          <p v-if="confirmError" class="mt-1 text-sm text-vibrant-coral">{{ confirmError }}</p>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-blue-black px-4 py-2 font-semibold text-white shadow-main hover:opacity-90 disabled:opacity-60 cursor-pointer"
        >
          {{ loading ? 'Changing…' : 'Change password' }}
        </button>
      </form>

      <div class="mt-6 flex items-center justify-between text-sm">
        <router-link to="/forgot-password" class="font-alexandria text-blue-black hover:underline">
          Request a new code
        </router-link>
        <router-link to="/login" class="font-alexandria text-vibrant-coral hover:underline">
          Back to login
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute } from 'vue-router';
import { apiResetPassword } from '@/api/password';

document.title = 'Fantasy Academy | Reset Password';

const route = useRoute();
const code = route.query.code || ''; // ← kód z URL

const password = ref('');
const confirm = ref('');
const loading = ref(false);
const success = ref(false);

const formError = ref('');
const passwordError = ref('');
const confirmError = ref('');

function isStrongPassword(v) {
  return /^(?=.*[A-Z])(?=.*\d).{6,}$/.test(v);
}

async function onSubmit() {
  formError.value = '';
  passwordError.value = '';
  confirmError.value = '';

  if (!password.value) passwordError.value = 'Enter a new password.';
  else if (!isStrongPassword(password.value))
    passwordError.value = 'Min. 6 chars, uppercase and number.';

  if (!confirm.value) confirmError.value = 'Confirm the new password.';
  else if (password.value !== confirm.value)
    confirmError.value = 'Passwords do not match.';

  if (passwordError.value || confirmError.value) return;

  loading.value = true;
  try {
    await apiResetPassword({ code, newPassword: password.value });
    success.value = true;
  } catch (e) {
    formError.value = e?.message || 'Failed to reset password.';
  } finally {
    loading.value = false;
  }
}
</script>