<template>
  <header class="bg-gray-800 text-white">
    <nav class="max-w-6xl mx-auto flex justify-between items-center p-4">
      <!-- Logo -->
      <router-link to="/" class="text-lg font-bold hover:text-gray-300">
        MyApp
      </router-link>

      <!-- Navigace -->
      <ul class="flex gap-4 items-center">
        <li><router-link to="/" class="hover:text-gray-300">Home</router-link></li>
        <li v-if="!isAuthenticated"><router-link to="/login" class="hover:text-gray-300">Login</router-link></li>
        <li v-if="!isAuthenticated"><router-link to="/signup" class="hover:text-gray-300">Signup</router-link></li>
        <li v-if="isAuthenticated"><router-link to="/dashboard" class="hover:text-gray-300">Dashboard</router-link></li>
        <li v-if="isAuthenticated"><router-link to="/profile" class="hover:text-gray-300">Profile</router-link></li>
        <li v-if="isAuthenticated">
          <button @click="handleLogout" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
            Logout
          </button>
        </li>
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