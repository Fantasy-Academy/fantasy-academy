<template>
  <div id="app">
    <!-- Hlavní navigace -->
    <nav>
      <router-link to="/">Home</router-link>
      <router-link v-if="!isAuthenticated" to="/login">Login</router-link>
      <router-link v-if="isAuthenticated" to="/dashboard">Dashboard</router-link>
      <button v-if="isAuthenticated" @click="logout">Logout</button>
    </nav>

    <!-- Hlavní obsah aplikace -->
    <main>
      <router-view />
    </main>
  </div>
</template>

<script>
import { useAuth } from './composables/useAuth';

export default {
  name: 'App',
  setup() {
    const { isAuthenticated, logout } = useAuth();

    return {
      isAuthenticated,
      logout,
    };
  },
};
</script>