<template>
  <header class="bg-dark-white/70 backdrop-blur-md rounded-lg mx-4 my-2 border border-white/30">
    <nav class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">

      <!-- LEFT — Logo -->
      <router-link to="/"
        class="text-3xl text-dark-purple font-bebas-neue tracking-wide hover:text-light-purple transition">
        Fantasy Academy
      </router-link>

      <!-- CENTER — Desktop Nav (BEZE ZMĚNY) -->
      <ul class="hidden md:flex flex-1 justify-center items-center gap-6 font-alexandria text-md">
        <li>
          <router-link to="/" class="flex items-center gap-2 hover:text-light-purple transition"
            active-class="text-light-purple font-semibold">
            <img src="@/assets/home.svg" class="w-3 h-3" />
            <span>Home</span>
          </router-link>
        </li>
        <li>
          <router-link to="/challenges" class="flex items-center gap-2 hover:text-light-purple transition"
            active-class="text-light-purple font-semibold">
            <img src="@/assets/challenges.svg" class="w-3 h-3" />
            <span>Challenges</span>
          </router-link>
        </li>
        <template v-if="isAuthenticated">
          <li>
            <router-link to="/dashboard" class="flex items-center gap-2 hover:text-light-purple transition"
              active-class="text-light-purple font-semibold">
              <img src="@/assets/dashboard.svg" class="w-3 h-3" />
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/leaderboard" class="flex items-center gap-2 hover:text-light-purple transition"
              active-class="text-light-purple font-semibold">
              <img src="@/assets/leaderboard.svg" class="w-3 h-3" />
              <span>Leaderboard</span>
            </router-link>
          </li>
        </template>
      </ul>

      <!-- RIGHT — Desktop Auth (BEZE ZMĚNY) -->
      <div class="hidden md:flex items-center gap-3">
        <template v-if="!isAuthenticated">
          <router-link to="/login">
            <button class="rounded-lg text-white font-semibold animate-gradient
              bg-[linear-gradient(270deg,var(--color-light-purple),var(--color-dark-purple),var(--color-vibrant-coral))]
              bg-[length:200%_200%] shadow-sm transition px-4 py-2">
              Sign in
            </button>
          </router-link>
        </template>

        <template v-else>
          <router-link to="/profile" class="flex items-center gap-2">
            <AvatarInitial v-if="user" :name="user.name" gradient size="md" />
          </router-link>

          <div class="flex flex-col leading-tight">
            <router-link to="/profile" class="font-semibold text-blue-black hover:text-light-purple transition">
              {{ user.name }}
            </router-link>
            <span @click="handleLogout" class="text-sm text-light-purple cursor-pointer hover:underline">
              Logout
            </span>
          </div>
        </template>
      </div>

      <!-- 🍔 Burger RIGHT -->
      <button class="md:hidden ml-2 p-2 rounded-lg hover:bg-dark-white/10" @click="mobileOpen = !mobileOpen"
        aria-label="Toggle menu">
        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </nav>

    <!-- 📱 Mobile Menu (VĚTŠÍ & TOUCH FRIENDLY) -->
    <transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0" leave-active-class="transition duration-100 ease-in"
      leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 -translate-y-2">
      <div v-if="mobileOpen" class="md:hidden border-t border-dark-white/10 bg-dark-white/95 backdrop-blur">
        <ul class="flex flex-col gap-2 px-5 py-5 font-alexandria text-lg">

          <template v-if="isAuthenticated">
            <li class="flex items-center gap-4 px-4 py-4 border-b border-dark-white/10 mb-2">
              <AvatarInitial :name="user.name" size="md" gradient />
              <div class="flex flex-col">
                <span class="font-semibold text-blue-black text-lg">{{ user.name }}</span>
                <span @click="handleLogout" class="text-sm text-light-purple cursor-pointer hover:underline">
                  Logout
                </span>
              </div>
            </li>
          </template>

          <li><router-link to="/" @click="closeMobile" class="mobile-link">Home</router-link></li>
          <li><router-link to="/challenges" @click="closeMobile" class="mobile-link">Challenges</router-link></li>

          <template v-if="!isAuthenticated">
            <li><router-link to="/login" @click="closeMobile" class="mobile-link">Login</router-link></li>
            <li><router-link to="/signup" @click="closeMobile" class="mobile-link">Signup</router-link></li>
          </template>

          <template v-else>
            <li><router-link to="/dashboard" @click="closeMobile" class="mobile-link">Dashboard</router-link></li>
            <li><router-link to="/leaderboard" @click="closeMobile" class="mobile-link">Leaderboard</router-link></li>
            <li><router-link to="/profile" @click="closeMobile" class="mobile-link">Profile</router-link></li>
          </template>

        </ul>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import AvatarInitial from './ui/AvatarInitial.vue'

const { isAuthenticated, logout, user } = useAuth()
const router = useRouter()
const route = useRoute()
const mobileOpen = ref(false)

function handleLogout() {
  logout()
  mobileOpen.value = false
  router.push('/login')
}

function closeMobile() {
  mobileOpen.value = false
}

watch(() => route.fullPath, () => mobileOpen.value = false)
</script>

<style scoped>
.mobile-link {
  @apply block px-4 py-4 rounded-xl hover:bg-dark-white/10 active:scale-[0.98] transition;
}
</style>