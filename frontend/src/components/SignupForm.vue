<!-- src/components/SignupForm.vue -->
<template>
  <section class="mx-auto w-full max-w-md px-4 py-8 sm:py-10">
    <div class="overflow-hidden rounded-[2rem] border border-dark-purple/10 bg-white shadow-sm">
      <div class="h-2 w-full"></div>

      <div class="p-6 sm:p-8">
        <header class="mb-8 text-center">
          <p class="font-alexandria text-xs uppercase tracking-[0.3em] text-cool-gray">
            Join Fantasy Academy
          </p>
          <h1 class="mt-3 font-bebas-neue text-4xl tracking-wide text-blue-black sm:text-5xl">
            Create account
          </h1>
          <p class="mt-2 text-sm font-alexandria text-cool-gray">
            Create your profile and start earning points today.
          </p>
        </header>

        <form class="space-y-5" @submit.prevent="handleRegister" novalidate>
          <!-- Name -->
          <div>
            <label for="name" class="mb-2 block text-sm font-medium text-blue-black">
              Name
            </label>
            <input
              id="name"
              v-model.trim="name"
              type="text"
              placeholder="Your name"
              class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
              :class="{ 'border-dark-purple': !!errors.name }"
            />
            <p
              v-if="errors.name"
              class="mt-2 rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
            >
              {{ errors.name }}
            </p>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="mb-2 block text-sm font-medium text-blue-black">
              Email
            </label>
            <input
              id="email"
              v-model.trim="email"
              type="email"
              autocomplete="email"
              placeholder="name@example.com"
              class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
              :class="{ 'border-dark-purple': !!errors.email }"
            />
            <p
              v-if="errors.email"
              class="mt-2 rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
            >
              {{ errors.email }}
            </p>
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
                v-model="password"
                autocomplete="new-password"
                placeholder="Min. 6 chars, one uppercase and a number"
                class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 pr-16 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
                :class="{ 'border-dark-purple': !!errors.password }"
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
            <p
              v-if="errors.password"
              class="mt-2 rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
            >
              {{ errors.password }}
            </p>
          </div>

          <!-- Confirm Password -->
          <div>
            <label for="confirm" class="mb-2 block text-sm font-medium text-blue-black">
              Confirm password
            </label>
            <div class="relative">
              <input
                :type="showConfirm ? 'text' : 'password'"
                id="confirm"
                v-model="confirm"
                autocomplete="new-password"
                placeholder="Re-enter your password"
                class="w-full rounded-xl border border-charcoal/15 bg-dark-white px-4 py-3 pr-16 font-alexandria text-blue-black placeholder:text-cool-gray/70 outline-none transition focus:border-dark-purple focus:ring-2 focus:ring-dark-purple/20"
                :class="{ 'border-dark-purple': !!errors.confirm }"
              />
              <button
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md px-2 py-1 text-sm font-medium text-dark-purple transition hover:bg-dark-purple/5 hover:text-blue-black"
                @click="showConfirm = !showConfirm"
                :aria-label="showConfirm ? 'Hide password' : 'Show password'"
              >
                {{ showConfirm ? 'Hide' : 'Show' }}
              </button>
            </div>
            <p
              v-if="errors.confirm"
              class="mt-2 rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
            >
              {{ errors.confirm }}
            </p>
          </div>

          <!-- Server / form errors -->
          <p
            v-if="formError"
            class="rounded-xl border border-dark-purple/20 bg-light-purple/20 px-4 py-3 text-sm font-alexandria text-dark-purple"
          >
            {{ formError }}
          </p>

          <ul
            v-if="serverViolations.length"
            class="list-disc space-y-1 rounded-xl border border-dark-purple/20 bg-light-purple/20 px-5 py-4 text-sm font-alexandria text-dark-purple"
          >
            <li v-for="(v, i) in serverViolations" :key="i">
              <strong>{{ v.propertyPath || 'Error' }}:</strong> {{ v.message }}
            </li>
          </ul>

          <!-- Submit -->
          <button
            type="submit"
            :disabled="loading"
            class="w-full cursor-pointer rounded-xl bg-dark-purple py-3 font-alexandria font-semibold text-white transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            {{ loading ? 'Creating account…' : 'Create account' }}
          </button>
        </form>

        <div class="mt-8 flex flex-col items-center gap-3 text-sm font-alexandria">
          <p class="text-cool-gray">
            Already have an account?
            <router-link
              to="/login"
              class="font-semibold text-dark-purple transition hover:underline"
            >
              Sign in
            </router-link>
          </p>
        </div>
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