<template>
  <section>
    <div class="flex flex-col gap-4 rounded-lg bg-dark-white p-4 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-3 min-w-0">
        <AvatarInitial v-if="player" :name="player.name" gradient size="xl" />

        <div class="flex items-start gap-3 min-w-0">
          <div class="flex min-w-0 flex-col">
            <h1 class="truncate font-semibold text-lg sm:text-xl">
              {{ player?.name || 'Player' }}
            </h1>

            <span class="text-sm text-cool-gray">
              Player profile
            </span>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2 text-white md:justify-end">
        <div class="rounded-full bg-light-purple px-4 py-2 text-sm font-semibold sm:px-5 sm:py-3">
          <p>
            Gameweek:
            <span v-if="dashboardLoading">...</span>
            <span v-else>{{ currentGameweekNumber ?? '—' }}</span>
          </p>
        </div>

        <div class="rounded-full bg-dark-purple/75 px-4 py-2 text-sm font-semibold sm:px-5 sm:py-3">
          <p>
            FAPS:
            <span v-if="dashboardLoading">...</span>
            <span v-else>{{ playerPoints ?? 0 }}</span>
          </p>
        </div>

        <div
          class="flex flex-row items-center gap-1 rounded-full bg-dark-purple/75 px-4 py-2 text-sm font-semibold sm:px-5 sm:py-3">
          <p>
            Rank:
            <span v-if="dashboardLoading">...</span>
            <span v-else>{{ playerRank ?? '—' }}</span>
          </p>
          <img src="../assets/rank.svg" alt="rank" class="w-3 h-3" />
        </div>
      </div>
    </div>
  </section>

  <section class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
    <ProfileSkillsPolarChart :player-id="playerId" />
    <ProfileActivityChart :player-id="playerId" />
  </section>
  <section class="mt-4">
    <ProfileGameweekPointsChart :player-id="playerId" />
  </section>

  <section class="mt-6">
    <ProfileCompletedChallenges :player-id="playerId" />
  </section>

  <ChallengeModal v-if="showModal" :show="showModal" :challenge-id="selectedChallengeId" @close="showModal = false"
    @submitted="handleChallengeSubmitted" />
</template>

<script setup>
import AvatarInitial from '../components/ui/AvatarInitial.vue'
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { getToken } from '@/services/tokenService'
import ProfileSkillsPolarChart from '../components/ProfileSkillsPolarChart.vue'
import ProfileActivityChart from '../components/ProfileActivityChart.vue'
import ProfileCompletedChallenges from '../components/ProfileCompletedChallenges.vue'
import ProfileGameweekPointsChart from '../components/ProfileGameweekPointsChart.vue'
import ChallengeModal from '../components/ChallengeModal.vue'

const route = useRoute()
const playerId = computed(() => route.params.id)

const loading = ref(false)
const dashboardLoading = ref(false)

const challenges = ref([])
const player = ref(null)

const playerRank = ref(null)
const playerPoints = ref(null)
const challengesAnswered = ref(null)

const showModal = ref(false)
const selectedChallengeId = ref(null)

const currentGameweekNumber = ref(null)

const BASE_URL =
  import.meta.env.VITE_BACKEND_URL ??
  import.meta.env.VITE_API_BASE_URL ??
  ''

const activeChallenges = computed(() => {
  return (challenges.value || []).filter((challenge) => {
    const started = challenge.isStarted ?? true
    const expired = !!challenge.isExpired
    const answered = !!challenge.isAnswered

    return started && !expired && !answered
  })
})

function resolveChallengeImage(image) {
  if (!image) return ''

  if (image.startsWith('http://') || image.startsWith('https://')) {
    return image
  }

  if (!BASE_URL) return image

  return `${BASE_URL}${image.startsWith('/') ? image : `/${image}`}`
}

function openChallenge(challengeId) {
  selectedChallengeId.value = challengeId
  showModal.value = true
}

function handleChallengeSubmitted() {
  showModal.value = false
  loadChallenges()
}

async function loadPlayerData() {
  dashboardLoading.value = true

  try {
    const token = getToken()

    const headers = {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    }

    const [playerResponse, gameweeksResponse] = await Promise.all([
      fetch(`${BASE_URL}/api/player/${playerId.value}`, { headers }),
      fetch(`${BASE_URL}/api/gameweeks`, { headers }),
    ])

    if (!playerResponse.ok) {
      throw new Error('Failed to load player data')
    }

    if (!gameweeksResponse.ok) {
      throw new Error('Failed to load gameweeks')
    }

    const playerData = await playerResponse.json()
    const gameweeksData = await gameweeksResponse.json()

    player.value = playerData
    playerRank.value = playerData?.overallStatistics?.rank ?? null
    playerPoints.value = playerData?.overallStatistics?.points ?? 0
    challengesAnswered.value = playerData?.overallStatistics?.challengesAnswered ?? 0
    currentGameweekNumber.value = gameweeksData?.current?.number ?? null
  } catch (err) {
    console.error('Failed to load player profile data:', err)
    player.value = null
    playerRank.value = null
    playerPoints.value = 0
    challengesAnswered.value = 0
    currentGameweekNumber.value = null
  } finally {
    dashboardLoading.value = false
  }
}

async function loadChallenges() {
  loading.value = true

  try {
    const token = getToken()

    const response = await fetch(`${BASE_URL}/api/challenges`, {
      headers: {
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    })

    if (!response.ok) {
      throw new Error('Failed to load challenges')
    }

    const data = await response.json()
    challenges.value = Array.isArray(data) ? data : data.member ?? []
  } catch (err) {
    console.error('Failed to fetch active challenges:', err)
    challenges.value = []
  } finally {
    loading.value = false
  }
}

async function loadPage() {
  await Promise.all([
    loadPlayerData(),
    loadChallenges(),
  ])
}

onMounted(() => {
  loadPage()
})

watch(
  () => route.params.id,
  () => {
    loadPage()
  }
)
</script>