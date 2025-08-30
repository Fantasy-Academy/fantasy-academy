<!-- src/components/LoginForm.vue -->
<template>
  <section class="mx-auto w-full max-w-md px-4 py-8">
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <header class="mb-6">
        <h1 class="font-bebas-neue text-4xl tracking-wide text-blue-black">Sign in</h1>
        <p class="mt-1 text-sm font-alexandria text-cool-gray">
          Welcome back! Enter your credentials to continue.
        </p>
      </header>

      <form @submit.prevent="onSubmit" class="space-y-4" novalidate>
        <!-- Email -->
        <div>
          <label for="email" class="mb-1 block text-sm font-medium text-blue-black">Email</label>
          <input id="email" name="email" type="email" v-model.trim="email" autocomplete="email"
            placeholder="name@example.com"
            class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 font-alexandria text-blue-black placeholder:cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40" />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="mb-1 block text-sm font-medium text-blue-black">Password</label>
          <div class="relative">
            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" v-model="password"
              autocomplete="current-password" placeholder="••••••••"
              class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 pr-10 font-alexandria text-blue-black placeholder:cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40" />
            <button type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 rounded px-2 text-sm text-cool-gray hover:text-blue-black"
              @click="showPassword = !showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'">
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>
        </div>

        <!-- Error -->
        <p v-if="formError" role="alert"
          class="rounded-md border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
          {{ formError }}
        </p>

        <!-- Submit -->
        <button type="submit" :disabled="authLoading"
          class="w-full rounded-lg bg-vibrant-coral py-2 font-alexandria font-semibold text-white shadow-sm transition hover:bg-vibrant-coral/90 disabled:opacity-60">
          {{ authLoading ? 'Signing in…' : 'Sign in' }}
        </button>
      </form>

      <div class="mt-6 flex flex-col items-center gap-2 text-sm font-alexandria">
        <router-link to="/forgot-password" class="text-blue-black hover:underline">Forgot password?</router-link>
        <p class="text-cool-gray">
          Don’t have an account?
          <router-link to="/signup" class="font-semibold text-vibrant-coral hover:underline">
            Create one
          </router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useValidation } from '@/composables/useValidation';
import { toFriendlyError } from '@/utils/errorHandler';

const { validateLoginForm } = useValidation(); // očekává { email, password } → '' | chybová hláška

const { login, loading: authLoading } = useAuth();
const router = useRouter();
const route = useRoute();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const formError = ref('');

async function onSubmit() {
  const em = String(email.value ?? '').trim();
  const pw = String(password.value ?? '');

  console.log('[LoginForm] submit payload preview:', { email: em, hasPw: pw.length > 0 });

  const msg = validateLoginForm({ email: em, password: pw });
  formError.value = msg;
  if (msg) return;

  try {
    const me = await login(em, pw);
    const target = (route.query.redirect && String(route.query.redirect)) || '/dashboard';
    if (me) router.push(target);
  } catch (e) {
    formError.value = toFriendlyError(e).userMessage;
  }
}
</script>