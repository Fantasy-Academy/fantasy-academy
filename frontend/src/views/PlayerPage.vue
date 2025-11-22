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

        <!-- Back button (goes below on mobile) -->
        <div class="w-full sm:w-auto sm:ml-auto">
          <router-link to="/leaderboard"
            class="block w-full text-center rounded-lg border border-dark-white/30 bg-white px-4 py-2 font-semibold text-blue-black hover:bg-dark-white shadow-sm">
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

    <template v-else-if="player">
      <!--stat cards-->
      <div class="mb-8 grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total FAPs -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Total FAPs</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.points ?? 0 }}</p>
          <p v-if="overall.pointsChange != null && overall.pointsChange !== 0"
            :class="changePointsClass(overall.pointsChange)" class="text-s">
            {{ formatChange(overall.pointsChange) }} this week
          </p>
        </div>

        <!-- Rank -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.rank ?? '—' }}</p>
          <p v-if="overall.rankChange != null && overall.rankChange !== 0" :class="changeRankClass(overall.rankChange)"
            class="text-s">
            {{ formatChange(overall.rankChange) }} this week
          </p>
        </div>

        <!-- Answered challenges -->
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
          <li v-for="(s, i) in overall.skills" :key="i"
            class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-center gap-3 justify-between">
              <p class="font-semibold text-blue-black font-alexandria">{{ s.name }}</p>
              <div class="flex items-center gap-3 text-sm font-alexandria">
                <span class="font-extrabold text-blue-black">{{ s.percentage }}%</span>
                <span v-if="s.percentageChange != null"
                  :class="s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrant-coral'">
                  {{ s.percentageChange >= 0 ? '+' : '' }}{{ s.percentageChange }}%
                </span>
              </div>
            </div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-dark-white">
              <div class="h-full bg-pistachio" :style="{ width: Math.min(100, Math.max(0, s.percentage)) + '%' }" />
            </div>
          </li>
        </ul>
      </div>

      <!-- Seasons (responsive) -->
      <div>
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Seasons</h2>

        <!-- Mobile: stacked cards -->
        <ul v-if="(player.seasonsStatistics?.length || 0) > 0" class="sm:hidden space-y-3">
          <li v-for="(s, i) in player.seasonsStatistics" :key="i"
            class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <p class="font-alexandria font-semibold text-blue-black">Season</p>
              <p class="font-alexandria text-blue-black">{{ s.seasonNumber }}</p>
            </div>
            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Rank</p>
                <p class="font-alexandria text-blue-black">{{ s.rank ?? '—' }}</p>
              </div>
              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Challenges</p>
                <p class="font-alexandria text-blue-black">{{ s.challengesAnswered }}</p>
              </div>
              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">FAPs</p>
                <p class="font-alexandria text-blue-black">{{ s.points }}</p>
              </div>
              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Top skills</p>
                <div class="mt-1 flex flex-wrap gap-2">
                  <span v-for="(sk, j) in (s.skills || []).slice(0, 3)" :key="j"
                    class="inline-flex items-center gap-1 rounded-full bg-white px-2 py-0.5 text-xs font-semibold text-blue-black">
                    {{ sk.name }}
                    <span class="text-cool-gray font-normal">{{ sk.percentage }}%</span>
                  </span>
                  <span v-if="(s.skills?.length || 0) > 3" class="text-xs text-cool-gray">
                    +{{ s.skills.length - 3 }} more
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <div v-else class="sm:hidden rounded-xl border border-charcoal/10 bg-dark-white p-4 text-cool-gray">
          No seasonal data.
        </div>

        <!-- Desktop/Tablet: table -->
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
              <tr v-for="(s, i) in player.seasonsStatistics || []" :key="i" class="border-t border-dark-white/60">
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.seasonNumber }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.rank ?? '—' }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.challengesAnswered }}</td>
                <td class="px-4 py-2 font-alexandria text-blue-black">{{ s.points }}</td>
                <td class="px-4 py-2">
                  <div class="flex flex-wrap gap-2">
                    <span v-for="(sk, j) in (s.skills || []).slice(0, 3)" :key="j"
                      class="inline-flex items-center gap-1 rounded-full bg-dark-white px-2 py-0.5 text-xs font-semibold text-blue-black">
                      {{ sk.name }}
                      <span class="text-cool-gray font-normal">{{ sk.percentage }}%</span>
                    </span>
                    <span v-if="(s.skills?.length || 0) > 3" class="text-xs text-cool-gray">
                      +{{ s.skills.length - 3 }} more
                    </span>
                  </div>
                </td>
              </tr>
              <tr v-if="!player.seasonsStatistics?.length">
                <td class="px-4 py-6 text-center text-cool-gray" colspan="5">No seasonal data.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- ========== Player's Answers ========== -->
      <div class="mt-10">
        <h2 class="font-bebas-neue text-2xl text-blue-black mb-4">
          Answered Challenges
        </h2>

        <div v-if="answersLoading" class="text-cool-gray">Loading answers…</div>
        <div v-else-if="answersError" class="text-vibrant-coral">{{ answersError }}</div>
        <div v-else-if="answers.length === 0" class="text-cool-gray">
          This player hasn't answered any challenges yet.
        </div>

        <div v-else class="grid gap-4 grid-cols-1">
          <div v-for="(c, i) in limitedAnswers" :key="i"
            class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex justify-between items-center">
              <h3 class="font-alexandria font-semibold text-blue-black text-lg truncate">
                {{ c.challengeName }}
              </h3>
              <span class="font-bold text-blue-black">{{ c.points }} FAPs</span>
            </div>

            <div class="mt-4 space-y-2">
              <span class="font-bold">Gameweek {{ c.gameweek ?? '—' }} Answers:</span>
              <div v-for="q in c.questions" :key="q.questionId" class="text-sm text-blue-black mt-2">
                <span class="font-semibold">{{ q.questionText }}:</span>
                <span class="text-cool-gray">{{ extractAnswerText(q.answer) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div v-if="answersToShow < answers.length" class="mt-2 text-center">
          <button @click="showMoreAnswers"
            class="px-4 py-2 rounded-lg bg-white border border-charcoal/20 font-semibold text-blue-black hover:bg-dark-white shadow-sm">
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

const { answers, loading: answersLoading, error: answersError, loadPlayerAnswers } = usePlayerAnswers();


const route = useRoute();
const player = ref(null);
const loading = ref(false);
const error = ref('');


onMounted(load);
async function load() {
  loading.value = true;
  try {
    player.value = await apiGetPlayer(route.params.id);
    console.log('[PlayerPage] overallStatistics:', player.value?.overallStatistics);
  } finally {
    loading.value = false;
  }

  if (player.value?.id) {
    loadPlayerAnswers(player.value.id).then(() => {
      console.log('[PlayerProfilePage] Loaded answers:', answers.value);
    });
  }
}

const registered = computed(() => {
  const d = player.value?.registeredAt ? new Date(player.value.registeredAt) : null;
  if (!d) return '—';
  try {
    return new Intl.DateTimeFormat('en-GB', {
      year: 'numeric', month: 'long', day: 'numeric',
      hour: '2-digit', minute: '2-digit'
    }).format(d);
  } catch { return d.toLocaleString(); }
});

const overall = computed(() => {
  return player.value?.overallStatistics ?? {
    rank: null,
    points: 0,
    challengesAnswered: 0,
    skills: [],
    weeklyPoints: 0,
    weeklyRankChange: 0,
  }
});

const initials = computed(() => {
  const name = player.value?.name?.trim() || '';
  if (!name) return '?';
  const parts = name.split(/\s+/).filter(Boolean);
  return ((parts[0]?.[0] || '') + (parts[parts.length - 1]?.[0] || '')).toUpperCase();
});

function extractAnswerText(answer) {
  if (!answer) return "—";

  if (answer.textAnswer) return answer.textAnswer;
  if (answer.numericAnswer !== null) return answer.numericAnswer;

  if (answer.selectedChoiceText) return answer.selectedChoiceText;
  if (answer.selectedChoiceTexts?.length) return answer.selectedChoiceTexts.join(", ");

  if (answer.orderedChoiceTexts?.length) return answer.orderedChoiceTexts.join(" → ");

  return "—";
}

function formatChange(value) {
  if (value === 0 || value === null || value === undefined) return '';
  return value > 0 ? `↑${value}` : `↓${Math.abs(value)}`;
}

function changePointsClass(value) {
  if (value > 0) return 'text-pistachio';
  if (value < 0) return 'text-vibrant-coral';
  return 'text-cool-gray';
}

function changeRankClass(value) {
  if (value < 0) return 'text-vibrant-coral';
  if (value > 0) return 'text-pistachio';
  return 'text-cool-gray';
}

const answersToShow = ref(5);

const limitedAnswers = computed(() =>
  (answers.value || []).slice(0, answersToShow.value)
);

function showMoreAnswers() {
  answersToShow.value += 5;
}

function formatDate(dt) {
  if (!dt) return '—';
  try {
    return new Intl.DateTimeFormat('en-GB', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(new Date(dt));
  } catch {
    return new Date(dt).toLocaleString();
  }
}

</script>
