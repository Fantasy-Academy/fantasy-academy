<template>
  <div>
    <label class="block font-medium mb-1">{{ question.text }}</label>
    <input
      type="number"
      v-model.number="localValue"
      class="w-full border rounded px-3 py-2"
    />
          <!-- 🔹 Popis (pokud existuje) -->
      <p v-if="opt.description" class="mt-1 text-sm text-cool-gray">
        {{ opt.description }}
      </p>

      <!-- 🔹 Obrázek (pokud existuje) -->
      <img
        v-if="opt.image"
        :src="opt.image"
        alt=""
        class="mt-2 max-h-32 w-full object-contain rounded-lg border border-charcoal/10"
      />
    <p v-if="question.hint" class="text-sm text-gray-500 mt-1">{{ question.hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  question: Object,
  modelValue: Number
});
const emit = defineEmits(['update:modelValue']);

const localValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>