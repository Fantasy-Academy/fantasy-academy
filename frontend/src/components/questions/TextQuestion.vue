<template>
  <div>
    <label class="block font-medium mb-1">{{ question.text }}</label>
    <input
      type="text"
      v-model="localValue"
      class="w-full border rounded px-3 py-2"
    />
    <p v-if="question.hint" class="text-sm text-gray-500 mt-1">{{ question.hint }}</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  question: Object,
  modelValue: String
});
const emit = defineEmits(['update:modelValue']);

const localValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
});
</script>