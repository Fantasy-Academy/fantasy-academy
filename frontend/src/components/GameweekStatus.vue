<template>
  <div class="rounded-xl text-white  bg-light-purple p-4 shadow-sm border border-charcoal/10 ">
    <p class="text-sm font-alexandria">Gameweek</p>

    <p v-if="loading">Loading...</p>

    <template v-else-if="currentGameweek">
      <p class="text-lg font-semibold">
        Current gameweek: {{ currentGameweek.number }}
      </p>
      <p class="text-sm">
        Ends at: {{ formatDateTime(currentGameweek.endsAt) }}
      </p>
    </template>

    <template v-else-if="nextGameweek">
      <p class="text-lg font-semibold">
        Next gameweek: GW {{ nextGameweek.number }}
      </p>
      <p class="text-sm text-blue-black/80">
        Starts at: {{ formatDateTime(nextGameweek.startsAt) }}
      </p>
    </template>

    <p v-else class="text-sm">
      No active gameweek.
    </p>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { useGameweek } from '@/composables/useGameweek';

const {
  currentGameweek,
  nextGameweek,
  loading,
  loadGameweeks,
} = useGameweek();

onMounted(loadGameweeks);

function formatDateTime(iso) {
  if (!iso) return '—';
  return new Date(iso).toLocaleString('cs-CZ', {
    dateStyle: 'medium',
    timeStyle: 'short',
  });
}
</script>