<template>
  <div>
    <h1>Welcome, {{ username }}</h1>
    <div v-if="isAdmin">
      <!-- Render admin-specific features -->
    </div>
    <div v-else>
      <!-- Render user-specific features -->
    </div>
  </div>
</template>

<script>
export default {
  computed: {
    username() {
      // Get username from authentication state
    },
    isAdmin() {
      // Check if user has admin role
    }
  }
};
</script>