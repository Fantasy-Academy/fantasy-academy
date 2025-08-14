<template>
  <form class="space-y-4" @submit.prevent="handleRegister" novalidate>
    <!-- Jméno -->
    <div>
      <label class="block text-sm font-medium mb-1">Jméno</label>
      <input v-model.trim="name" type="text" placeholder="Tvoje jméno"
             class="w-full rounded-lg border px-3 py-2"
             :class="{'border-red-500': errors.name}" />
      <p v-if="errors.name" class="text-sm text-red-600">{{ errors.name }}</p>
    </div>

    <!-- E-mail -->
    <div>
      <label class="block text-sm font-medium mb-1">E-mail</label>
      <input v-model.trim="email" type="email" placeholder="name@example.com"
             class="w-full rounded-lg border px-3 py-2"
             :class="{'border-red-500': errors.email}" />
      <p v-if="errors.email" class="text-sm text-red-600">{{ errors.email }}</p>
    </div>

    <!-- Heslo -->
    <div>
      <label class="block text-sm font-medium mb-1">Heslo</label>
      <input v-model="password" type="password" placeholder="Min. 6 znaků, velké písmeno a číslo"
             class="w-full rounded-lg border px-3 py-2"
             :class="{'border-red-500': errors.password}" />
      <p v-if="errors.password" class="text-sm text-red-600">{{ errors.password }}</p>
    </div>

    <!-- Potvrzení hesla -->
    <div>
      <label class="block text-sm font-medium mb-1">Potvrzení hesla</label>
      <input v-model="confirm" type="password" placeholder="Zadej heslo znovu"
             class="w-full rounded-lg border px-3 py-2"
             :class="{'border-red-500': errors.confirm}" />
      <p v-if="errors.confirm" class="text-sm text-red-600">{{ errors.confirm }}</p>
    </div>

    <!-- Chyby -->
    <p v-if="formError" class="text-sm text-red-600">{{ formError }}</p>
    <ul v-if="serverViolations.length" class="text-sm text-red-600 list-disc pl-5">
      <li v-for="(v, i) in serverViolations" :key="i">
        <strong>{{ v.propertyPath || 'Chyba' }}:</strong> {{ v.message }}
      </li>
    </ul>

    <!-- Tlačítko -->
    <button type="submit"
            :disabled="loading"
            class="w-full rounded-lg bg-blue-600 py-2 font-semibold text-white hover:bg-blue-700 disabled:opacity-60">
      {{ loading ? 'Vytvářím účet…' : 'Vytvořit účet' }}
    </button>
  </form>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';
import { useValidation } from '@/composables/useValidation';

const name = ref('');
const email = ref('');
const password = ref('');
const confirm = ref('');
const errors = ref({ name: '', email: '', password: '', confirm: '' });
const formError = ref('');
const serverViolations = ref([]);

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
    await register(name.value, email.value, password.value);
    router.push(route.query.redirect || '/dashboard');
  } catch (e) {
    if (e?.data?.violations?.length) {
      serverViolations.value = e.data.violations;
    } else if (e?.message) {
      formError.value = e.message;
    } else {
      formError.value = 'Registrace selhala.';
    }
  }
}
</script>