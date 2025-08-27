<template>
  <div class="flex flex-col min-h-screen">
    <Header />

    <main class="flex-grow max-w-6xl mx-auto p-4">
      <slot />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
</script>