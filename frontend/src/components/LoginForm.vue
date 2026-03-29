<!-- src/components/LoginForm.vue -->
<template>
  <section class="mx-auto w-full max-w-md px-4 py-8 sm:py-10">
    <div class="overflow-hidden rounded-[2rem] border border-dark-purple/10 bg-white shadow-sm">
      <!-- top accent -->
      <div class="h-2 w-full"></div>

      <div class="p-6 sm:p-8">
        <header class="mb-8 text-center">
          <p class="font-alexandria text-xs uppercase tracking-[0.3em] text-cool-gray">
            Welcome back
          </p>
          <h1 class="mt-3 font-bebas-neue text-4xl tracking-wide text-blue-black sm:text-5xl">
            Sign in
          </h1>
          <p class="mt-2 text-sm font-alexandria text-cool-gray">
            Enter your credentials to continue to Fantasy Academy.
          </p>
        </header>

        <form @submit.prevent="onSubmit" class="space-y-5" novalidate>
          <!-- Email -->
          <div>
            <label for="email" class="mb-2 block text-sm font-medium text-blue-black">
              Email
            </label>
            <input
              id="email"
              name="email"
              type="email"
              v-model.trim="email"
              autocomplete="email"
              placeholder="name@example.com"
              class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
            />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="mb-2 block text-sm font-medium text-blue-black">
              Password
            </label>
            <div class="relative">
              <input
                :type="showPassword ? 'text' : 'password'"
                id="password"
                name="password"
                v-model="password"
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 pr-16 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-sm font-medium text-dark-purple transition hover:bg-dark-purple/5 hover:text-blue-black"
                @click="showPassword = !showPassword"
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
              >
                {{ showPassword ? 'Hide' : 'Show' }}
              </button>
            </div>
          </div>

          <!-- Error -->
          <p
            v-if="formError"
            role="alert"
            class="rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
          >
            {{ formError }}
          </p>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="authLoading"
            class="w-full rounded-xl bg-dark-purple py-3 font-alexandria font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ authLoading ? 'Signing in…' : 'Sign in' }}
          </button>
        </form>

        <div class="mt-8 flex flex-col items-center gap-3 text-sm font-alexandria">
          <router-link
            to="/forgot-password"
            class="text-dark-purple transition hover:underline"
          >
            Forgot password?
          </router-link>

          <p class="text-cool-gray">
            Don’t have an account?
            <router-link
              to="/signup"
              class="font-semibold text-dark-purple transition hover:underline"
            >
              Create one
            </router-link>
          </p>
        </div>
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