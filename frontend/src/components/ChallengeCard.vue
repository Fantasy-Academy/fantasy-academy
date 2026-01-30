<template>
  <article class="flex flex-col overflow-hidden rounded-lg shadow-sm w-full">

    <!-- TOP IMAGE -->
    <div class="relative h-40 w-full overflow-hidden rounded-t-lg">
      <img v-if="resolvedImage(challenge)" :src="resolvedImage(challenge)" :alt="challenge.name"
        class="absolute inset-0 h-full w-full object-cover" loading="lazy" @error="onImgError" />

      <span v-else class="text-xs text-cool-gray absolute inset-0 flex items-center justify-center">
        No image
      </span>

      <!-- BADGES -->
      <div class="absolute top-2 right-2 z-10 flex flex-wrap gap-2 justify-end">

        <!-- Evaluated badge -->
        <span v-if="challenge.isEvaluated"
          class="px-3 py-1 bg-indigo-500/90 text-white rounded-full text-xs font-semibold">
          Evaluated
        </span>

        <!-- Completed badge -->
        <span v-if="challenge.isAnswered"
          class="px-3 py-1 bg-green-500/90 text-white rounded-full text-xs font-semibold">
          Completed
        </span>

        <!-- Expiring / Expired badge -->
        <span class="px-3 py-1 rounded-full text-xs font-semibold" :class="expirationText.includes('Expired')
          ? 'bg-vibrant-coral/90 text-white'
          : 'bg-golden-yellow/90 text-blue-black'">
          {{ expirationText }}
        </span>

      </div>
      <div class="absolute bottom-2 left-2 z-10 px-3 py-1 bg-dark-white/90 rounded-full text-xs font-semibold">
        {{ challenge.maxPoints }}FAPs
      </div>
    </div>

    <!-- CONTENT -->
    <div class="flex flex-row gap-4 p-4 bg-dark-white">
      <div class="w-20 h-20 rounded overflow-hidden flex-shrink-0">
        <img v-if="resolvedImage(challenge)" :src="resolvedImage(challenge)" :alt="challenge.name"
          class="h-full w-full object-cover" loading="lazy" @error="onImgError" />
        <span v-else class="text-xs text-cool-gray">No image</span>
      </div>

      <div class="flex flex-col gap-1 flex-1">
        <h1 class="font-bold line-clamp-1">{{ challenge.name }}</h1>
        <h2 class="text-sm text-blue-black/70 line-clamp-2" v-html="challenge.description"></h2>
      </div>
    </div>

    <!-- CTA -->
    <div class="p-4">
      <button class="w-full text-center rounded-lg font-semibold cursor-pointer
         bg-dark-purple text-white          
         px-3 py-2 shadow-sm transition duration-240

         hover:bg-gradient-to-r             
         hover:from-dark-purple
         hover:to-light-purple
         hover:animate-gradient
         hover:text-white" @click="$emit('select', challenge.id)">
        Open Challenge
      </button>
    </div>

  </article>
</template>

<script setup>
import { computed } from 'vue';
import { resolvedImage, onImgError } from '../utils/imageHelpers.js';
import ExpiredIcon from '../assets/expiredTime.svg';
import ActiveIcon from '../assets/activeTime.svg';


const props = defineProps({
  challenge: {
    type: Object,
    required: true,
  },
});

// počítá text expirace
const expirationText = computed(() => {
  if (!props.challenge.expiresAt) return '';

  const now = new Date();
  const exp = new Date(props.challenge.expiresAt);

  const diffMs = exp.getTime() - now.getTime();
  const diffMinutes = Math.round(diffMs / 60000);
  const diffHours = Math.round(diffMs / 3600000);
  const diffDays = Math.round(diffMs / 86400000);

  if (diffMs > 0) {
    // ještě neexpiruje
    if (diffMinutes < 60) return `Expires in ${diffMinutes} min`;
    if (diffHours < 24) return `Expires in ${diffHours} h`;
    return `Expires in ${diffDays} d`;
  } else {
    // už expirovala
    const pastMinutes = Math.abs(diffMinutes);
    const pastHours = Math.abs(diffHours);
    const pastDays = Math.abs(diffDays);

    if (pastMinutes < 60) return `Expired ${pastMinutes} min ago`;
    if (pastHours < 24) return `Expired ${pastHours} h ago`;
    return `Expired ${pastDays} d ago`;
  }
});
</script>