<!-- src/components/SignupForm.vue -->
<template>
  <section class="mx-auto w-full max-w-md px-4 py-8">
    <!-- Card -->
    <div class="rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <!-- Header -->
      <header class="mb-6">
        <h1 class="font-bebas-neue text-4xl tracking-wide text-blue-black">Create account</h1>
        <p class="mt-1 text-sm font-alexandria text-cool-gray">
          Join Fantasy Academy and start earning points today.
        </p>
      </header>

      <!-- Form -->
      <form class="space-y-4" @submit.prevent="handleRegister" novalidate>
        <!-- Name -->
        <div>
          <label for="name" class="mb-1 block text-sm font-medium text-blue-black">Name</label>
          <input
            id="name"
            v-model.trim="name"
            type="text"
            placeholder="Your name"
            class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40"
            :class="{ 'border-vibrant-coral': !!errors.name }"
          />
          <p v-if="errors.name" class="mt-1 rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
            {{ errors.name }}
          </p>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="mb-1 block text-sm font-medium text-blue-black">Email</label>
          <input
            id="email"
            v-model.trim="email"
            type="email"
            autocomplete="email"
            placeholder="name@example.com"
            class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40"
            :class="{ 'border-vibrant-coral': !!errors.email }"
          />
          <p v-if="errors.email" class="mt-1 rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
            {{ errors.email }}
          </p>
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="mb-1 block text-sm font-medium text-blue-black">Password</label>
          <div class="relative">
            <input
              :type="showPassword ? 'text' : 'password'"
              id="password"
              v-model="password"
              autocomplete="new-password"
              placeholder="Min. 6 chars, one uppercase and a number"
              class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 pr-10 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40"
              :class="{ 'border-vibrant-coral': !!errors.password }"
            />
            <button
              type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 rounded px-2 text-sm text-cool-gray hover:text-blue-black"
              @click="showPassword = !showPassword"
              :aria-label="showPassword ? 'Hide password' : 'Show password'"
            >
              {{ showPassword ? 'Hide' : 'Show' }}
            </button>
          </div>
          <p v-if="errors.password" class="mt-1 rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
            {{ errors.password }}
          </p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label for="confirm" class="mb-1 block text-sm font-medium text-blue-black">Confirm password</label>
          <div class="relative">
            <input
              :type="showConfirm ? 'text' : 'password'"
              id="confirm"
              v-model="confirm"
              autocomplete="new-password"
              placeholder="Re-enter your password"
              class="w-full rounded-lg border border-charcoal/20 bg-white px-3 py-2 pr-10 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none ring-0 focus:border-golden-yellow focus:ring-2 focus:ring-golden-yellow/40"
              :class="{ 'border-vibrant-coral': !!errors.confirm }"
            />
            <button
              type="button"
              class="absolute right-2 top-1/2 -translate-y-1/2 rounded px-2 text-sm text-cool-gray hover:text-blue-black"
              @click="showConfirm = !showConfirm"
              :aria-label="showConfirm ? 'Hide password' : 'Show password'"
            >
              {{ showConfirm ? 'Hide' : 'Show' }}
            </button>
          </div>
          <p v-if="errors.confirm" class="mt-1 rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
            {{ errors.confirm }}
          </p>
        </div>

        <!-- Server / form errors -->
        <p v-if="formError" class="rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-2 text-sm font-alexandria text-vibrant-coral">
          {{ formError }}
        </p>
        <ul
          v-if="serverViolations.length"
          class="list-disc space-y-1 rounded border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-sm font-alexandria text-vibrant-coral"
        >
          <li v-for="(v, i) in serverViolations" :key="i">
            <strong>{{ v.propertyPath || 'Error' }}:</strong> {{ v.message }}
          </li>
        </ul>

        <!-- Submit -->
        <button
          type="submit"
          :disabled="loading"
          class="w-full rounded-lg bg-vibrant-coral py-2 font-alexandria font-semibold text-white shadow-sm transition hover:bg-vibrant-coral/90 disabled:opacity-60 cursor-pointer"
        >
          {{ loading ? 'Creating account…' : 'Create account' }}
        </button>
      </form>

      <!-- Footer links -->
      <div class="mt-6 flex flex-col items-center gap-2 text-sm font-alexandria">
        <p class="text-cool-gray">
          Already have an account?
          <router-link to="/login" class="font-semibold text-vibrant-coral hover:underline">
            Sign in
          </router-link>
        </p>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useValidation } from '@/composables/useValidation';
import { toFriendlyError } from '@/utils/errorHandler';

const name = ref('');
const email = ref('');
const password = ref('');
const confirm = ref('');
const errors = ref({ name: '', email: '', password: '', confirm: '' });
const formError = ref('');
const serverViolations = ref([]);

const showPassword = ref(false);
const showConfirm = ref(false);

const { register, loading } = useAuth();
const { validateSignup } = useValidation();
const route = useRoute();
const router = useRouter();

async function handleRegister() {
  formError.value = '';
  serverViolations.value = [];

  errors.value = validateSignup({
    name: name.value,
    email: email.value,
    password: password.value,
    confirm: confirm.value
  });

  if (Object.values(errors.value).some(Boolean)) return;

  try {
    // trim pro jistotu před odesláním
    await register(name.value.trim(), email.value.trim(), password.value);
    router.push(route.query.redirect || '/dashboard');
  } catch (e) {
    const fe = toFriendlyError(e);

    // pokud API poslalo detailní porušení (např. Symfony/Api Platform "violations")
    const v =
      e?.data?.violations ||
      fe.details?.violations ||
      [];

    if (Array.isArray(v) && v.length) {
      serverViolations.value = v;
      formError.value = ''; // necháme jen pole porušení
    } else {
      formError.value = fe.userMessage || 'Registration failed.';
    }
  }
}
</script>