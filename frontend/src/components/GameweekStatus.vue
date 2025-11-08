<template>
  <div class="rounded-xl bg-golden-yellow p-4 shadow-sm border border-charcoal/10">
    <p class="text-sm text-blue-black font-alexandria">Gameweek</p>

    <p v-if="currentGameweek !== null" class="text-lg font-semibold text-blue-black">
      Current gameweek: {{ currentGameweek }}
    </p>

    <p v-else-if="nextGameweek !== null" class="text-lg font-semibold text-blue-black">
      Next gameweek starts soon: GW {{ nextGameweek }}
    </p>

    <p v-else class="text-sm text-blue-black">No active gameweek.</p>
  </div>
</template>

<script setup>
import { useGameweek } from '@/composables/useGameweek';

const { currentGameweek, nextGameweek } = useGameweek();
</script>