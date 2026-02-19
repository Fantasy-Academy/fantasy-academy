<template>
  <div class="w-full min-w-0 flex flex-col gap-3
           px-6 py-4">
    <!-- Loading -->
    <p v-if="loading" class="text-sm text-cool-gray">
      Loading…
    </p>

    <!-- CURRENT GAMEWEEK -->
    <template v-else-if="currentGameweek">
      <div class="flex flex-col gap-1">
        <p class="text-base font-semibold text-blue-black">
          Current gameweek · GW {{ currentGameweek.number }}
        </p>

        <p class="text-sm text-cool-gray">
          Ends in
          <span class="font-medium text-blue-black">
            {{ timeRemaining }}
          </span>
        </p>
      </div>

      <!-- Progress bar -->
      <div class="mt-2 w-full h-2 rounded-full bg-dark-white overflow-hidden">
        <div class="h-full bg-light-purple transition-all duration-500" :style="{ width: progressPercent + '%' }" />
      </div>

      <p class="text-xs text-cool-gray">
        {{ progressPercent.toFixed(1) }} % has passed
      </p>
    </template>

    <!-- NEXT GAMEWEEK -->
    <template v-else-if="nextGameweek">
      <div class="flex flex-col gap-1">
        <p class="text-base font-semibold text-blue-black">
          Next gameweek · GW {{ nextGameweek.number }}
        </p>

        <p class="text-sm text-cool-gray">
          Starts in
          <span class="font-medium text-blue-black">
            {{ timeUntilStart }}
          </span>
        </p>
      </div>

      <div class="mt-2 w-full h-2 rounded-full bg-dark-white overflow-hidden">
        <div class="h-full bg-light-purple/70 transition-all duration-500"
          :style="{ width: startProgressPercent + '%' }" />
      </div>
    </template>

    <!-- FALLBACK -->
    <p v-else class="text-sm text-cool-gray">
      No active gameweek.
    </p>
  </div>
</template>

<script setup>
import { onMounted, onBeforeUnmount, computed, ref } from 'vue'
import { useGameweek } from '@/composables/useGameweek'

const {
  currentGameweek,
  nextGameweek,
  loading,
  loadGameweeks,
} = useGameweek()

const now = ref(Date.now())
let timer = null

onMounted(() => {
  loadGameweeks()
  timer = setInterval(() => {
    now.value = Date.now()
  }, 1000)
})

onBeforeUnmount(() => {
  clearInterval(timer)
})

/* -----------------------------
   CURRENT GAMEWEEK HELPERS
-------------------------------- */
const timeRemaining = computed(() => {
  if (!currentGameweek.value?.endsAt) return '—'
  return formatDuration(
    new Date(currentGameweek.value.endsAt).getTime() - now.value
  )
})

const progressPercent = computed(() => {
  if (!currentGameweek.value) return 0

  const start = new Date(currentGameweek.value.startsAt).getTime()
  const end = new Date(currentGameweek.value.endsAt).getTime()
  const total = end - start
  const elapsed = now.value - start

  return clamp((elapsed / total) * 100)
})

/* -----------------------------
   NEXT GAMEWEEK HELPERS
-------------------------------- */
const timeUntilStart = computed(() => {
  if (!nextGameweek.value?.startsAt) return '—'
  return formatDuration(
    new Date(nextGameweek.value.startsAt).getTime() - now.value
  )
})

const startProgressPercent = computed(() => {
  if (!nextGameweek.value) return 0

  const start = new Date(nextGameweek.value.startsAt).getTime()
  const prevEnd = start - 1000 * 60 * 60 * 24

  const total = start - prevEnd
  const elapsed = now.value - prevEnd

  return clamp((elapsed / total) * 100)
})

/* -----------------------------
   UTILS
-------------------------------- */
function formatDuration(ms) {
  if (ms <= 0) return '0s'

  const totalSeconds = Math.floor(ms / 1000)
  const days = Math.floor(totalSeconds / 86400)
  const hours = Math.floor((totalSeconds % 86400) / 3600)
  const minutes = Math.floor((totalSeconds % 3600) / 60)
  const seconds = totalSeconds % 60

  if (days > 0) return `${days}d ${hours}h`
  if (hours > 0) return `${hours}h ${minutes}m`
  if (minutes > 0) return `${minutes}m ${seconds}s`
  return `${seconds}s`
}

function clamp(val) {
  return Math.min(100, Math.max(0, val))
}
</script>