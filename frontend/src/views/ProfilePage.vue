<template>
  <section>
    <div class="flex flex-col gap-4 rounded-lg bg-dark-white p-4 md:flex-row md:items-center md:justify-between">
      <div class="flex items-center gap-3 min-w-0">
        <AvatarInitial v-if="user" :name="user.name" gradient size="xl" />

        <div class="flex items-start gap-3 min-w-0">
          <div class="flex min-w-0 flex-col">
            <h1 class="truncate font-semibold text-lg sm:text-xl">
              {{ user.name }}
            </h1>

            <span @click="handleLogout" class="text-sm text-light-purple cursor-pointer hover:underline">
              Logout
            </span>
          </div>

          <router-link to="/profile/edit" class="flex-shrink-0 mt-1 opacity-70 hover:opacity-100 transition">
            <img src="../assets/edit.svg" alt="Edit profile" class="w-5 h-5" />
          </router-link>
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
            <span v-else>{{ totalFaps ?? 0 }}</span>
          </p>
        </div>

        <div
          class="flex flex-row items-center gap-1 rounded-full bg-dark-purple/75 px-4 py-2 text-sm font-semibold sm:px-5 sm:py-3">
          <p>
            Rank:
            <span v-if="dashboardLoading">...</span>
            <span v-else>{{ userRank ?? '—' }}</span>
          </p>
          <img src="../assets/rank.svg" alt="rank" class="w-3 h-3" />
        </div>
      </div>
    </div>

    <section>
      <div class="mt-4">
        <div v-if="loading" class="text-sm text-cool-gray">
          Loading active challenges...
        </div>

        <div v-else class="flex flex-row gap-4 overflow-x-auto pb-2 scrollbar-thin">
          <button v-for="challenge in activeChallenges" :key="challenge.id" type="button"
            @click="openChallenge(challenge.id)" class="min-w-[260px] max-w-[260px] flex-shrink-0 flex flex-row items-center justify-between
               bg-dark-white p-3 rounded-xl cursor-pointer text-left
               transition hover:scale-[1.01]">
            <div class="min-w-0 pr-3">
              <h2 class="font-semibold text-blue-black truncate">
                {{ challenge.name }}
              </h2>

              <p class="mt-1 inline-flex items-center rounded-full bg-light-purple/15
                   px-2 py-0.5 text-xs font-semibold text-light-purple">
                ACTIVE
              </p>
            </div>

            <img v-if="challenge.image" :src="resolveChallengeImage(challenge.image)" :alt="challenge.name"
              class="w-16 h-16 rounded-lg object-cover flex-shrink-0 bg-white" />

            <div v-else class="w-16 h-16 rounded-lg bg-white border border-charcoal/10 flex-shrink-0" />
          </button>
        </div>
      </div>

      <ChallengeModal v-if="showModal" :show="showModal" :challenge-id="selectedChallengeId" @close="showModal = false"
        @submitted="handleChallengeSubmitted" />
    </section>
  </section>
  <section class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
    <ProfileSkillsPolarChart />
    <ProfileActivityChart />
  </section>
  <section class="mt-4">
    <ProfileGameweekPointsChart />
  </section>
  <section class="mt-6">
    <ProfileCompletedChallenges />
  </section>
</template>

<script setup>
import AvatarInitial from '../components/ui/AvatarInitial.vue';
import { ref, computed, onMounted } from 'vue'
import { getToken } from '@/services/tokenService'
import { useAuth } from '@/composables/useAuth'
import ProfileSkillsPolarChart from '../components/ProfileSkillsPolarChart.vue'
import ProfileActivityChart from '../components/ProfileActivityChart.vue'
import ProfileCompletedChallenges from '../components/ProfileCompletedChallenges.vue'
import ProfileGameweekPointsChart from '../components/ProfileGameweekPointsChart.vue'

const { isAuthenticated, logout, user } = useAuth()

const loading = ref(false)
const challenges = ref([])

// MODAL STATE — DOPLNĚNO
const showModal = ref(false)
const selectedChallengeId = ref(null)

// DOPLNĚNO
const dashboardLoading = ref(false)
const currentGameweekNumber = ref(null)
const totalFaps = ref(null)
const userRank = ref(null)

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

// DOPLNĚNO
function openChallenge(challengeId) {
  selectedChallengeId.value = challengeId
  showModal.value = true
}

// DOPLNĚNO
function handleChallengeSubmitted() {
  showModal.value = false
  loadChallenges()
}

// DOPLNĚNO
function handleLogout() {
  logout()
}

// DOPLNĚNO
async function loadDashboardData() {
  dashboardLoading.value = true

  try {
    const token = getToken()

    const headers = {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    }

    const [meResponse, gameweeksResponse] = await Promise.all([
      fetch(`${BASE_URL}/api/me`, { headers }),
      fetch(`${BASE_URL}/api/gameweeks`, { headers }),
    ])

    if (!meResponse.ok) {
      throw new Error('Failed to load user data')
    }

    if (!gameweeksResponse.ok) {
      throw new Error('Failed to load gameweeks')
    }

    const meData = await meResponse.json()
    const gameweeksData = await gameweeksResponse.json()

    totalFaps.value = meData?.overallStatistics?.points ?? 0
    userRank.value = meData?.overallStatistics?.rank ?? null
    currentGameweekNumber.value = gameweeksData?.current?.number ?? null
  } catch (err) {
    console.error('Failed to load dashboard data:', err)
    totalFaps.value = 0
    userRank.value = null
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

onMounted(() => {
  loadChallenges()
  loadDashboardData()
})
</script>