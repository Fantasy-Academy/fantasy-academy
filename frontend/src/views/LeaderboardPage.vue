<template>
  <section class="w-full max-w-6xl mx-auto px-4 py-8">
    <GameweekProgressBar :gameweek-number="currentGameweekNumber" :start="currentGameweekStart"
      :end="currentGameweekEnd" />
    <!-- Header -->
    <header class="mb-6 mt-6 rounded-lg sm:p-5 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <div
          class="inline-flex items-center rounded-full bg-light-purple/15 px-3 py-1 text-xs font-semibold text-light-purple mb-3">
          Season ranking
        </div>

        <h1 class="font-bold text-2xl sm:text-3xl text-blue-black">
          Leaderboard
        </h1>

        <p class="mt-1 text-sm sm:text-base text-cool-gray font-alexandria">
          Compare performance, points and challenge activity across all players.
        </p>
      </div>

      <div class="flex flex-wrap gap-2 sm:gap-3">

        <!-- Total players -->
        <div class="rounded-full bg-light-purple px-4 py-2 text-sm font-semibold text-white sm:px-5 sm:py-3">
          Players: {{ items.length }}
        </div>

        <!-- Your rank -->
        <div v-if="items.some(p => p.isMyself)"
          class="flex flex-row items-center gap-1 rounded-full bg-dark-purple/75 px-4 py-2  text-white text-sm font-semibold sm:px-5 sm:py-3">
          Your rank: {{items.find(p => p.isMyself)?.rank ?? '—'}}
          <img src="../assets/rank.svg" alt="rank" class="w-3 h-3" />

        </div>

      </div>
    </header>


    <!-- SEARCH -->
    <div class="mb-6">
      <input v-model="searchQuery" type="text" placeholder="Search player..."
        class="w-full rounded-lg border border-charcoal/10 bg-white px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-light-purple/40" />
    </div>

    <!-- States -->
    <div v-if="initialLoading" class="grid gap-4 grid-cols-1 sm:grid-cols-2">
      <div v-for="n in 6" :key="n" class="animate-pulse rounded-xl bg-dark-white p-4">
        <div class="flex items-center gap-4">
          <div class="h-12 w-12 rounded-full bg-white"></div>
          <div class="flex-1">
            <div class="h-4 w-40 rounded bg-white"></div>
            <div class="mt-2 h-3 w-24 rounded bg-white"></div>
          </div>
        </div>
      </div>
    </div>

    <p v-else-if="error"
      class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral">
      {{ error }}
    </p>

    <template v-else>
      <!-- Leaderboard list -->
      <div class="space-y-3">
        <button v-for="(p, i) in visibleItems" :key="p.playerId || i" type="button" @click="onRowClick(p.playerId)"
          :aria-label="`Open player ${p.playerName}`"
          class="w-full rounded-xl p-4 text-left transition hover:scale-[1.005] cursor-pointer" :class="p.isMyself
            ? 'bg-light-purple/10 border border-light-purple/30'
            : 'bg-dark-white'">
          <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <!-- LEFT -->
            <div class="flex items-center gap-4 min-w-0">
              <div
                class="grid h-12 w-12 flex-shrink-0 place-items-center rounded-full text-base font-bold text-blue-black"
                :class="badgeBg(p.rank, i)">
                {{ p.rank ?? i + 1 }}
              </div>

              <AvatarInitial :name="p.playerName" size="lg" :highlight="p.isMyself" />

              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <p class="truncate font-semibold text-blue-black text-base sm:text-lg">
                    {{ p.playerName }}
                  </p>

                  <span v-if="p.isMyself"
                    class="inline-flex items-center rounded-full bg-light-purple px-2 py-0.5 text-xs font-semibold text-white">
                    You
                  </span>
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-2">
                  <span
                    class="inline-flex items-center rounded-full bg-white px-2.5 py-1 text-xs font-medium text-cool-gray">
                    {{ rankLabel(p.rank) }}
                  </span>

                  <span v-if="p.rankChange !== null && p.rankChange !== 0" :class="changeBadgeClass(p.rankChange)"
                    class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                    {{ formatChange(p.rankChange) }} rank
                  </span>
                </div>
              </div>
            </div>

            <!-- RIGHT -->
            <div class="grid grid-cols-2 gap-3 sm:flex sm:flex-wrap sm:justify-end">
              <div class="rounded-lg bg-white px-4 py-3 min-w-[110px]">
                <p class="text-xs text-cool-gray mb-1">FAPs</p>
                <p class="font-bold text-blue-black text-base sm:text-lg">
                  {{ p.points }}
                </p>
              </div>

              <div class="rounded-lg bg-white px-4 py-3 min-w-[110px]">
                <p class="text-xs text-cool-gray mb-1">Answered</p>
                <p class="font-bold text-blue-black text-base sm:text-lg">
                  {{ p.challengesAnswered }}
                </p>
              </div>
            </div>
          </div>
        </button>

        <div v-if="visibleItems.length === 0" class="rounded-xl bg-dark-white p-6 text-cool-gray">
          No players found.
        </div>
      </div>

      <!-- LOAD MORE -->
      <div class="mt-8 flex items-center justify-center">
        <button v-if="items.length > visibleItems.length" @click="loadNextPage"
          class="rounded-lg bg-white border border-charcoal/10 px-6 py-3 font-semibold text-blue-black hover:bg-dark-white hover:shadow-sm cursor-pointer">
          Show next 20 players
        </button>
      </div>
    </template>
  </section>
