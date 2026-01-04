<template>
  <section class="mx-auto max-w-5xl px-4 py-8 bg-dark-white/40 rounded-2xl">
    <!-- States -->
    <div v-if="loading" class="text-cool-gray">Loading profile…</div>
    <div v-else-if="error"
      class="rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-4 text-vibrant-coral shadow-sharp">
      {{ error }}
    </div>

    <template v-else>
      <!-- Header with avatar/monogram -->
      <div
        class="mb-8 flex flex-col lg:flex-row items-start lg:items-center gap-5 rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-5 text-white shadow-main">
        <!-- Left side: Avatar + user info -->
        <div class="flex flex-row sm:flex-row gap-4 items-center flex-1">
          <div class="relative h-16 w-16 shrink-0">
            <img v-if="avatarSrc" :src="avatarSrc" :alt="profile.name || 'User avatar'"
              class="h-full w-full rounded-full object-cover border-2 border-golden-yellow" loading="lazy"
              @error="onImgError" />
            <div v-else
              class="grid h-full w-full place-items-center rounded-full bg-golden-yellow text-blue-black text-2xl font-bold">
              {{ initials }}
            </div>
          </div>
          <div class="min-w-0">
            <h1 class="font-bebas-neue text-2xl sm:text-3xl tracking-wide">
              {{ profile.name }}
            </h1>
            <p class="text-dark-white/90 text-sm sm:text-base font-alexandria break-all">
              {{ profile.email }}
            </p>
            <p class="text-xs sm:text-sm text-dark-white/70 font-alexandria">
              Registered:
              <span class="font-semibold text-white">{{ formattedRegistered }}</span>
            </p>
          </div>
        </div>

        <!-- Right side: stats + edit -->
        <div class="flex flex-col gap-2 w-full lg:w-auto items-start lg:items-end">
          <span
            class="inline-flex items-center gap-2 rounded-full bg-pistachio/20 px-3 py-1 text-sm font-semibold text-pistachio shadow-sm"
            title="Number of available challenges">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="M5 4h14a1 1 0 0 1 1 1v13.382a1 1 0 0 1-1.553.833L12 15.236l-5.447 3.979A1 1 0 0 1 5 18.382V5a1 1 0 0 1 1-1Z" />
            </svg>
            {{ activeChallengesCount }} challenges available
          </span>
          <router-link to="profile/edit"
            class="inline-flex items-center rounded-lg px-4 py-2 text-sm sm:text-base font-semibold text-white hover:underline">
            Edit Profile
          </router-link>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="mb-8 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total FAPs -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Total FAPs</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">
            {{ overall.points ?? 0 }}
          </p>
          <p v-if="overall.pointsChange != null && overall.pointsChange !== 0"
            :class="changePointsClass(overall.pointsChange)" class="text-s">
            {{ formatChange(overall.pointsChange) }} this week
          </p>
        </div>

        <!-- Rank -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">
            {{ overall.rank ?? '—' }}
          </p>
          <p v-if="overall.rankChange != null && overall.rankChange !== 0" :class="changeRankClass(overall.rankChange)"
            class="text-s">
            {{ formatChange(overall.rankChange) }} this week
          </p>
        </div>

        <!-- Answered Challenges -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Answered challenges</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">
            {{ overall.challengesAnswered ?? 0 }}
          </p>
        </div>

        <GameweekStatus />
      </div>

      <!-- Skills -->
      <div v-if="overall.skills?.length" class="mb-10">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Skills</h2>
        <ul class="space-y-3">
          <li v-for="(s, i) in overall.skills" :key="i"
            class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
              <p class="font-semibold text-blue-black font-alexandria">{{ s.name }}</p>
              <div class="flex items-center gap-2 text-sm font-alexandria">
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
      <div class="mb-4">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Seasons</h2>

        <!-- Mobile: stacked cards -->
        <ul v-if="(profile.seasonsStatistics?.length || 0) > 0" class="sm:hidden space-y-3">
          <li v-for="(s, i) in profile.seasonsStatistics" :key="i"
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
          No seasonal data yet.
        </div>

        <!-- Desktop/tablet: table -->
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
              <tr v-for="(s, i) in profile.seasonsStatistics" :key="i" class="border-t border-dark-white/60">
                <td class="px-4 py-2 font-alexandria text-blue-black">
                  {{ s.seasonNumber }}
                </td>
                <td class="px-4 py-2 font-alexandria text-blue-black">
                  {{ s.rank ?? '—' }}
                </td>
                <td class="px-4 py-2 font-alexandria text-blue-black">
                  {{ s.challengesAnswered }}
                </td>
                <td class="px-4 py-2 font-alexandria text-blue-black">
                  {{ s.points }}
                </td>
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
              <tr v-if="!profile.seasonsStatistics?.length">
                <td class="px-4 py-6 text-center text-cool-gray" colspan="5">
                  No seasonal data yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- PLAYER ANSWERS -->
      <div class="mt-10">
        <h2 class="font-bebas-neue text-2xl text-blue-black mb-4">Player Answers</h2>

        <div v-if="playerAnswersLoading" class="text-cool-gray">Loading answers…</div>
        <div v-else-if="playerAnswersError" class="text-vibrant-coral">
          {{ playerAnswersError }}
        </div>

        <div v-else-if="playerAnswers.length === 0"
          class="rounded-xl border border-charcoal/10 bg-dark-white p-4 text-cool-gray">
          No answers yet.
        </div>

        <div v-else class="max-h-96 overflow-y-auto space-y-4 pr-2">
          <div v-for="(c, i) in limitedAnswers" :key="c.challengeId || i"
            class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <!-- Header -->
            <div class="mb-2 flex flex-wrap items-center gap-2 text-xs">
              <span
                class="inline-flex items-center bg-dark-white px-2 py-0.5 rounded-full font-semibold text-blue-black">
                {{ c.challengeName }}
              </span>
              <button class="ml-auto text-xs font-semibold text-vibrant-coral hover:underline cursor-pointer"
                @click="openChallengeFromAnswers(c.challengeId)">
                View challenge
              </button>
              <span class="text-cool-gray">·</span>

              <span class="text-cool-gray">Gameweek {{ c.gameweek }}</span>

              <p class="font-bold text-blue-black">
                +{{ c.points }} FAPs
              </p>
            </div>

            <!-- Questions -->
            <div v-for="(q, qi) in c.questions || []" :key="q.questionId || qi" class="mt-3">
              <p class="font-alexandria text-blue-black font-semibold">
                {{ q.questionText }}
              </p>

              <p class="text-sm mt-1">
                Your answer:
                <span class="font-semibold text-blue-black">
                  {{ formatAnswer(q.answer) }}
                </span>
              </p>

              <p class="text-sm mt-1">
                Correct:
                <span class="font-semibold text-pistachio">
                  {{ formatCorrectAnswer(q.correctAnswer) }}
                </span>
              </p>
            </div>
          </div>
        </div>

        <div v-if="answersToShow < playerAnswers.length" class="mt-4 text-center">
          <button @click="showMoreAnswers"
            class="px-4 py-2 rounded-lg bg-white border border-charcoal/20 font-semibold text-blue-black hover:bg-dark-white shadow-sm">
            Show more
          </button>
        </div>
      </div>
    </template>
  </section>
  <ChallengeModal v-if="showChallengeModal" :show="showChallengeModal" :challenge-id="selectedChallengeId"
    @close="showChallengeModal = false" />
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { resolvedImage, onImgError } from '../utils/imageHelpers';
import { useProfile } from '@/composables/useProfile';
import { useAuth } from '@/composables/useAuth';
import { useChallenges } from '@/composables/useChallenges';
import GameweekStatus from '../components/GameweekStatus.vue';
import { usePlayerAnswers } from '@/composables/usePlayerAnswers';
import { formatAnswer, formatCorrectAnswer } from '@/utils/formatAnswers';
import { watchEffect } from 'vue';
import ChallengeModal from '../components/ChallengeModal.vue';

