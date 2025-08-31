<template>
  <header class="bg-blue-black text-dark-white shadow-main">
    <nav class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3 gap-4">
      <!-- Logo -->
      <router-link to="/" class="text-2xl font-bebas-neue tracking-wide hover:text-golden-yellow transition">
        Fantasy Academy
      </router-link>

      <!-- Desktop nav -->
      <ul class="hidden md:flex items-center gap-6 font-alexandria text-sm">
        <li>
          <router-link to="/" class="hover:text-vibrant-coral transition"
            active-class="text-vibrant-coral font-semibold">
            Home
          </router-link>
        </li>
        <li>
          <router-link to="/challenges" class="hover:text-vibrant-coral transition"
            active-class="text-vibrant-coral font-semibold">
            Challenges
          </router-link>
        </li>
        <template v-if="!isAuthenticated">
          <li>
            <router-link to="/login" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              Login
            </router-link>
          </li>
          <li>
            <router-link to="/signup" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              Signup
            </router-link>
          </li>
        </template>

        <template v-else>
          <li>
            <router-link to="/dashboard" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              Dashboard
            </router-link>
          </li>
          <li>
            <router-link to="/leaderboard" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              Leaderboard
            </router-link>
          </li>
          <li>
            <router-link to="/myanswers" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              My Answers
            </router-link>
          </li>
          <li>
            <router-link to="/profile" class="hover:text-vibrant-coral transition"
              active-class="text-vibrant-coral font-semibold">
              Profile
            </router-link>
          </li>
          <li>
            <button @click="handleLogout"
              class="bg-vibrant-coral px-3 py-1 rounded-lg text-white font-semibold hover:bg-vibrant-coral/80 shadow-sm transition">
              Logout
            </button>
          </li>
        </template>
      </ul>

      <!-- Hamburger (mobile only) -->
      <button
        class="md:hidden inline-flex items-center justify-center rounded-lg p-2 hover:bg-dark-white/10 focus:outline-none focus:ring-2 focus:ring-golden-yellow/60"
        :aria-expanded="mobileOpen.toString()" aria-controls="mobile-menu" aria-label="Toggle navigation"
        @click="mobileOpen = !mobileOpen">
        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
          stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </nav>

    <!-- Mobile panel -->
    <transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
      <div v-if="mobileOpen" id="mobile-menu"
        class="md:hidden border-t border-dark-white/10 bg-blue-black/95 backdrop-blur">
        <ul class="flex flex-col gap-1 px-4 py-3 font-alexandria text-sm">
          <li>
            <router-link to="/" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
              active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
              Home
            </router-link>
          </li>
          <li>
            <router-link to="/challenges" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
              active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
              Challenges
            </router-link>
          </li>
          <template v-if="!isAuthenticated">
            <li>
              <router-link to="/login" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                Login
              </router-link>
            </li>
            <li>
              <router-link to="/signup" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                Signup
              </router-link>
            </li>
          </template>

          <template v-else>
            <li>
              <router-link to="/dashboard" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                Dashboard
              </router-link>
            </li>
            <li>
              <router-link to="/leaderboard" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                Leaderboard
              </router-link>
            </li>
            <li>
              <router-link to="/myanswers" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                My Answers
              </router-link>
            </li>
            <li>
              <router-link to="/profile" class="block rounded-lg px-3 py-2 hover:bg-dark-white/10 transition"
                active-class="bg-dark-white/10 text-vibrant-coral font-semibold" @click="closeMobile">
                Profile
              </router-link>
            </li>
            <li class="pt-1">
              <button @click="handleLogout"
                class="w-full text-left rounded-lg bg-vibrant-coral px-3 py-2 font-semibold text-white hover:bg-vibrant-coral/85 shadow-sm transition">
                Logout
              </button>
            </li>
          </template>
        </ul>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '@/composables/useAuth';

const { isAuthenticated, logout } = useAuth();
const router = useRouter();
const route = useRoute();

const mobileOpen = ref(false);

function handleLogout() {
  logout();
  mobileOpen.value = false;
  router.push('/login');
}
function closeMobile() {
  mobileOpen.value = false;
}

// zavři menu při změně trasy (když uživatel klikne na link)
watch(
  () => route.fullPath,
  () => { mobileOpen.value = false; }
);
</script>