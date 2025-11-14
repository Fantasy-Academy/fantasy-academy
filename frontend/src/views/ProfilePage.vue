<template>
  <section class="mx-auto max-w-5xl px-4 py-8 bg-dark-white/40 rounded-2xl">

    <!-- States -->
    <div v-if="loading" class="text-cool-gray">Loading profile…</div>

    <div v-else-if="error"
         class="rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-4 text-vibrant-coral shadow-sharp">
      {{ error }}
    </div>

    <template v-else>

      <!-- HEADER -->
      <div class="mb-8 flex flex-col lg:flex-row items-start lg:items-center gap-5
                  rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-5 text-white shadow-main">

        <!-- Avatar -->
        <div class="flex flex-row gap-4 items-center flex-1">
          <div class="relative h-16 w-16 shrink-0">
            <img v-if="avatarSrc"
                 :src="avatarSrc"
                 :alt="profile.name || 'User avatar'"
                 class="h-full w-full rounded-full object-cover border-2 border-golden-yellow"
                 @error="onImgError" />

            <div v-else class="grid h-full w-full place-items-center rounded-full bg-golden-yellow
                                text-blue-black text-2xl font-bold">
              {{ initials }}
            </div>
          </div>

          <div class="min-w-0">
            <h1 class="font-bebas-neue text-2xl sm:text-3xl tracking-wide">{{ profile.name }}</h1>
            <p class="text-dark-white/90 text-sm sm:text-base font-alexandria break-all">{{ profile.email }}</p>
            <p class="text-xs sm:text-sm text-dark-white/70 font-alexandria">
              Registered: <span class="font-semibold text-white">{{ formattedRegistered }}</span>
            </p>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex flex-col gap-2 w-full lg:w-auto items-start lg:items-end">
          <span class="inline-flex items-center gap-2 rounded-full bg-pistachio/20 px-3 py-1
                       text-sm font-semibold text-pistachio shadow-sm">
            {{ activeChallengesCount }} challenges available
          </span>

          <router-link to="profile/edit"
                       class="inline-flex items-center rounded-lg px-4 py-2 text-sm font-semibold text-white hover:underline">
            Edit Profile
          </router-link>
        </div>
      </div>


      <!-- ================= STAT CARDS ================= -->
      <div class="mb-8 grid gap-4 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">

        <!-- Total FAPs -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Total FAPs</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.points ?? 0 }}</p>

          <p v-if="overall.pointsChange"
             :class="changePointsClass(overall.pointsChange)">
            {{ formatChange(overall.pointsChange) }} this week
          </p>
        </div>

        <!-- Rank -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.rank ?? '—' }}</p>

          <p v-if="overall.rankChange"
             :class="changeRankClass(overall.rankChange)">
            {{ formatChange(overall.rankChange) }} this week
          </p>
        </div>

        <!-- Answered Challenges -->
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm text-center">
          <p class="text-sm text-cool-gray font-alexandria">Answered challenges</p>
          <p class="mt-1 text-2xl sm:text-3xl font-bold text-blue-black">{{ overall.challengesAnswered ?? 0 }}</p>
        </div>

        <!-- Gameweek component -->
        <GameweekStatus />
      </div>


      <!-- ================= SKILLS ================= -->
      <div v-if="overall.skills?.length" class="mb-10">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Skills</h2>

        <ul class="space-y-3">
          <li v-for="(s, i) in overall.skills" :key="i"
              class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex justify-between items-center">
              <p class="font-semibold text-blue-black font-alexandria">{{ s.name }}</p>
              <div class="flex items-center gap-2 text-sm font-alexandria">
                <span class="font-extrabold text-blue-black">{{ s.percentage }}%</span>
                <span :class="s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrant-coral'">
                  {{ s.percentageChange >= 0 ? '+' : '' }}{{ s.percentageChange }}%
                </span>
              </div>
            </div>

            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-dark-white">
              <div class="h-full bg-pistachio" :style="{ width: s.percentage + '%' }" />
            </div>
          </li>
        </ul>
      </div>


      <!-- ================= SEASONS ================= -->
      <div class="mb-4">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Seasons</h2>

        <!-- Mobile -->
        <ul v-if="profile.seasonsStatistics?.length" class="sm:hidden space-y-3">
          <li v-for="(s, i) in profile.seasonsStatistics" :key="i"
              class="rounded-xl border bg-white p-4 shadow-sm">

            <div class="flex justify-between">
              <p class="font-semibold text-blue-black">Season</p>
              <p>{{ s.seasonNumber }}</p>
            </div>

            <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Rank</p>
                <p>{{ s.rank ?? '—' }}</p>
              </div>

              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Challenges</p>
                <p>{{ s.challengesAnswered }}</p>
              </div>

              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">FAPs</p>
                <p>{{ s.points }}</p>
              </div>

              <div class="rounded-lg bg-dark-white/60 p-2">
                <p class="text-cool-gray">Top skills</p>
                <div class="flex gap-2 flex-wrap mt-1">
                  <span v-for="sk in s.skills.slice(0,3)"
                        class="bg-white px-2 py-0.5 rounded-full text-xs font-semibold text-blue-black">
                    {{ sk.name }} ({{ sk.percentage }}%)
                  </span>
                </div>
              </div>
            </div>
          </li>
        </ul>

        <div v-else class="sm:hidden rounded-xl border bg-dark-white p-4 text-cool-gray">
          No seasonal data yet.
        </div>

        <!-- Desktop -->
        <div class="hidden sm:block overflow-x-auto rounded-xl border bg-white shadow-sm">
          <table class="min-w-full text-sm">
            <thead class="bg-dark-white text-cool-gray">
              <tr>
                <th class="px-4 py-2">Season</th>
                <th class="px-4 py-2">Rank</th>
                <th class="px-4 py-2">Challenges</th>
                <th class="px-4 py-2">FAPs</th>
                <th class="px-4 py-2">Top skills</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(s, i) in profile.seasonsStatistics" :key="i"
                  class="border-t border-dark-white/60">

                <td class="px-4 py-2">{{ s.seasonNumber }}</td>
                <td class="px-4 py-2">{{ s.rank ?? '—' }}</td>
                <td class="px-4 py-2">{{ s.challengesAnswered }}</td>
                <td class="px-4 py-2">{{ s.points }}</td>

                <td class="px-4 py-2">
                  <div class="flex gap-2 flex-wrap">
                    <span v-for="sk in s.skills.slice(0,3)"
                          class="bg-dark-white px-2 py-0.5 rounded-full text-xs font-semibold text-blue-black">
                      {{ sk.name }} ({{ sk.percentage }}%)
                    </span>
                  </div>
                </td>
              </tr>

              <tr v-if="!profile.seasonsStatistics.length">
                <td colspan="5" class="text-center py-6 text-cool-gray">
                  No seasonal data yet.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>


      <!-- ================= MY ANSWERS ================= -->
      <div class="mt-10">
        <h2 class="font-bebas-neue text-2xl text-blue-black mb-4">My Answers</h2>

        <div v-if="answersLoading" class="text-cool-gray">Loading answers…</div>
        <div v-else-if="answersError" class="text-vibrant-coral">{{ answersError }}</div>

        <div v-else-if="!myAnswers.length"
             class="rounded-xl border bg-dark-white p-4 text-cool-gray">
          No answers yet.
        </div>

        <div v-else class="max-h-96 overflow-y-auto space-y-4 pr-2">

          <div v-for="(c, i) in limitedAnswers" :key="i"
               class="rounded-2xl border bg-white p-4 shadow-sm">

            <div class="flex items-center gap-2 text-xs mb-2">
              <span class="bg-dark-white px-2 py-0.5 rounded-full font-semibold text-blue-black">
                {{ c.challengeName }}
              </span>

              <span class="text-cool-gray">·</span>

              <span class="text-cool-gray">Gameweek {{ c.gameweek }}</span>

              <span class="ml-auto font-semibold text-blue-black">
                {{ c.points }} FAPs
              </span>
            </div>

            <!-- Questions inside challenge -->
            <div v-for="(q, qi) in c.questions" :key="qi" class="mt-2">
              <p class="font-alexandria text-blue-black font-semibold">
                {{ q.questionText }}
              </p>

              <p class="text-sm text-cool-gray mt-1">
                You answered: <span class="font-semibold text-blue-black">{{ formatAnswer(q.answer) }}</span>
              </p>
            </div>

          </div>
        </div>

        <div v-if="answersToShow < myAnswers.length" class="mt-4 text-center">
          <button @click="showMoreAnswers"
                  class="px-4 py-2 rounded-lg bg-white border-charcoal/20 font-semibold text-blue-black hover:bg-dark-white shadow-sm">
            Show more
          </button>
        </div>
      </div>

    </template>
  </section>
