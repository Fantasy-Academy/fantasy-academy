<template>
  <section class="mx-auto max-w-full px-8 py-10">
    <!-- Header -->
    <div
      class="mb-8 flex flex-wrap items-center justify-between gap-4 rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-6 text-white shadow-main">
      <div>
        <h1 class="font-bebas-neue text-4xl tracking-wide">Leaderboard</h1>
        <p class="font-alexandria text-dark-white/80 text-lg">
          See how you rank among other players
        </p>
      </div>
    </div>

    <!-- States -->
    <div v-if="initialLoading" class="text-cool-gray">Loading leaderboard…</div>
    <div v-else-if="error"
      class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-4 text-vibrant-coral">
      {{ error }}
    </div>

    <template v-else>
      <!-- List -->
      <div class="overflow-hidden rounded-2xl border border-charcoal/10 bg-white shadow-sm">
        <div class="hidden grid-cols-12 gap-6 bg-dark-white px-8 py-4 text-left text-base text-cool-gray lg:grid">
          <div class="col-span-2">Rank</div>
          <div class="col-span-6">Player</div>
          <div class="col-span-2">FAPs</div>
          <div class="col-span-2">Answered</div>
        </div>

        <ul class="divide-y">
          <li v-for="(p, i) in items" :key="p.playerId || i"
            class="grid grid-cols-12 gap-6 px-8 py-6 hover:bg-dark-white/50 sm:items-center cursor-pointer focus:outline-none focus:bg-dark-white/60"
            role="button" tabindex="0" :aria-label="`Open player ${p.playerName}`" @click="onRowClick(p.playerId)"
            @keydown.enter.prevent="onRowClick(p.playerId)" @keydown.space.prevent="onRowClick(p.playerId)">
            <!-- Rank -->
            <div class="col-span-12 mb-3 flex items-center gap-4 sm:col-span-2 sm:mb-0">
              <div class="grid h-12 w-12 place-items-center rounded-full text-blue-black text-lg"
                :class="badgeBg(p.rank, i)">
                {{ (p.rank ?? i + 1) }}
              </div>
              <!-- Rank change -->
              <span v-if="p.rankChange !== null" :class="changeClass(p.rankChange)" class="text-sm ml-2">
                {{ formatChange(p.rankChange) }}
              </span>
            </div>

            <!-- Player -->
            <div class="col-span-12 sm:col-span-6">
              <div
                class="inline-flex max-w-full items-center gap-4 rounded-lg px-2 py-1 font-alexandria text-blue-black">
                <div class="grid h-12 w-12 shrink-0 place-items-center rounded-full"
                  :class="p.isMyself ? 'bg-golden-yellow text-blue-black' : 'bg-dark-white text-blue-black'">
                  {{ monogram(p.playerName) }}
                </div>
                <span class="truncate text-lg font-medium">
                  {{ p.playerName }}
                  <span v-if="p.isMyself" class="text-sm text-vibrant-coral">(you)</span>
                </span>
              </div>
            </div>

            <!-- Points -->
            <div class="col-span-6 sm:col-span-2">
              <p class="text-lg font-bold text-blue-black">{{ p.points }}</p>
              <p v-if="p.pointsChange !== null" :class="changeClass(p.pointsChange)" class="text-sm">
                {{ formatChange(p.pointsChange) }}
              </p>
              <p class="text-xs text-cool-gray sm:hidden">FAPs</p>
            </div>

            <!-- Answered -->
            <div class="col-span-6 sm:col-span-2">
              <p class="text-lg font-bold text-blue-black">{{ p.challengesAnswered }}</p>
              <p class="text-xs text-cool-gray sm:hidden">Answered</p>
            </div>
          </li>

          <li v-if="items.length === 0" class="px-8 py-10 text-center text-cool-gray text-lg">No data.</li>
        </ul>
      </div>

      <!-- See more -->
      <div class="mt-8 flex items-center justify-center">
        <button v-if="hasMore"
          class="rounded-lg border border-charcoal/20 bg-white px-8 py-3 font-semibold text-blue-black hover:bg-dark-white disabled:opacity-50"
          :disabled="loadingMore" @click="loadMore">
          <span v-if="loadingMore">Loading…</span>
          <span v-else>See more</span>
        </button>
        <p v-else class="text-base text-cool-gray">You’ve reached the end.</p>
      </div>
    </template>
  </section>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { apiGetLeaderboards } from '@/api/leaderboards';
import { toFriendlyError } from '@/utils/errorHandler';

document.title = 'Fantasy Academy | Leaderboard';

const router = useRouter();

const items = ref([]);
const initialLoading = ref(false);
const loadingMore = ref(false);
const error = ref('');

const page = ref(1);
const lastPage = ref(1);

const hasMore = computed(() => page.value < lastPage.value);

function monogram(name) {
  if (!name) return '?';
  const parts = String(name).trim().split(/\s+/);
  const a = parts[0]?.[0] || '';
  const b = parts.length > 1 ? parts[parts.length - 1][0] || '' : '';
  return (a + b).toUpperCase();
}

function badgeBg(rank, indexInList) {
  const r = rank ?? indexInList + 1;
  if (r === 1) return 'bg-golden-yellow';
  if (r === 2) return 'bg-gray-200';
  if (r === 3) return 'bg-amber-200';
  return 'bg-dark-white';
}

function onRowClick(playerId) {
  if (!playerId) return;
  router.push(`/player/${playerId}`);
}

async function fetchPage(n) {
  const { items: list, lastPage: lp } = await apiGetLeaderboards(n);
  lastPage.value = lp || n; // fallback
  return list;
}

async function loadInitial() {
  initialLoading.value = true;
  error.value = '';
  try {
    page.value = 1;
    const list = await fetchPage(page.value);
    items.value = list ?? [];
  } catch (e) {
    const fe = toFriendlyError(e);
    console.warn('[LeaderboardPage] loadInitial FAIL', {
      status: e?.status,
      message: fe.userMessage,
      rawMessage: e?.message,
      data: e?.data,
    });
    error.value = fe.userMessage || 'Nepodařilo se načíst žebříček.';
  } finally {
    initialLoading.value = false;
  }
}

async function loadMore() {
  if (!hasMore.value || loadingMore.value) return;
  loadingMore.value = true;
  error.value = '';
  try {
    const next = page.value + 1;
    const list = await fetchPage(next);

    // Optional de-dup by playerId if backend can repeat entries
    const seen = new Set(items.value.map(x => x.playerId));
    const merged = [...items.value];
    for (const it of list) {
      if (!seen.has(it.playerId)) {
        merged.push(it);
        seen.add(it.playerId);
      }
    }
    items.value = merged;
    page.value = next;
  } catch (e) {
    const fe = toFriendlyError(e);
    console.warn('[LeaderboardPage] loadMore FAIL', {
      status: e?.status,
      message: fe.userMessage,
      rawMessage: e?.message,
      data: e?.data,
    });
    error.value = fe.userMessage || 'Nepodařilo se načíst více dat.';
  } finally {
    loadingMore.value = false;
  }
}

function formatChange(value) {
  if (value === 0 || value === null || value === undefined) return '';
  return value > 0 ? `↑${value}` : `↓${Math.abs(value)}`;
}

function changeClass(value) {
  if (value > 0) return 'text-pistachio';
  if (value < 0) return 'text-vibrant-coral';
  return 'text-cool-gray';
}

onMounted(loadInitial);
</script>