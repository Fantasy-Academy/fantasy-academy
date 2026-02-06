<template>
  <!-- full-bleed wrapper -->
  <div class="min-h-screen w-full flex flex-col">
    <div class="sticky top-0 z-50">
      <Header />
    </div>

    <!-- content area: úzké na mobilech, bez limitu od md+ -->
    <main class="flex-grow w-full mx-auto min-w-0
             px-4 sm:px-6 md:px-8 lg:px-12
             max-w-[640px] md:max-w-none">
      <slot />
    </main>

    <Footer />
  </div>
</template>

<script setup>
import Header from '../components/Header.vue';
import Footer from '../components/Footer.vue';
</script>