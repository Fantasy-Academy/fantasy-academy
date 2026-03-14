<template>
  <section class="rounded-xl bg-dark-white p-4 shadow-sm">
    <div class="mb-4 flex items-start justify-between gap-3">
      <div>
        <h3 class="font-bold text-blue-black">Completed challenges</h3>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-cool-gray">
      Loading completed challenges...
    </div>

    <div v-else-if="pagedChallenges.length === 0" class="text-sm text-cool-gray">
      No completed challenges yet.
    </div>

    <div v-else class="space-y-3">
      <button
        v-for="challenge in pagedChallenges"
        :key="challenge.id"
        type="button"
        @click="openChallenge(challenge.id)"
        class="w-full rounded-xl border border-charcoal/10 bg-white p-4 text-left transition hover:scale-101 cursor-pointer"
      >
        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
          <div class="min-w-0">
            <h4 class="font-semibold text-blue-black truncate">
              {{ challenge.name }}
            </h4>

            <p class="mt-1 text-sm text-cool-gray">
              My answer:
              <span class="text-blue-black font-medium">
                {{ challenge.myAnswerPreview || '—' }}
              </span>
            </p>
          </div>

          <div class="flex-shrink-0">
            <span
              v-if="challenge.isEvaluated"
              class="inline-flex rounded-full bg-light-purple/15 px-3 py-1 text-sm font-semibold text-light-purple"
            >
              {{ challenge.myPoints ?? 0 }} FAPS
            </span>

            <span
              v-else
              class="inline-flex rounded-full bg-dark-purple/10 px-3 py-1 text-sm font-semibold text-dark-purple"
            >
              Waiting for the evaluation
            </span>
          </div>
        </div>
      </button>
    </div>

    <div
      v-if="totalPages > 1"
      class="mt-5 flex items-center justify-center gap-3"
    >
      <button
        type="button"
        class="rounded-lg border border-charcoal/10 bg-white px-3 py-2 text-sm font-semibold text-blue-black transition disabled:opacity-40 cursor-pointer"
        :disabled="page === 1"
        @click="page--"
      >
        ← Previous
      </button>

      <span class="text-sm text-cool-gray">
        Page {{ page }} / {{ totalPages }}
      </span>

      <button
        type="button"
        class="rounded-lg border border-charcoal/10 bg-white px-3 py-2 text-sm font-semibold text-blue-black transition disabled:opacity-40 cursor-pointer"
        :disabled="page === totalPages"
        @click="page++"
      >
        Next →
      </button>
    </div>

    <ChallengeModal
      v-if="showModal"
      :show="showModal"
      :challenge-id="selectedChallengeId"
      @close="showModal = false"
      @submitted="handleSubmitted"
    />
  </section>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getToken } from '@/services/tokenService'
import ChallengeModal from '@/components/ChallengeModal.vue'

const BASE_URL =
  import.meta.env.VITE_BACKEND_URL ??
  import.meta.env.VITE_API_BASE_URL ??
  ''

const loading = ref(false)
const challenges = ref([])
const page = ref(1)
const pageSize = 10

const showModal = ref(false)
const selectedChallengeId = ref(null)

const completedChallenges = computed(() => {
  return (challenges.value || [])
    .filter((challenge) => {
      return !!challenge.isAnswered
    })
    .sort((a, b) => {
      const aDate = a.answeredAt ? new Date(a.answeredAt).getTime() : 0
      const bDate = b.answeredAt ? new Date(b.answeredAt).getTime() : 0
      return bDate - aDate
    })
})

const totalPages = computed(() => {
  return Math.max(1, Math.ceil(completedChallenges.value.length / pageSize))
})

const pagedChallenges = computed(() => {
  const start = (page.value - 1) * pageSize
  const end = start + pageSize
  return completedChallenges.value.slice(start, end)
})

function openChallenge(challengeId) {
  selectedChallengeId.value = challengeId
  showModal.value = true
}

function handleSubmitted() {
  showModal.value = false
  loadCompletedChallenges()
}

function getAnswerPreview(question) {
  const answer = question?.myAnswer
  if (!answer) return null

  if (answer.textAnswer) return answer.textAnswer

  if (answer.numericAnswer !== null && answer.numericAnswer !== undefined) {
    return String(answer.numericAnswer)
  }

  if (answer.selectedChoiceText) {
    return answer.selectedChoiceText
  }

  if (Array.isArray(answer.selectedChoiceTexts) && answer.selectedChoiceTexts.length) {
    return answer.selectedChoiceTexts.join(', ')
  }

  if (Array.isArray(answer.orderedChoiceTexts) && answer.orderedChoiceTexts.length) {
    return answer.orderedChoiceTexts.join(' → ')
  }

  return null
}

async function loadCompletedChallenges() {
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
    const baseChallenges = Array.isArray(data) ? data : data.member ?? []
    const completedBase = baseChallenges.filter((challenge) => !!challenge.isAnswered)

    const detailedChallenges = await Promise.all(
      completedBase.map(async (challenge) => {
        try {
          const detailResponse = await fetch(`${BASE_URL}/api/challenges/${challenge.id}`, {
            headers: {
              'Content-Type': 'application/json',
              ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
          })

          if (!detailResponse.ok) {
            return {
              ...challenge,
              myAnswerPreview: null,
            }
          }

          const detailData = await detailResponse.json()
          const firstAnsweredQuestion = (detailData.questions || []).find((q) => q.myAnswer)

          return {
            ...challenge,
            myAnswerPreview: getAnswerPreview(firstAnsweredQuestion),
          }
        } catch (err) {
          return {
            ...challenge,
            myAnswerPreview: null,
          }
        }
      })
    )

    challenges.value = detailedChallenges

    if (page.value > Math.ceil(detailedChallenges.length / pageSize)) {
      page.value = 1
    }
  } catch (err) {
    console.error('Failed to load completed challenges:', err)
    challenges.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCompletedChallenges()
})
</script>