const { user } = useAuth();
const { me, loading, error, load } = useProfile();
const { challenges, loadChallenges } = useChallenges();

const {
  answers: playerAnswers,
  loading: playerAnswersLoading,
  error: playerAnswersError,
  loadPlayerAnswers,
} = usePlayerAnswers();

const showChallengeModal = ref(false);
const selectedChallengeId = ref(null);

function openChallengeFromAnswers(challengeId) {
  selectedChallengeId.value = challengeId;
  showChallengeModal.value = true;
}

document.title = 'Fantasy Academy | Profile';

// Pokud už máme usera a není nastavený me, zkopíruj ho
if (user.value && !me.value) {
  me.value = user.value;
}

onMounted(() => {
  load();           // profil
  loadChallenges(); // výzvy

  // pro případ, že me je už připravený
  if (me.value?.id) {
    loadPlayerAnswers(me.value.id);
  }
});

// Když se změní ID hráče → načíst jeho answers
watch(
  () => me.value?.id,
  (id) => {
    if (id) {
      console.log('🔵 Loading player answers for profile id:', id);
      loadPlayerAnswers(id);
    }
  }
);

const profile = computed(() => ({
  id: me.value?.id ?? user.value?.id ?? null,
  name: me.value?.name ?? user.value?.name ?? '',
  email: me.value?.email ?? user.value?.email ?? '',
  registeredAt: me.value?.registeredAt ?? user.value?.registeredAt ?? null,
  availableChallenges: me.value?.availableChallenges ?? 0,
  overallStatistics: me.value?.overallStatistics ?? user.value?.overallStatistics ?? null,
  seasonsStatistics: me.value?.seasonsStatistics ?? user.value?.seasonsStatistics ?? [],
}));

