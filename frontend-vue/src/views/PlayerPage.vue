<template>
  <section class="mx-auto max-w-5xl px-4 py-8">
    <!-- Header -->
    <div class="mb-6 rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-5 text-white shadow-main">
      <div class="flex items-center gap-4">
        <div
          class="grid h-16 w-16 place-items-center rounded-full bg-golden-yellow text-2xl font-extrabold text-blue-black"
        >
          {{ initials }}
        </div>
        <div class="min-w-0">
          <h1 class="font-bebas-neue text-3xl tracking-wide">{{ player?.name || 'Player' }}</h1>
          <p class="font-alexandria text-dark-white/80">
            Registered: <span class="font-semibold text-white">{{ registered }}</span>
          </p>
        </div>
        <div class="ml-auto">
          <router-link
            to="/leaderboard"
            class="rounded-lg border border-dark-white/30 bg-white px-4 py-2 font-semibold text-blue-black hover:bg-dark-white shadow-sm"
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

    <template v-else-if="player">
      <!-- KPIs -->
      <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Total points</p>
          <p class="mt-1 text-3xl font-bold text-blue-black">{{ overall.points ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Rank</p>
          <p class="mt-1 text-3xl font-bold text-blue-black">{{ overall.rank ?? '—' }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Answered challenges</p>
          <p class="mt-1 text-3xl font-bold text-blue-black">{{ overall.challengesAnswered ?? 0 }}</p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-sm">
          <p class="text-sm text-cool-gray font-alexandria">Skills</p>
          <p class="mt-1 text-3xl font-bold text-blue-black">{{ (overall.skills?.length || 0) }}</p>
        </div>
      </div>

      <!-- Skills -->
      <div v-if="overall.skills?.length" class="mb-10">
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Skills</h2>
        <ul class="space-y-3">
          <li v-for="(s, i) in overall.skills" :key="i" class="rounded-xl border border-charcoal/10 bg-white p-4 shadow-sm">
            <div class="flex items-center justify-between">
              <p class="font-semibold text-blue-black font-alexandria">{{ s.name }}</p>
              <div class="flex items-center gap-3 text-sm font-alexandria">
                <span class="font-extrabold text-blue-black">{{ s.percentage }}%</span>
                <span v-if="s.percentageChange != null" :class="s.percentageChange >= 0 ? 'text-pistachio' : 'text-vibrant-coral'">
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

      <!-- Seasonal stats -->
      <div>
        <h2 class="mb-3 font-bebas-neue text-2xl text-blue-black">Seasons</h2>
        <div class="overflow-x-auto rounded-xl border border-charcoal/10 bg-white shadow-sm">
          <table class="min-w-full text-left text-sm">
            <thead class="bg-dark-white text-cool-gray">
              <tr>
                <th class="px-4 py-2 font-semibold">Season</th>
                <th class="px-4 py-2 font-semibold">Rank</th>
                <th class="px-4 py-2 font-semibold">Challenges</th>
                <th class="px-4 py-2 font-semibold">Points</th>
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
                    <span
                      v-for="(sk, j) in (s.skills || []).slice(0, 3)"
                      :key="j"
                      class="inline-flex items-center gap-1 rounded-full bg-dark-white px-2 py-0.5 text-xs font-semibold text-blue-black"
                    >
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
    </template>
  </section>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { apiGetPlayer } from '@/api/players';

const route = useRoute();
const player = ref(null);
const loading = ref(false);
const error = ref('');

onMounted(load);
async function load() {
  loading.value = true;
  error.value = '';
  try {
    player.value = await apiGetPlayer(route.params.id);
  } catch (e) {
    error.value = e?.message || 'Failed to load player';
  } finally {
    loading.value = false;
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

const overall = computed(() => player.value?.overallStatistics ?? {
  rank: null, challengesAnswered: 0, points: 0, skills: []
});

const initials = computed(() => {
  const name = player.value?.name?.trim() || '';
  if (!name) return '?';
  const parts = name.split(/\s+/).filter(Boolean);
  return ((parts[0]?.[0] || '') + (parts[parts.length - 1]?.[0] || '')).toUpperCase();
});
</script>