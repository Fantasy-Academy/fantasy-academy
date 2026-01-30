<template>
  <div>
    <label class="block font-medium mb-2">{{ question.text }}</label>

    <ul class="select-none">
      <li v-for="(opt, idx) in items" :key="opt.id"
        class="border rounded px-3 py-2 mb-1 bg-gray-100 flex flex-col gap-2" draggable="true"
        @dragstart="onDragStart(idx, $event)" @dragover.prevent="onDragOver(idx, $event)" @dragleave="onDragLeave(idx)"
        @drop.prevent="onDrop(idx)" :class="{
          'opacity-70': draggingIndex === idx,
          'ring-2 ring-blue-400': dragOverIndex === idx
        }">
        <div class="flex items-center gap-3">
          <span class="cursor-grab text-gray-500" title="Přetáhni pro změnu pořadí">⋮⋮</span>
          <span class="flex-1">{{ opt.label }}</span>
          <div class="flex items-center gap-1">
            <button type="button" class="px-2 py-1 text-xs rounded border hover:bg-gray-50" :disabled="idx === 0"
              @click="move(idx, idx - 1)" title="Posunout nahoru">▲</button>
            <button type="button" class="px-2 py-1 text-xs rounded border hover:bg-gray-50"
              :disabled="idx === items.length - 1" @click="move(idx, idx + 1)" title="Posunout dolů">▼</button>
          </div>
        </div>

        <!-- 🔹 Popis a obrázek patří sem, k danému opt -->
        <p v-if="opt.description" class="mt-1 text-sm text-cool-gray">
          {{ opt.description }}
        </p>
        <img v-if="opt.image" :src="opt.image" alt=""
          class="mt-2 max-h-32 w-full object-contain rounded-lg border border-charcoal/10" />
      </li>
    </ul>
    <p v-if="question.hint" class="text-sm text-gray-500 mt-1 hint-content" v-html="question.hint"></p>
    <p class="text-sm text-gray-500 mt-1">
      Move the options or change their order using the ▲▼ buttons
    </p>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  question: { type: Object, required: true },
  modelValue: { type: Array, default: () => [] }
})
const emit = defineEmits(['update:modelValue'])

const items = ref([...props.modelValue])

watch(
  () => props.modelValue,
  val => { items.value = [...val] },
  { deep: true }
)

function commit() {
  emit('update:modelValue', [...items.value])
}

function move(from, to) {
  if (from === to || to < 0 || to >= items.value.length) return
  const copy = [...items.value]
  const [it] = copy.splice(from, 1)
  copy.splice(to, 0, it)
  items.value = copy
  commit()
}

const draggingIndex = ref(null)
const dragOverIndex = ref(null)

function onDragStart(idx, e) {
  draggingIndex.value = idx
  dragOverIndex.value = null
  e.dataTransfer?.setData('text/plain', String(idx))
  e.dataTransfer?.setDragImage?.(createGhost(items.value[idx].label), 0, 0)
}

function onDragOver(idx) {
  dragOverIndex.value = idx
}

function onDragLeave(idx) {
  if (dragOverIndex.value === idx) dragOverIndex.value = null
}

function onDrop(idx) {
  const from = draggingIndex.value
  const to = idx
  draggingIndex.value = null
  dragOverIndex.value = null
  if (from == null || to == null) return
  move(from, to)
}

function createGhost(text = '') {
  const el = document.createElement('div')
  el.style.padding = '6px 10px'
  el.style.background = '#e5e7eb'
  el.style.border = '1px solid #d1d5db'
  el.style.borderRadius = '6px'
  el.style.fontSize = '12px'
  el.style.color = '#111827'
  el.style.position = 'absolute'
  el.style.top = '-9999px'
  el.textContent = text || 'Přesunout'
  document.body.appendChild(el)
  return el
}
</script>