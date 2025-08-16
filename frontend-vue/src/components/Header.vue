<template>
  <header class="bg-blue-black text-dark-white shadow-main">
    <nav class="max-w-6xl mx-auto flex justify-between items-center px-4 py-3 gap-8">
      <!-- Logo -->
      <router-link
        to="/"
        class="text-2xl font-bebas-neue tracking-wide hover:text-golden-yellow transition"
      >
        Fantasy Academy
      </router-link>

      <!-- Navigace -->
      <ul class="flex gap-6 items-center font-alexandria text-sm">
        <li>
          <router-link
            to="/"
            class="hover:text-vibrant-coral transition"
            active-class="text-vibrant-coral font-semibold"
          >
            Home
          </router-link>
        </li>
        <li>
          <router-link
            to="/challenges"
            class="hover:text-vibrant-coral transition"
            active-class="text-vibrant-coral font-semibold"
          >
            Challenges
          </router-link>
        </li>

        <!-- Auth states -->
        <template v-if="!isAuthenticated">
          <li>
            <router-link
              to="/login"
              class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold"
            >
              Login
            </router-link>
          </li>
          <li>
            <router-link
              to="/signup"
              class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold"
            >
              Signup
            </router-link>
          </li>
        </template>

        <template v-else>
          <li>
            <router-link
              to="/dashboard"
              class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold"
            >
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link
              to="/profile"
              class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold"
            >
              Profile
            </router-link>
          </li>
          <li>
            <button
              @click="handleLogout"
              class="bg-vibrant-coral px-3 py-1 rounded-lg text-white font-semibold hover:bg-vibrant-coral/80 shadow-sm transition"
            >
              Logout
            </button>
          </li>
        </template>
      </ul>
    </nav>
  </header>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import { useRouter } from 'vue-router';

const { isAuthenticated, logout } = useAuth();
const router = useRouter();

function handleLogout() {
  logout();
  router.push('/login');
}
</script>