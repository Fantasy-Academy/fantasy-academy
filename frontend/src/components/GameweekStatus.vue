<template>
  <div class="rounded-xl bg-golden-yellow p-4 shadow-sm border border-charcoal/10">
    <p class="text-sm text-blue-black font-alexandria">Gameweek</p>

    <p v-if="loading" class="text-blue-black">Loading...</p>

    <p v-else-if="currentGameweek" class="text-lg font-semibold text-blue-black">
      Current gameweek: {{ currentGameweek.number }}
    </p>

    <p v-else-if="nextGameweek" class="text-lg font-semibold text-blue-black">
      Next gameweek starts soon: GW {{ nextGameweek.number }}
    </p>

    <p v-else class="text-sm text-blue-black">No active gameweek.</p>
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

// 🔥 Tohle ti chybělo:
onMounted(() => {
  loadGameweeks();
});
</script>