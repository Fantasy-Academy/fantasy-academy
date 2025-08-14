<template>
  <div id="app">
    <!-- Hlavní navigace -->
    <nav>
      <router-link to="/">Home</router-link>
      <router-link v-if="!isAuthenticated" to="/login">Login</router-link>
      <router-link v-if="!isAuthenticated" to="/signup">Signup</router-link>
      <router-link v-if="isAuthenticated" to="/dashboard">Dashboard</router-link>
      <router-link v-if="isAuthenticated" to="/profile">Profile</router-link>
      <button v-if="isAuthenticated" @click="handleLogout">Logout</button>
    </nav>

    <!-- Hlavní obsah aplikace -->
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import { useAuth } from './composables/useAuth';
import { useRouter } from 'vue-router';

export default {
  name: 'App',
  setup() {
    const router = useRouter();
    const { isAuthenticated, logout } = useAuth();

    function handleLogout() {
      logout();
      router.push('/login');
    }

    return {
      isAuthenticated,
      handleLogout,
      logout,
    };
  },
};
</script>