<!-- src/components/subscription/UpgradePrompt.vue -->
<script setup>
defineProps({
  message: {
    type: String,
    default: 'This feature is available for Premium members.',
  },
});
</script>

<template>
  <div class="relative rounded-2xl overflow-hidden">

    <!-- Blurred slot content (the locked feature preview) -->
    <div class="select-none pointer-events-none blur-sm opacity-40 max-h-48 overflow-hidden">
      <slot />
    </div>

    <!-- Overlay -->
    <div class="absolute inset-0 flex flex-col items-center justify-center bg-white/80 backdrop-blur-sm p-6 text-center">
      <span class="text-3xl mb-3">🔒</span>
      <p class="font-nunito font-bold text-blue-black mb-4">{{ message }}</p>
      <router-link
        to="/subscription"
        class="bg-dark-purple text-white font-nunito font-bold px-6 py-2.5 rounded-xl shadow-main hover:opacity-90 transition text-sm"
      >
        Unlock Premium
      </router-link>
    </div>

  </div>
</template>