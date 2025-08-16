<template>
  <article
    class="flex gap-4 p-4 border border-charcoal/10 rounded-xl bg-white shadow-sm hover:shadow-main cursor-pointer transition"
    role="button"
    :aria-label="challenge?.name || 'Challenge'"
    @click="$emit('select', challenge)"
  >
    <!-- Thumbnail -->
    <div class="w-24 h-24 shrink-0 overflow-hidden rounded bg-dark-white grid place-items-center">
      <img
        v-if="imgSrc"
        :src="imgSrc"
        :alt="challenge?.name || 'Challenge image'"
        class="w-full h-full object-cover"
        loading="lazy"
        @error="onImgError"
      />
      <span v-else class="text-xs text-cool-gray">No image</span>
    </div>

    <!-- Text -->
    <div class="flex-1 min-w-0">
      <h3 class="truncate text-lg font-semibold text-blue-black font-alexandria">
        {{ challenge.name }}
      </h3>
      <p class="text-sm text-cool-gray line-clamp-2">
        {{ challenge.shortDescription || '—' }}
      </p>

      <!-- Meta -->
      <div class="mt-2 flex items-center gap-2 text-xs text-cool-gray">
        <span v-if="challenge.maxPoints != null">{{ challenge.maxPoints }} pts</span>
        <span
          v-if="challenge.isAnswered"
          class="px-2 py-0.5 rounded bg-pistachio/20 text-pistachio"
        >
          Answered
        </span>
        <span
          v-else-if="challenge.isExpired"
          class="px-2 py-0.5 rounded bg-vibrant-coral/10 text-vibrant-coral"
        >
          Expired
        </span>
      </div>
    </div>

    <span class="self-center text-vibrant-coral font-semibold font-alexandria">
      View &rarr;
    </span>
  </article>
</template>

<script setup>
import { computed } from 'vue';
import { resolvedImage, onImgError } from '@/utils/imageHelpers';

const props = defineProps({
  challenge: {
    type: Object,
    required: true,
  },
});

defineEmits(['select']);

const imgSrc = computed(() => resolvedImage(props.challenge));
</script>