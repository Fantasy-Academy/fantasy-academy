<template>
  <section class="mx-auto max-w-5xl px-4 py-8">
    <!-- Header -->
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-5 text-white shadow-main">
      <div class="flex flex-wrap items-center gap-4">
        <div
          class="grid h-16 w-16 place-items-center rounded-full bg-golden-yellow text-2xl font-extrabold text-blue-black">
          {{ initials }}
        </div>

        <div class="min-w-0 flex-1">
          <h1 class="font-bebas-neue text-3xl tracking-wide truncate">{{ player?.name || 'Player' }}</h1>
          <p class="font-alexandria text-dark-white/80">
            Registered: <span class="font-semibold text-white">{{ registered }}</span>
          </p>
        </div>

        <div class="w-full sm:w-auto sm:ml-auto">
          <router-link
            to="/leaderboard"
            class="block w-full text-center rounded-lg border border-dark-white/30 bg-white px-4 py-2 font-semibold text-blue-black hover:bg-dark-white shadow-sm"
          >
            Back to leaderboard
          </router-link>
        </div>
      </div>
    </div>

    <!-- States -->
    <div v-if="loading" class="text-cool-gray">Loading player…</div>
    <div v-else-if="error" class="rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-4 text-vibrant-coral">
      {{ error }}
    </div>

    <!-- Main content -->
    <template v-else-if="player">
      <!-- Stat cards -->
      <div class="mb-8 grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total FAPs -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Total FAPs</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.points ?? 0 }}</p>
          <p
            v-if="overall.pointsChange"
            :class="changePointsClass(overall.pointsChange)"
            class="text-s"
          >
            {{ formatChange(overall.pointsChange) }} this week
          </p>
        </div>

        <!-- Rank -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.rank ?? '—' }}</p>
          <p
            v-if="overall.rankChange"
            :class="changeRankClass(overall.rankChange)"
            class="text-s"
          >
            {{ formatChange(overall.rankChange) }} this week
          </p>
        </div>

        <!-- Answered Challenges -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Answered challenges</p>
          <p class="mt-1 text-3xl font-bold text-blue-black">{{ overall.challengesAnswered ?? 0 }}</p>
        </div>

        <GameweekStatus />
      </div>

      <!-- Skills -->
      <div v-if="overall.skills?.length" class="mb-10">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Skills</h2>

        <ul class="space-y-3">
          <li
            v-for="(s, i) in overall.skills"
            :key="i"
            class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm"
          >
            <div class="flex flex-wrap items-center gap-3 justify-between">
              <p class="font-semibold text-blue-black font-alexandria">{{ s.name }}</p>
              <div class="flex items-center gap-3 text-sm font-alexandria">
                <span class="font-extrabold text-blue-black">{{ s.percentage }}%</span>
                <span
                  v-if="s.percentageChange != null"
                  :class="s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrant-coral'"
                >
                  {{ s.percentageChange >= 0 ? '+' : '' }}{{ s.percentageChange }}%
                </span>
              </div>
            </div>

            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-dark-white">
              <div
                class="h-full bg-pistachio"
                :style="{ width: Math.min(100, Math.max(0, s.percentage)) + '%' }"
              />
            </div>
          </li>
        </ul>
      </div>

      <!-- Seasons -->
      <div class="mb-10">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Seasons</h2>

        <div class="hidden sm:block overflow-x-auto rounded-xl border border-charcoal/10 bg-white shadow-sm">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-dark-white text-cool-gray">
              <tr>
                <th class="px-4 py-2 font-semibold">Season</th>
                <th class="px-4 py-2 font-semibold">Rank</th>
                <th class="px-4 py-2 font-semibold">Challenges</th>
                <th class="px-4 py-2 font-semibold">FAPs</th>
                <th class="px-4 py-2 font-semibold">Top skills</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(s, i) in player.seasonsStatistics" :key="i" class="border-t border-dark-white/60">
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.seasonNumber }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.rank ?? '—' }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.challengesAnswered }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.points }}</td>
                <td class="px-4 py-2">
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="(sk, j) in (s.skills || []).slice(0, 3)"
                      :key="j"
                      class="inline-flex items-center gap-1 rounded-full bg-dark-white px-2 py-0.5 text-xs font-semibold text-blue-black"
                    >
                      {{ sk.name }}
                      <span class="text-cool-gray font-normal">{{ sk.percentage }}%</span>
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="!player.seasonsStatistics?.length" class="rounded-xl border p-4 text-cool-gray">
          No seasonal data.
        </div>
      </div>

      <!-- Answers -->
      <div class="mt-10">
        <h2 class="font-bebas-neue text-2xl text-blue-black mb-4">Answered Challenges</h2>

        <div v-if="answersLoading" class="text-cool-gray">Loading answers…</div>
        <div v-else-if="answersError" class="text-vibrant-coral">{{ answersError }}</div>
        <div v-else-if="answers.length === 0" class="text-cool-gray">
          This player hasn't answered any challenges yet.
        </div>

        <div v-else class="max-h-96 overflow-y-auto space-y-4 pr-2">
          <div
            v-for="(a, i) in limitedAnswers"
            :key="i"
            class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm"
          >
            <div class="mb-2 flex flex-wrap items-center gap-2 text-xs">
              <span class="inline-flex items-center bg-dark-white px-2 py-0.5 rounded-full font-semibold text-blue-black">
                {{ a.challengeName }}
              </span>

              <span class="text-cool-gray">·</span>

              <span class="text-cool-gray">
                {{ formatDate(a.answeredAt) }}
              </span>

              <span class="ml-auto font-semibold text-blue-black">
                {{ a.points ?? a.myPoints ?? 0 }} FAPs
              </span>
            </div>

            <p class="font-alexandria text-blue-black font-semibold">
              {{ a.questionText }}
            </p>

            <p class="text-sm text-cool-gray mt-1">
              Answer:
              <span class="font-semibold text-blue-black">
                {{ a.answerText }}
              </span>
            </p>
          </div>
        </div>

        <div v-if="answersToShow < answers.length" class="mt-4 text-center">
          <button
            @click="showMoreAnswers"
            class="px-4 py-2 rounded-lg bg-white border border-charcoal/20 font-semibold text-blue-black hover:bg-dark-white shadow-sm"
          >
            Show more
          </button>
        </div>
      </div>
    </template>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { apiGetPlayer } from '@/api/players';
