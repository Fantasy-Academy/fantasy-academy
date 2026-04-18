<script setup>
defineProps({
  plan: String,
  title: String,
  price: String,
  period: String,
  badge: String,
  loading: Boolean,
});
const emit = defineEmits(['select']);
</script>

<template>
  <div class="relative rounded-2xl bg-dark-white p-8 flex flex-col gap-10">
    <span v-if="badge" class="absolute top-4 right-4 bg-golden-yellow text-blue-black text-xs font-nunito font-bold px-3 py-1 rounded-full">
      {{ badge }}
    </span>
    <div>
      <h2 class="font-bebas-neue text-3xl text-blue-black">{{ title }}</h2>
      <div class="mt-2 flex items-end gap-1">
        <span class="font-bebas-neue text-5xl text-dark-purple">${{ price }}</span>
        <span class="text-cool-gray font-source-sans-3 mb-1">/ {{ period }}</span>
      </div>
    </div>
    <button
      :disabled="loading"
      @click="emit('select', plan)"
      class="w-full bg-dark-purple text-white font-nunito font-bold py-3 rounded-xl hover:opacity-90 transition disabled:opacity-50 disabled:cursor-not-allowed"
    >
      {{ loading ? 'Processing…' : `Get ${title}` }}
    </button>
  </div>
</template>