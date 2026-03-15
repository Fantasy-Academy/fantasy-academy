<template>
    <section class="w-full px-6 py-5 flex flex-col gap-4">

        <!-- HEADER -->
        <div class="flex flex-wrap items-center justify-between gap-3">

            <div class="flex items-center gap-3">
                <span v-if="currentGameweek"
                    class="rounded-full bg-light-purple px-3 py-1 text-xs font-semibold text-white">
                    Gameweek {{ currentGameweek.number }}
                </span>

                <span v-else-if="nextGameweek"
                    class="rounded-full bg-light-purple/70 px-3 py-1 text-xs font-semibold text-white">
                    Next GW {{ nextGameweek.number }}
                </span>

                <span class="text-sm text-cool-gray font-alexandria">
                    {{ progressLabel }}
                </span>
            </div>

            <span class="text-sm text-blue-black">
                <p >Ends in: <span class="font-semibold">{{ timeLabel }}</span></p>
            </span>

        </div>

        <!-- PROGRESS BAR -->
        <div class="relative w-full h-4 rounded-full bg-white overflow-hidden border border-charcoal/10">

            <!-- animated gradient -->
            <div class="absolute left-0 top-0 h-full rounded-full
        bg-[linear-gradient(270deg,var(--color-light-purple),var(--color-dark-purple),var(--color-vibrant-coral))]
        bg-[length:200%_200%] animate-gradient
        transition-[width] duration-700 ease-out" :style="{ width: progress + '%' }" />

        </div>

        <!-- FOOTER -->
        <div class="flex justify-between text-xs text-cool-gray">
            <span v-if="nextGameweek && !currentGameweek">
                Starts {{ formatDate(nextGameweek.startsAt) }}
            </span>

        </div>

    </section>
</template>

<script setup>
import { onMounted, onBeforeUnmount, ref, computed } from 'vue'
import { useGameweek } from '@/composables/useGameweek'

const {
    currentGameweek,
    nextGameweek,
    loading,
    loadGameweeks
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
   PROGRESS
-------------------------------- */

const progress = computed(() => {

    if (currentGameweek.value) {

        const start = new Date(currentGameweek.value.startsAt).getTime()
        const end = new Date(currentGameweek.value.endsAt).getTime()

        const total = end - start
        const elapsed = now.value - start

        return clamp((elapsed / total) * 100)

    }

    if (nextGameweek.value) {

        const start = new Date(nextGameweek.value.startsAt).getTime()
        const prevEnd = start - 86400000

        const total = start - prevEnd
        const elapsed = now.value - prevEnd

        return clamp((elapsed / total) * 100)

    }

    return 0
})

/* -----------------------------
   LABELS
-------------------------------- */

const timeLabel = computed(() => {

    if (currentGameweek.value) {

        return formatDuration(
            new Date(currentGameweek.value.endsAt).getTime() - now.value
        )

    }

    if (nextGameweek.value) {

        return formatDuration(
            new Date(nextGameweek.value.startsAt).getTime() - now.value
        )

    }

    return '—'

})

const progressLabel = computed(() => {

    if (currentGameweek.value)
        return `${progress.value.toFixed(1)}% completed`

    if (nextGameweek.value)
        return `Starts soon`

    return ''

})

/* -----------------------------
   UTILS
-------------------------------- */

function formatDate(date) {
    return new Date(date).toLocaleDateString(undefined, {
        month: 'short',
        day: 'numeric'
    })
}

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

function clamp(v) {
    return Math.min(100, Math.max(0, v))
}
</script>