import { usePlayerAnswers } from '@/composables/usePlayerAnswers';
import GameweekStatus from '../components/GameweekStatus.vue';

document.title = 'Fantasy Academy | Player';

const route = useRoute();
const player = ref(null);
const loading = ref(false);
const error = ref('');

const { answers, loading: answersLoading, error: answersError, loadPlayerAnswers } =
  usePlayerAnswers();

onMounted(load);
async function load() {
  loading.value = true;
  try {
    player.value = await apiGetPlayer(route.params.id);
  } finally {
    loading.value = false;
  }

  if (player.value?.id) {
    loadPlayerAnswers(player.value.id);
  }
}

const registered = computed(() => {
  const d = player.value?.registeredAt ? new Date(player.value.registeredAt) : null;
  if (!d) return '—';
  return new Intl.DateTimeFormat('en-GB', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(d);
});

const overall = computed(() => {
  return player.value?.overallStatistics ?? {
    rank: null,
    points: 0,
    challengesAnswered: 0,
    skills: [],
    weeklyPoints: 0,
    weeklyRankChange: 0
  };
});

const initials = computed(() => {
  const name = player.value?.name || '';
  const parts = name.trim().split(/\s+/);
  return (parts[0]?.[0] || '?').toUpperCase();
});

function formatChange(v) {
  return v > 0 ? `↑${v}` : `↓${Math.abs(v)}`;
}

function changePointsClass(v) {
  return v > 0 ? 'text-pistachio' : 'text-vibrant-coral';
}

function changeRankClass(v) {
  return v > 0 ? 'text-pistachio' : 'text-vibrant-coral';
}

const answersToShow = ref(5);
const limitedAnswers = computed(() => answers.value.slice(0, answersToShow.value));

function showMoreAnswers() {
  answersToShow.value += 5;
}

function formatDate(dt) {
  return dt
    ? new Intl.DateTimeFormat('en-GB', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      }).format(new Date(dt))
    : '—';
}
</script>