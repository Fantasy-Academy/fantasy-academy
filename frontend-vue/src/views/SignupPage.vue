<template>
  <div class="auth-wrap">
    <h1>Registrace</h1>

    <form class="auth-form" @submit.prevent="handleRegister" novalidate>
      <label>
        Jméno
        <input
          v-model.trim="name"
          type="text"
          name="name"
          placeholder="Tvoje jméno"
          autocomplete="name"
          required
        />
      </label>

      <label>
        E‑mail
        <input
          v-model.trim="email"
          type="email"
          name="email"
          placeholder="name@example.com"
          autocomplete="email"
          required
        />
      </label>

      <label>
        Heslo
        <input
          v-model="password"
          type="password"
          name="password"
          placeholder="Min. 8 znaků"
          autocomplete="new-password"
          minlength="8"
          required
        />
      </label>

      <label>
        Potvrzení hesla
        <input
          v-model="confirm"
          type="password"
          name="confirm"
          placeholder="Zadej heslo znovu"
          autocomplete="new-password"
          minlength="8"
          required
        />
      </label>

      <!-- klientské chyby -->
      <p v-if="formError" class="error">{{ formError }}</p>

      <!-- serverová obecná chyba z useAuth -->
      <p v-if="error" class="error">{{ error }}</p>

      <!-- serverové detailní chyby (422 violations) -->
      <ul v-if="serverViolations.length" class="error-list">
        <li v-for="(v, i) in serverViolations" :key="i">
          <strong>{{ v.propertyPath || 'Chyba' }}:</strong> {{ v.message }}
        </li>
      </ul>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Vytvářím účet…' : 'Vytvořit účet' }}
      </button>
    </form>

    <p class="note">
      Máš už účet?
      <router-link to="/login">Přihlásit se</router-link>
    </p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const name = ref('');
const email = ref('');
const password = ref('');
const confirm = ref('');
const formError = ref('');
const serverViolations = ref([]); // ⬅️ pro zobrazení detailních chyb z API

const { register, loading, error } = useAuth();
const route = useRoute();
const router = useRouter();

function isValidEmail(v) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
}

async function handleRegister() {
  formError.value = '';
  serverViolations.value = [];

  // jednoduchá klientská validace
  if (!name.value.trim()) {
    formError.value = 'Jméno je povinné.';
    return;
  }
  if (!isValidEmail(email.value)) {
    formError.value = 'Neplatný e‑mail.';
    return;
  }
  if ((password.value || '').length < 8) {
    formError.value = 'Heslo musí mít alespoň 8 znaků.';
    return;
  }
  if (password.value !== confirm.value) {
    formError.value = 'Hesla se neshodují.';
    return;
  }

  try {
    await register(name.value, email.value, password.value);
    router.push(route.query.redirect || '/dashboard');
  } catch (e) {
    // Z `http.js` může přijít err.status a err.data (vč. violations / detail)
    console.error('[Register] Error detail:', e);
    // hezčí zobrazení detailu
    if (e?.data?.violations?.length) {
      serverViolations.value = e.data.violations;
    } else if (e?.message) {
      formError.value = e.message; // např. "Email already in use" nebo "Bad Request"
    } else {
      formError.value = 'Registrace selhala.';
    }
  }
}
</script>

<style scoped>
.auth-wrap {
  max-width: 420px;
  margin: 3rem auto;
  padding: 1.25rem;
}
.auth-form {
  display: grid;
  gap: 0.9rem;
}
label {
  display: grid;
  gap: 0.4rem;
  font-weight: 600;
}
input {
  padding: 0.6rem 0.75rem;
  border: 1px solid #dadada;
  border-radius: 8px;
  font: inherit;
}
button {
  margin-top: 0.4rem;
  padding: 0.7rem 1rem;
  border: 0;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
}
button[disabled] {
  opacity: 0.6;
  cursor: not-allowed;
}
.error {
  color: #c0392b;
  font-size: 0.95rem;
}
.error-list {
  color: #c0392b;
  font-size: 0.95rem;
  padding-left: 1.2rem;
}
.note {
  margin-top: 1rem;
  text-align: center;
}
</style>