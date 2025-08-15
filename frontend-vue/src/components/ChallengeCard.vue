<template>
  <div
    class="p-4 border rounded-lg shadow-sm hover:shadow-md cursor-pointer flex justify-between items-center transition"
    @click="$emit('select', challenge)"
  >
    <div>
      <h3 class="text-lg font-semibold">{{ challenge.title }}</h3>
      <p class="text-sm text-gray-600 line-clamp-2">{{ challenge.description }}</p>
    </div>
    <span class="text-blue-600 font-medium">Zobrazit &rarr;</span>
  </div>
</template>

<script setup>
defineProps({
  challenge: {
    type: Object,
    required: true,
  },
});
</script>