</template>


<script setup>
import { ref, computed, onMounted } from 'vue';
import { useAuth } from '@/composables/useAuth';
import { useProfile } from '@/composables/useProfile';
import { useChallenges } from '@/composables/useChallenges';
import { useMyAnswers } from '@/composables/useMyAnswers';
import { resolvedImage, onImgError } from '@/utils/imageHelpers';
import GameweekStatus from '@/components/GameweekStatus.vue';

// AUTH
const { user } = useAuth();
const { me, loading, error, load } = useProfile();

// CHALLENGES
const { challenges, loadChallenges } = useChallenges();

// ANSWERS
const {
  myAnswers,
  loading: answersLoading,
  error: answersError,
  loadMyAnswers
} = useMyAnswers();

// INIT
onMounted(() => {
  load();
  loadChallenges();
  loadMyAnswers({ page: 1, auth: true });
});

// PROFILE MERGE
const profile = computed(() => ({
  id: me.value?.id ?? user.value?.id ?? null,
  name: me.value?.name ?? user.value?.name ?? '',
  email: me.value?.email ?? user.value?.email ?? '',
  registeredAt: me.value?.registeredAt ?? user.value?.registeredAt ?? null,
  overallStatistics: me.value?.overallStatistics ?? user.value?.overallStatistics ?? null,
  seasonsStatistics: me.value?.seasonsStatistics ?? user.value?.seasonsStatistics ?? [],
}));

