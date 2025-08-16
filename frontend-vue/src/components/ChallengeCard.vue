<template>
  <article
    class="flex gap-4 p-4 border rounded shadow hover:shadow-lg cursor-pointer transition bg-white"
    role="button"
    :aria-label="challenge?.name || 'Challenge'"
    @click="$emit('select', challenge)"
  >
    <!-- Thumbnail -->
    <div class="w-24 h-24 shrink-0 overflow-hidden rounded bg-gray-100 flex items-center justify-center">
      <img
        v-if="imgSrc"
        :src="imgSrc"
        :alt="challenge?.name || 'Challenge image'"
        class="w-full h-full object-cover"
        loading="lazy"
        @error="onImgError"
      />
      <span v-else class="text-xs text-gray-500">No image</span>
    </div>

    <!-- Text -->
    <div class="flex-1 min-w-0">
      <h3 class="text-lg font-semibold truncate">{{ challenge.name }}</h3>
      <p class="text-gray-600 line-clamp-2">
        {{ challenge.shortDescription }}
      </p>

      <!-- Meta (volitelné) -->
      <div class="mt-2 text-xs text-gray-500 flex items-center gap-2">
        <span v-if="challenge.maxPoints != null">{{ challenge.maxPoints }} pts</span>
        <span v-if="challenge.isAnswered" class="px-2 py-0.5 rounded bg-green-50 text-green-700">Odpovězeno</span>
        <span v-else-if="challenge.isExpired" class="px-2 py-0.5 rounded bg-red-50 text-red-700">Expirovaná</span>
      </div>
    </div>

    <span class="self-center text-blue-600 font-medium">Zobrazit &rarr;</span>
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

/** Absolutní URL obrázku (nebo null) */
const imgSrc = computed(() => resolvedImage(props.challenge));
</script>