<template>
  <div
    class="grid place-items-center rounded-full font-semibold select-none"
    :class="[sizeClass, bgClass, textClass]"
  >
    {{ initials }}
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  name: { type: String, default: '' },
  size: { type: String, default: 'md' }, // sm | md | lg | xl
  highlight: { type: Boolean, default: false },
  gradient: { type: Boolean, default: false }, // 🔥 NEW
});

// 🔠 Inicialy
const initials = computed(() => {
  if (!props.name) return '?';
  const parts = props.name.trim().split(/\s+/);
  const first = parts[0]?.[0] || '';
  const last = parts.length > 1 ? parts[parts.length - 1][0] || '' : '';
  return (first + last).toUpperCase();
});

// 📏 Velikosti
const sizeClass = computed(() => {
  return {
    sm: 'h-8 w-8 text-sm',
    md: 'h-12 w-12 text-base',
    lg: 'h-16 w-16 text-xl',
    xl: 'h-20 w-20 text-2xl',
  }[props.size];
});

// 🎨 Pozadí
const bgClass = computed(() => {
  if (props.gradient) {
    return 'text-white bg-[linear-gradient(270deg,var(--color-light-purple),var(--color-dark-purple),var(--color-vibrant-coral))] bg-[length:200%_200%] animate-gradient';
  }
  if (props.highlight) {
    return 'bg-golden-yellow text-blue-black';
  }
  return 'bg-dark-white text-blue-black';
});

const textClass = 'font-bebas-neue tracking-wide';
</script>