// Aktivní výzvy
const activeChallengesCount = computed(() => {
  const list = challenges.value || [];
  return list.filter((c) => c && c.isStarted && !c.isExpired && !c.isAnswered).length;
});

const avatarSrc = computed(() => resolvedImage(profile.value));

const formattedRegistered = computed(() => {
  if (!profile.value.registeredAt) return '—';
  const d = new Date(profile.value.registeredAt);
  try {
    return new Intl.DateTimeFormat('en-GB', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }).format(d);
  } catch {
    return d.toLocaleString();
  }
});

const overall = computed(
  () =>
    profile.value.overallStatistics ?? {
      rank: null,
      challengesAnswered: 0,
      points: 0,
      weeklyPoints: 0,
      weeklyRankChange: 0,
      skills: [],
    }
);

const initials = computed(() => {
  const name = profile.value.name?.trim();
  if (name) {
    const parts = name.split(/\s+/).filter(Boolean);
    const first = parts[0]?.[0] ?? '';
    const last = parts.length > 1 ? parts[parts.length - 1][0] ?? '' : '';
    return (first + last).toUpperCase();
  }
  const email = profile.value.email ?? '';
  return (email[0] || '?').toUpperCase();
});

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
  playerAnswers.value.slice(0, answersToShow.value)
);

watchEffect(() => {
  console.log("🟪 limitedAnswers used for rendering:", limitedAnswers.value);
});

function showMoreAnswers() {
  answersToShow.value += 5;
}


watchEffect(() => {
  console.log("🟦 RAW playerAnswers from composable:", playerAnswers.value);

  if (!playerAnswers.value || playerAnswers.value.length === 0) {
    console.log("🟨 No player answers yet.");
    return;
  }

  playerAnswers.value.forEach((ch, ci) => {
    console.groupCollapsed(`📘 Challenge #${ci + 1}: ${ch.challengeName || ch.name}`);
    console.log("Challenge object:", ch);

    console.log("➡️ gameweek:", ch.gameweek);
    console.log("➡️ points:", ch.points ?? ch.myPoints);

    ch.questions?.forEach((q, qi) => {
      console.groupCollapsed(`🟩 Question #${qi + 1}`);
      console.log("Text:", q.questionText || q.text);
      console.log("My Answer:", q.answer || q.myAnswer);
      console.log("Correct Answer:", q.correctAnswer);
      console.groupEnd();
    });

    console.groupEnd();
  });
});

watchEffect(() => {
  playerAnswers.value?.forEach((c, ci) => {
    c.questions?.forEach((q, qi) => {
      console.log(
        `%c[DEBUG correctAnswer] challenge ${ci} question ${qi}`,
        "color:#ff00ff;font-weight:bold"
      );
      console.log("CorrectAnswer object actually passed:", q.correctAnswer);
      console.log("After formatter:", formatCorrectAnswer(q.correctAnswer));
    });
  });
});


</script>