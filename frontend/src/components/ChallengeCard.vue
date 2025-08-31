<template>
  <article
    class="flex flex-col sm:flex-row gap-5 rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm transition hover:shadow-main cursor-pointer"
    @click="$emit('select', challenge.id)">
    <!-- Image -->
    <div class="flex items-center justify-center overflow-hidden rounded-xl bg-dark-white aspect-square 
         w-full max-w-[120px] max-h-[120px] sm:w-28 sm:h-28 shrink-0 mx-auto sm:mx-0">
      <img v-if="resolvedImage(challenge)" :src="resolvedImage(challenge)" :alt="challenge.name"
        class="h-full w-full object-contain" loading="lazy" @error="onImgError" />
      <span v-else class="text-xs text-cool-gray">No image</span>
    </div>

    <!-- Content -->
    <div class="flex-1 min-w-0 flex flex-col">
      <h3 class="truncate font-alexandria text-lg font-bold text-blue-black mb-1">
        {{ challenge.name }}
      </h3>
      <p class="line-clamp-3 text-sm text-cool-gray mb-3">
        {{ challenge.shortDescription || '—' }}
      </p>

      <div class="mt-auto flex flex-wrap items-center gap-3 text-xs text-cool-gray">
        <span v-if="challenge.maxPoints != null" class="font-semibold text-blue-black">
          {{ challenge.maxPoints }} pts
        </span>

        <span v-if="challenge.isAnswered" class="rounded-full bg-pistachio/20 px-3 py-1 text-pistachio font-semibold">
          Answered
        </span>
        <span v-else-if="challenge.isExpired"
          class="rounded-full bg-vibrant-coral/10 px-3 py-1 text-vibrant-coral font-semibold">
          Expired
        </span>
        <span v-else class="rounded-full bg-golden-yellow/20 px-3 py-1 text-golden-yellow font-semibold">
          Active
        </span>

        <!-- Expiration info -->
        <span class="ml-auto text-xs italic text-cool-gray">
          {{ expirationText }}
        </span>
      </div>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { resolvedImage, onImgError } from '../utils/imageHelpers.js';

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