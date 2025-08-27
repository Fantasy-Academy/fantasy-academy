<!-- src/views/HomePage.vue -->
<template>
  <section class="px-6 py-10">

    <!-- HERO -->
    <div
      class="mx-auto max-w-6xl rounded-2xl bg-gradient-to-r from-blue-black to-charcoal p-8 text-dark-white shadow-main">
      <div class="grid gap-8 md:grid-cols-2 md:items-center">
        <div>
          <h1 class="font-bebas-neue text-5xl tracking-wide">
            Level up your skills with Fantasy Academy
          </h1>
          <p class="mt-3 text-dark-white/90 font-alexandria">
            Tackle challenges, earn points, climb the leaderboard, and track your progress across seasons.
          </p>

          <div class="mt-6 flex flex-wrap gap-3">
            <router-link to="/challenges"
              class="inline-flex items-center justify-center rounded-lg bg-golden-yellow px-5 py-2 font-alexandria font-semibold text-blue-black shadow-sm hover:opacity-90 transition">
              Browse Challenges
            </router-link>

            <router-link v-if="isAuthenticated" to="/dashboard"
              class="inline-flex items-center justify-center rounded-lg border border-dark-white/30 px-5 py-2 font-alexandria font-semibold text-dark-white hover:bg-dark-white/10 transition">
              Go to Dashboard
            </router-link>
            <router-link v-else to="/signup"
              class="inline-flex items-center justify-center rounded-lg border border-dark-white/30 px-5 py-2 font-alexandria font-semibold text-dark-white hover:bg-dark-white/10 transition">
              Create Account
            </router-link>
          </div>

          <div class="mt-4 flex items-center gap-3 text-sm text-dark-white/80 font-alexandria">
            <span class="inline-flex h-2 w-2 rounded-full bg-pistachio"></span> New challenges weekly
            <span class="inline-flex h-2 w-2 rounded-full bg-golden-yellow"></span> Season-based progress
            <span class="inline-flex h-2 w-2 rounded-full bg-vibrant-coral"></span> Live leaderboard
          </div>
        </div>

        <div class="rounded-xl bg-dark-white/10 p-4 border border-dark-white/20">
          <div class="grid grid-cols-3 gap-3">
            <div class="rounded-xl bg-dark-white/20 p-4 text-center">
              <p class="text-sm text-dark-white/80 font-alexandria">Active <br /> Players</p>
              <p class="mt-1 text-3xl font-bold text-golden-yellow">{{ playerCount }}</p>
            </div>
            <div class="rounded-xl bg-dark-white/20 p-4 text-center">
              <p class="text-sm text-dark-white/80 font-alexandria">Total Challenges</p>
              <p class="mt-1 text-3xl font-bold text-golden-yellow">{{ allChallenges }}</p>
            </div>
            <div class="rounded-xl bg-dark-white/20 p-4 text-center">
              <p class="text-sm text-dark-white/80 font-alexandria">Live <br /> Challenges</p>
              <p class="mt-1 text-3xl font-bold text-golden-yellow">{{ activeChallengesCount }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- QUICK ACTIONS -->
    <div class="mx-auto mt-10 max-w-6xl grid gap-4 md:grid-cols-3">
      <router-link to="/challenges"
        class="group rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm hover:shadow-main transition">
        <h3 class="text-xl font-alexandria font-semibold text-blue-black flex items-center gap-2">
          <span class="inline-grid h-8 w-8 place-items-center rounded-full bg-pistachio/20 text-pistachio">🏆</span>
          Explore Challenges
        </h3>
        <p class="mt-2 text-sm text-cool-gray">
          Solve tasks across multiple skills. New sets appear during the season.
        </p>
        <span class="mt-3 inline-block text-vibrant-coral font-semibold">Start now →</span>
      </router-link>

      <router-link :to="isAuthenticated ? '/dashboard' : '/signup'"
        class="group rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm hover:shadow-main transition">
        <h3 class="text-xl font-alexandria font-semibold text-blue-black flex items-center gap-2">
          <span
            class="inline-grid h-8 w-8 place-items-center rounded-full bg-golden-yellow/30 text-blue-black">📊</span>
          {{ isAuthenticated ? 'Your Dashboard' : 'Create Account' }}
        </h3>
        <p class="mt-2 text-sm text-cool-gray">{{ isAuthenticated ? 'See your points, rank and active challenges.' :
          'Join and save your progressacrossseasons.' }}</p>
        <span class="mt-3 inline-block text-vibrant-coral font-semibold">
          {{ isAuthenticated ? 'View dashboard →' : 'Sign up →' }}
        </span>
      </router-link>

      <router-link to="/profile"
        class="group rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm hover:shadow-main transition">
        <h3 class="text-xl font-alexandria font-semibold text-blue-black flex items-center gap-2">
          <span
            class="inline-grid h-8 w-8 place-items-center rounded-full bg-vibrant-coral/10 text-vibrant-coral">🧑‍🚀</span>
          Profile & Stats
        </h3>
        <p class="mt-2 text-sm text-cool-gray">
          Manage your account and track your skill growth in detail.
        </p>
        <span class="mt-3 inline-block text-vibrant-coral font-semibold">Open profile →</span>
      </router-link>
    </div>

    <!-- HOW IT WORKS -->
    <div class="mx-auto mt-12 max-w-6xl">
      <h2 class="font-bebas-neue text-3xl tracking-wide text-blue-black">How it works</h2>
      <div class="mt-4 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <p class="text-sm font-alexandria text-cool-gray">Step 1</p>
          <h3 class="mt-1 font-alexandria text-xl font-semibold text-blue-black">Pick a challenge</h3>
          <p class="mt-2 text-sm text-cool-gray">
            Choose from single-select, multi-select, text, numeric, or sort questions.
          </p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <p class="text-sm font-alexandria text-cool-gray">Step 2</p>
          <h3 class="mt-1 font-alexandria text-xl font-semibold text-blue-black">Submit your answers</h3>
          <p class="mt-2 text-sm text-cool-gray">
            Answers are validated on the client and sent to the API securely.
          </p>
        </div>
        <div class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <p class="text-sm font-alexandria text-cool-gray">Step 3</p>
          <h3 class="mt-1 font-alexandria text-xl font-semibold text-blue-black">Earn points & climb ranks</h3>
          <p class="mt-2 text-sm text-cool-gray">
            Score points, improve skills and watch your progress over seasons.
          </p>
        </div>
      </div>
    </div>

    <!-- BENEFITS -->
    <div class="mx-auto mt-12 max-w-6xl">
      <h2 class="font-bebas-neue text-3xl tracking-wide text-blue-black">Why Fantasy Academy?</h2>
      <ul class="mt-4 grid gap-4 md:grid-cols-2">
        <li class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <h3 class="font-alexandria text-lg font-semibold text-blue-black">Real API, real data</h3>
          <p class="mt-1 text-sm text-cool-gray">
            Challenges & answers are fetched and submitted through the OpenAPI-powered backend.
          </p>
        </li>
        <li class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <h3 class="font-alexandria text-lg font-semibold text-blue-black">Season-based progression</h3>
          <p class="mt-1 text-sm text-cool-gray">
            Track your rank and skill evolution by season, not just overall.
          </p>
        </li>
        <li class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <h3 class="font-alexandria text-lg font-semibold text-blue-black">Friendly UI & validation</h3>
          <p class="mt-1 text-sm text-cool-gray">
            Clear hints, helpful errors, and accessible controls to keep you in flow.
          </p>
        </li>
        <li class="rounded-2xl border border-charcoal/10 bg-white p-5 shadow-sm">
          <h3 class="font-alexandria text-lg font-semibold text-blue-black">Custom theme</h3>
          <p class="mt-1 text-sm text-cool-gray">
            A consistent look powered by your Tailwind palette and fonts.
          </p>
        </li>
      </ul>
    </div>

    <!-- CALLOUT -->
    <div class="mx-auto my-12 max-w-6xl rounded-2xl border border-charcoal/10 bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
          <h3 class="font-alexandria text-xl font-semibold text-blue-black">
            Ready to start your next challenge?
          </h3>
          <p class="text-sm text-cool-gray">
            Choose a challenge, submit answers and collect points today.
          </p>
        </div>
        <router-link to="/challenges"
          class="inline-flex items-center justify-center rounded-lg bg-vibrant-coral px-5 py-2 font-alexandria font-semibold text-white hover:bg-vibrant-coral/90 transition shadow-sm">
          Browse Challenges →
        </router-link>
      </div>
    </div>

  </section>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import { useChallenges } from '@/composables/useChallenges';
import { apiGetLeaderboards } from '@/api/leaderboards';
import { computed, onMounted, ref } from 'vue';

const { challenges, totalCount, loadChallenges } = useChallenges();
const playerCount = ref(null);

onMounted(async () => {
  loadChallenges({ auth: false });

  try {
    const { totalItems } = await apiGetLeaderboards({ page: 1, auth: false });
    playerCount.value = Number.isFinite(totalItems) ? totalItems : 0;
  } catch (e) {
    playerCount.value = null;
  }
});

const activeChallengesCount = computed(() => {
  const list = challenges.value || [];
  return list.filter(c => c?.isStarted && !c.isExpired).length;
});

const allChallenges = computed(() => totalCount.value || 0);

const { isAuthenticated } = useAuth();
document.title = 'Fantasy Academy | Home';
</script>