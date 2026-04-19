<script setup>
import { computed } from 'vue';
const props = defineProps({ status: Object, loading: Boolean });
const emit = defineEmits(['manage']);

function formatDate(iso) {
  if (!iso) return '—';
  return new Date(iso).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
}

const renewsLabel = computed(() =>
  props.status?.willCancelAtPeriodEnd ? 'Cancels on' : 'Renews on'
);
</script>

<template>
  <div class="rounded-2xl border-2 border-charcoal bg-white p-8 shadow-main">
    <template v-if="status">
      <div class="flex items-center gap-3 mb-6">
        <span class="inline-block w-2.5 h-2.5 rounded-full"
          :class="{
            'bg-pistachio': status.isActive && !status.willCancelAtPeriodEnd,
            'bg-golden-yellow': status.willCancelAtPeriodEnd,
            'bg-vibrant-coral': !status.isActive,
          }"
        ></span>
        <span class="font-nunito font-bold text-blue-black capitalize">
          {{ status.willCancelAtPeriodEnd ? 'Canceling' : status.status ?? 'Active' }}
        </span>
      </div>

      <dl class="space-y-3 text-sm font-source-sans-3">
        <div class="flex justify-between">
          <dt class="text-cool-gray">{{ renewsLabel }}</dt>
          <dd class="font-semibold text-blue-black">{{ formatDate(status.currentPeriodEnd) }}</dd>
        </div>
      </dl>

      <button
        @click="emit('manage')"
        :disabled="loading"
        class="mt-6 w-full border-2 border-charcoal text-charcoal font-nunito font-bold py-3 rounded-xl hover:bg-dark-white transition disabled:opacity-50"
      >
        Manage Subscription
      </button>
    </template>

    <div v-else-if="loading" class="h-6 bg-dark-white rounded animate-pulse w-1/2"></div>
  </div>
</template>