<template>
  <div>
    <label class="block font-medium mb-2">{{ question.text }}</label>
    <ul>
      <li
        v-for="(opt, idx) in modelValue"
        :key="opt.id"
        class="border rounded px-3 py-2 mb-1 bg-gray-100 cursor-move"
      >
        {{ opt.label }}
      </li>
    </ul>
    <p class="text-sm text-gray-500 mt-1">*(Drag & drop řazení zde může být později doplněno)</p>
  </div>
</template>

<script setup>
defineProps({
  question: Object,
  modelValue: Array
});
defineEmits(['update:modelValue']);
</script>