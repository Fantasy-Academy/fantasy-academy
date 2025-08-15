<template>
  <div
    v-if="show"
    class="fixed inset-0 bg-black/25 flex items-center justify-center z-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
      <button
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
        @click="$emit('close')"
      >
        ✕
      </button>
      <h2 class="text-2xl font-bold mb-4">{{ challenge.title }}</h2>
      <p class="mb-4 text-gray-700">{{ challenge.description }}</p>

      <div v-if="challenge.hint" class="p-3 bg-gray-100 rounded">
        <strong>Nápověda:</strong> {{ challenge.hint }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  challenge: Object,
});
</script>