// ACTIVE CHALLENGES
const activeChallengesCount = computed(() => {
  return (challenges.value || []).filter(c => c?.isStarted && !c?.isExpired && !c?.isAnswered).length;
});

// AVATAR
const avatarSrc = computed(() => resolvedImage(profile.value));

// REGISTERED DATE
const formattedRegistered = computed(() => {
  if (!profile.value.registeredAt) return '—';
  return new Intl.DateTimeFormat('en-GB', {
    year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  }).format(new Date(profile.value.registeredAt));
});

// OVERALL STATS
const overall = computed(() => profile.value.overallStatistics ?? {
  rank: null,
  points: 0,
  challengesAnswered: 0,
  rankChange: 0,
  pointsChange: 0,
  skills: [],
});

// LIMIT ANSWERS
const answersToShow = ref(5);
const limitedAnswers = computed(() =>
  myAnswers.value.slice(0, answersToShow.value)
);

function showMoreAnswers() {
  answersToShow.value += 5;
}

function formatAnswer(a) {
  if (!a) return '—';
  if (a.textAnswer != null) return a.textAnswer;
  if (a.numericAnswer != null) return a.numericAnswer;
  if (a.selectedChoiceId) return a.selectedChoiceId;
  if (Array.isArray(a.selectedChoiceIds)) return a.selectedChoiceIds.join(', ');
  if (Array.isArray(a.orderedChoiceIds)) return a.orderedChoiceIds.join(' → ');
  return '—';
}

const initials = computed(() => {
  const name = profile.value.name?.trim();
  if (name) {
    const p = name.split(' ');
    return (p[0][0] + (p[1]?.[0] || '')).toUpperCase();
  }
  return (profile.value.email?.[0] || '?').toUpperCase();
});

function formatChange(v) {
  if (!v) return '';
  return v > 0 ? `↑${v}` : `↓${Math.abs(v)}`;
}

function changePointsClass(v) {
  if (v > 0) return 'text-pistachio';
  if (v < 0) return 'text-vibrant-coral';
  return 'text-cool-gray';
}

function changeRankClass(v) {
  if (v < 0) return 'text-vibrant-coral';
  if (v > 0) return 'text-pistachio';
  return 'text-cool-gray';
}
</script>