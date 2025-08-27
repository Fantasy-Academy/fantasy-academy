<template>
  <div>
    <label class="block font-medium mb-2">{{ question.text }}</label>
    <div v-for="opt in question.options" :key="opt.id" class="flex items-center mb-1">
      <input
        type="radio"
        :name="'q' + question.id"
        :value="opt.value"
        v-model="localValue"
        class="mr-2"
      />
      <span>{{ opt.label }}</span>
    </div>
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