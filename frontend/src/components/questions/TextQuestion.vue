<template>
  <div>
    <label class="block font-medium mb-1">{{ question.text }}</label>

    <input type="text" v-model="localValue" class="w-full border rounded px-3 py-2" />

    <!-- 🔹 Popis otázky (pokud existuje) -->
    <p v-if="question.description" class="mt-1 text-sm text-cool-gray">
      {{ question.description }}
    </p>

    <!-- 🔹 Obrázek otázky (pokud existuje) -->
    <img v-if="question.image" :src="question.image" alt=""
      class="mt-2 max-h-32 w-full object-contain rounded-lg border border-charcoal/10" />

    <p v-if="question.hint" class="text-sm text-gray-500 mt-1 hint-content" v-html="question.hint"></p>

  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  question: {
    type: Object,
    default: () => ({})
  },
  modelValue: String
})
const emit = defineEmits(['update:modelValue'])

const localValue = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})
</script>