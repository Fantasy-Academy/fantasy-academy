<template>
  <section class="mx-auto max-w-6xl px-4 py-8">
    <!-- States -->
    <div v-if="loadingProfile" class="text-cool-gray">Loading profile…</div>
    <div
      v-else-if="errorProfile"
      class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-4 text-vibrant-coral shadow-sharp"
    >
      {{ errorProfile }}
    </div>

    <template v-else>
      <!-- Header -->
      <div
        class="mb-8 flex flex-wrap items-center gap-4 rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-5 text-white shadow-main"
      >
        <div
          class="grid h-14 w-14 place-items-center rounded-full bg-golden-yellow text-blue-black text-lg font-extrabold"
        >
          {{ initials }}
        </div>
        <div class="min-w-0">
          <h1 class="truncate font-bebas-neue text-3xl tracking-wide">
            Hello, {{ profile.name || 'player' }} 👋
          </h1>
          <p class="font-alexandria text-dark-white/90">
            Overview of your account and current challenges
          </p>
        </div>
        <div class="ml-auto flex flex-wrap gap-2">
          <router-link
            to="/challenges"
            class="inline-flex items-center justify-center rounded-lg bg-golden-yellow px-4 py-2 font-semibold text-blue-black hover:opacity-90 shadow-sm"
          >
            Browse Challenges
          </router-link>
          <router-link
            to="/profile"
            class="inline-flex items-center justify-center rounded-lg border border-dark-white/30 bg-white px-4 py-2 font-semibold text-blue-black hover:bg-dark-white shadow-sm"
          >
            My Profile
          </router-link>
        </div>
      </div>

      <!-- KPI Cards -->
      <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Total Points</p>
          <p class="mt-1 text-3xl font-extrabold text-blue-black">{{ overall.points ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-3xl font-extrabold text-blue-black">{{ overall.rank ?? '—' }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Answered Challenges</p>
          <p class="mt-1 text-3xl font-extrabold text-blue-black">{{ overall.challengesAnswered ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Available Challenges</p>
          <p class="mt-1 text-3xl font-extrabold text-blue-black">{{ availableChallenges.length }}</p>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <!-- Available Challenges -->
        <div class="lg:col-span-2">
          <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bebas-neue text-2xl text-blue-black">
              Available Challenges
              <span class="ml-2 text-base font-normal text-cool-gray">({{ availableChallenges.length }})</span>
            </h2>
            <router-link to="/challenges" class="text-sm text-vibrant-coral hover:underline">
              All challenges →
            </router-link>
          </div>

          <div v-if="loadingChallenges" class="text-cool-gray">Loading challenges…</div>
          <div v-else-if="errorChallenges" class="rounded-xl border border-amber-300 bg-amber-50 p-3 text-amber-800">
            {{ errorChallenges }}
          </div>
          <div v-else>
            <div
              v-if="topChallenges.length === 0"
              class="rounded-xl border border-charcoal/10 bg-dark-white p-4 text-cool-gray"
            >
              No available challenges at the moment.
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
              <article
                v-for="c in topChallenges"
                :key="c.id"
                class="flex cursor-pointer gap-4 rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm transition hover:shadow-main"
                @click="openChallenge(c.id)"
              >
                <div
                  class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded bg-dark-white"
                >
                  <img
                    v-if="resolvedImage(c)"
                    :src="resolvedImage(c)"
                    :alt="c.name"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    @error="onImgError"
                  />
                  <span v-else class="text-xs text-cool-gray">No image</span>
                </div>
                <div class="min-w-0 flex-1">
                  <h3 class="truncate font-alexandria text-base font-semibold text-blue-black">{{ c.name }}</h3>
                  <p class="line-clamp-2 text-sm text-cool-gray">
                    {{ c.shortDescription || '—' }}
                  </p>
                  <div class="mt-1 flex items-center gap-2 text-xs text-cool-gray">
                    <span v-if="c.maxPoints != null">{{ c.maxPoints }} pts</span>
                    <span v-if="c.isAnswered" class="rounded bg-pistachio/20 px-2 py-0.5 text-pistachio">Answered</span>
                    <span v-else-if="c.isExpired" class="rounded bg-vibrant-coral/10 px-2 py-0.5 text-vibrant-coral"
                      >Expired</span
                    >
                  </div>
                </div>
              </article>
            </div>
          </div>
        </div>

        <!-- Leaderboard -->
        <div class="lg:col-span-1">
          <div class="mb-3 flex items-center justify-between">
            <h2 class="font-bebas-neue text-2xl text-blue-black">Leaderboard (Top 5)</h2>
            <button class="text-sm text-blue-black/70 hover:underline" @click="loadLeaderboards" :disabled="loadingLb">
              Refresh
            </button>
          </div>

          <div v-if="loadingLb" class="text-cool-gray">Loading leaderboard…</div>
          <div v-else-if="errorLb" class="rounded-xl border border-amber-300 bg-amber-50 p-3 text-amber-800">
            {{ errorLb }}
          </div>
          <ul v-else class="divide-y rounded-xl border border-charcoal/10 bg-white shadow-sm">
            <li v-for="(p, i) in top5" :key="p.playerId || i" class="flex items-center gap-3 p-3">
              <div
                class="grid h-8 w-8 shrink-0 place-items-center rounded-full text-blue-black"
                :class="badgeBg(i)"
                :title="p.isMyself ? 'This is you' : undefined"
              >
                <span class="text-sm font-semibold">{{ p.rank ?? i + 1 }}</span>
              </div>
              <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium text-blue-black">
                  {{ p.playerName }} <span v-if="p.isMyself" class="text-xs text-vibrant-coral">(you)</span>
                </p>
                <p class="text-xs text-cool-gray">{{ p.points }} pts · {{ p.challengesAnswered }} challenges</p>
              </div>
            </li>
            <li v-if="top5.length === 0" class="p-3 text-sm text-cool-gray">No leaderboard data.</li>
          </ul>
        </div>
      </div>

      <!-- Skills -->
      <div class="mt-8">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Skills</h2>
        <div
          v-if="(overall.skills?.length || 0) === 0"
          class="rounded-xl border border-charcoal/10 bg-dark-white p-4 text-cool-gray"
        >
          No skills evaluated yet.
        </div>
        <ul v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          <li class="rounded-xl border border-charcoal/10 bg-white p-4" v-for="(s, i) in overall.skills" :key="i">
            <div class="flex items-center justify-between">
              <p class="font-alexandria font-semibold text-blue-black">{{ s.name }}</p>
              <div class="flex items-center gap-2 text-sm">
                <span class="font-extrabold text-blue-black">{{ s.percentage }}%</span>
                <span :class="s.percentageChange != null && s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrant-coral'">
                  <template v-if="s.percentageChange != null">
                    {{ s.percentageChange >= 0 ? '+' : '' }}{{ s.percentageChange }}%
                  </template>
                </span>
              </div>
            </div>
            <div class="mt-2 h-2 w-full overflow-hidden rounded bg-dark-white">
              <div class="h-full bg-pistachio" :style="{ width: Math.min(100, Math.max(0, s.percentage)) + '%' }" />
            </div>
          </li>
        </ul>
      </div>
    </template>

    <!-- Modal with questions -->
    <ChallengeModal
      v-if="showModal"
      :show="showModal"
      :challenge-id="selectedId"
      @close="showModal = false"
      @submitted="afterSubmit"
    />
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useProfile } from '@/composables/useProfile';
import { useAuth } from '@/composables/useAuth';
import { useChallenges } from '@/composables/useChallenges';
import { resolvedImage, onImgError } from '@/utils/imageHelpers';
import ChallengeModal from '@/components/ChallengeModal.vue';
import { apiFetch } from '@/api/http';

/* Profile */
const { me, loading: loadingProfile, error: errorProfile, load } = useProfile();
const { user } = useAuth();
if (user.value && !me.value) me.value = user.value;
onMounted(load);

const profile = computed(() => ({
  id: me.value?.id ?? user.value?.id ?? null,
  name: me.value?.name ?? user.value?.name ?? '',
  email: me.value?.email ?? user.value?.email ?? '',
  registeredAt: me.value?.registeredAt ?? user.value?.registeredAt ?? null,
  availableChallenges: me.value?.availableChallenges ?? 0,
  overallStatistics: me.value?.overallStatistics ?? user.value?.overallStatistics ?? null,
}));
const overall = computed(() => profile.value.overallStatistics ?? {
  rank: null, challengesAnswered: 0, points: 0, skills: [],
});
const initials = computed(() => {
  const name = profile.value.name?.trim();
  if (name) {
    const parts = name.split(/\s+/).filter(Boolean);
    return ((parts[0]?.[0] || '') + (parts[parts.length - 1]?.[0] || '')).toUpperCase();
  }
  return (profile.value.email?.[0] || '?').toUpperCase();
});

/* Challenges */
const { challenges, loading: loadingChallenges, error: errorChallenges, loadChallenges } = useChallenges();
onMounted(() => loadChallenges());

// filtrování výzev (aby "Available" opravdu sedělo)
const availableChallenges = computed(() =>
  (challenges.value || []).filter(c => c.isStarted && !c.isExpired && !c.isAnswered)
);
const completedChallenges = computed(() =>
  (challenges.value || []).filter(c => c.isAnswered)
);
const expiredChallenges = computed(() =>
  (challenges.value || []).filter(c => c.isExpired && !c.isAnswered)
);
// zobrazíme jen prvních 5 dostupných
const topChallenges = computed(() => availableChallenges.value.slice(0, 5));

/* Modal */
const showModal = ref(false);
const selectedId = ref(null);
function openChallenge(id) {
  selectedId.value = id;
  showModal.value = true;
}
function afterSubmit() {
  showModal.value = false;
  loadChallenges(); // refresh po odeslání
}

/* Leaderboards */
const lb = ref([]);
const loadingLb = ref(false);
const errorLb = ref('');
async function loadLeaderboards() {
  loadingLb.value = true;
  errorLb.value = '';
  try {
    const data = await apiFetch('/api/leaderboards', { auth: true });
    const list = Array.isArray(data) ? data : (Array.isArray(data?.member) ? data.member : []);
    lb.value = list;
  } catch (e) {
    errorLb.value = e?.message || 'Failed to load leaderboard';
  } finally {
    loadingLb.value = false;
  }
}
onMounted(loadLeaderboards);
const top5 = computed(() => (lb.value || []).slice(0, 5));

/* UI helpers */
function badgeBg(index) {
  // decentní odstíny, ladí s tvými barvami
  return [
    'bg-golden-yellow/30 text-blue-black',
    'bg-dark-white text-blue-black',
    'bg-amber-200 text-charcoal',
  ][index] || 'bg-blue-black/10 text-blue-black';
}
</script>