</template>

<script setup>
import { onMounted, ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { apiGetLeaderboards } from '@/api/leaderboards'
import { toFriendlyError } from '@/utils/errorHandler'
import AvatarInitial from '@/components/ui/AvatarInitial.vue'
import GameweekProgressBar from "../components/GameweekProgressBar.vue"

document.title = 'Fantasy Academy | Leaderboard'

const router = useRouter()

const items = ref([])
const initialLoading = ref(false)
const loadingMore = ref(false)
const error = ref('')

const page = ref(1)
const lastPage = ref(1)

const hasMore = computed(() => page.value < lastPage.value)
const topThree = computed(() => items.value.slice(0, 3))

const searchQuery = ref('')
const visibleCount = ref(20)

/* -----------------------------
   FILTERED PLAYERS
-------------------------------- */
const filteredItems = computed(() => {
  if (!searchQuery.value) return items.value

  const q = searchQuery.value.toLowerCase()

  return items.value.filter(p =>
    p.playerName?.toLowerCase().includes(q)
  )
})

/* -----------------------------
   VISIBLE PLAYERS
-------------------------------- */
const visibleItems = computed(() => {

  // při search zobraz všechny nalezené
  if (searchQuery.value) {
    return filteredItems.value
  }

  // jinak pouze 20
  return filteredItems.value.slice(0, visibleCount.value)

})

/* -----------------------------
   LOAD MORE BUTTON
-------------------------------- */
const showLoadMore = computed(() => {

  // při search load more nedává smysl
  if (searchQuery.value) return false

  return visibleCount.value < items.value.length

})

function loadNextPage() {
  visibleCount.value += 20
}

/* -----------------------------
   RESET PAGINATION ON SEARCH
-------------------------------- */
watch(searchQuery, () => {
  visibleCount.value = 20
})


function badgeBg(rank, indexInList) {
  const r = rank ?? indexInList + 1
  if (r === 1) return 'bg-golden-yellow/80'
  if (r === 2) return 'bg-slate-200'
  if (r === 3) return 'bg-amber-200'
  return 'bg-dark-white'
}

function podiumBar(index) {
  if (index === 0) return 'bg-golden-yellow'
  if (index === 1) return 'bg-slate-300'
  return 'bg-amber-300'
}

function rankLabel(rank) {
  if (rank === 1) return 'Leader'
  if (rank === 2) return 'Runner-up'
  if (rank === 3) return 'Top 3'
  if (rank && rank <= 10) return 'Top 10'
  if (rank && rank <= 50) return 'Top 50'
  return 'Competitive'
}

function onRowClick(playerId) {
  if (!playerId) return
  router.push(`/player/${playerId}`)
}

async function fetchPage(n) {
  const { items: list, lastPage: lp } = await apiGetLeaderboards(n)
  lastPage.value = lp || n
  return list
}

async function loadInitial() {
  initialLoading.value = true
  error.value = ''

  try {
    page.value = 1
    const list = await fetchPage(page.value)
    items.value = list ?? []
  } catch (e) {
    const fe = toFriendlyError(e)
    console.warn('[LeaderboardPage] loadInitial FAIL', {
      status: e?.status,
      message: fe.userMessage,
      rawMessage: e?.message,
      data: e?.data,
    })
    error.value = fe.userMessage || 'Nepodařilo se načíst žebříček.'
  } finally {
    initialLoading.value = false
  }
}

async function loadMore() {
  if (!hasMore.value || loadingMore.value) return

  loadingMore.value = true
  error.value = ''

  try {
    const next = page.value + 1
    const list = await fetchPage(next)

    const seen = new Set(items.value.map((x) => x.playerId))
    const merged = [...items.value]

    for (const it of list) {
      if (!seen.has(it.playerId)) {
        merged.push(it)
        seen.add(it.playerId)
      }
    }

    items.value = merged
    page.value = next
  } catch (e) {
    const fe = toFriendlyError(e)
    console.warn('[LeaderboardPage] loadMore FAIL', {
      status: e?.status,
      message: fe.userMessage,
      rawMessage: e?.message,
      data: e?.data,
    })
    error.value = fe.userMessage || 'Nepodařilo se načíst více dat.'
  } finally {
    loadingMore.value = false
  }
}

function formatChange(value) {
  if (value === 0 || value === null || value === undefined) return ''
  return value > 0 ? `↑${value}` : `↓${Math.abs(value)}`
}

function changeClass(value) {
  if (value > 0) return 'text-pistachio'
  if (value < 0) return 'text-vibrant-coral'
  return 'text-cool-gray'
}

function changeBadgeClass(value) {
  if (value > 0) return 'bg-pistachio/10 text-pistachio'
  if (value < 0) return 'bg-vibrant-coral/10 text-vibrant-coral'
  return 'bg-dark-white text-cool-gray'
}

onMounted(loadInitial)
</script>