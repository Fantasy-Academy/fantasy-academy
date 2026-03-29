<template>
  <section class="bg-dark-white">

    <!-- HERO -->
    <div class="relative overflow-hidden min-h-[780px] bg-blue-black text-dark-white" :style="{
      backgroundImage: `linear-gradient(to bottom, rgba(12, 26, 42, 0.38), rgba(12, 26, 42, 0.82)), url(${heroImage})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    }">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14 lg:pt-16">
        <div class="flex min-h-[780px] flex-col justify-between py-10 sm:py-14 lg:py-20">
          <!-- HERO CONTENT -->
          <div class="mx-auto flex w-full max-w-4xl flex-1 flex-col items-center justify-center text-center">
            <p class="font-alexandria text-xs sm:text-sm uppercase tracking-[0.35em] text-dark-white/70">
              Skill challenges · Seasons · Leaderboard
            </p>

            <h1 class="mt-6 font-bebas-neue text-6xl leading-none tracking-wider sm:text-7xl lg:text-8xl">
              Fantasy Academy
            </h1>

            <p class="mt-4 max-w-2xl font-alexandria text-sm sm:text-base lg:text-lg text-dark-white/85">
              Tackle skill challenges, collect points, climb the leaderboard and
              track your progress across every season.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-4">
              <router-link to="/challenges"
                class="inline-flex items-center justify-center rounded-xl bg-dark-purple px-7 py-3 font-alexandria text-sm sm:text-base font-semibold text-dark-white transition hover:opacity-90">
                Browse Challenges
              </router-link>

              <router-link v-if="isAuthenticated" to="/dashboard"
                class="inline-flex items-center justify-center rounded-xl border border-dark-white/35 px-7 py-3 font-alexandria text-sm sm:text-base font-semibold text-dark-white transition hover:bg-dark-white/10">
                Go to Dashboard
              </router-link>

              <router-link v-else to="/signup"
                class="inline-flex items-center justify-center rounded-xl border border-dark-white/35 px-7 py-3 font-alexandria text-sm sm:text-base font-semibold text-dark-white transition hover:bg-dark-white/10">
                Create Account
              </router-link>
            </div>

            <ul
              class="mt-8 flex flex-wrap items-center justify-center gap-x-6 gap-y-3 font-alexandria text-xs sm:text-sm text-dark-white/80">
              <li class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-light-purple"></span>
                New challenges weekly
              </li>
              <li class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-dark-purple"></span>
                Season-based progress
              </li>
              <li class="inline-flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-light-purple"></span>
                Live leaderboard
              </li>
            </ul>
          </div>

          <!-- HERO STATS -->
          <div class="mx-auto mt-10 grid w-full max-w-6xl grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-dark-white/15 bg-dark-white/10 p-6 text-center backdrop-blur-sm">
              <p class="font-alexandria text-xs uppercase tracking-wider text-dark-white/70">
                Active Players
              </p>
              <p class="mt-3 font-bebas-neue text-5xl leading-none tracking-wide">
                {{ playerCount ?? '—' }}
              </p>
            </div>

            <div class="rounded-2xl border border-dark-white/15 bg-dark-white/10 p-6 text-center backdrop-blur-sm">
              <p class="font-alexandria text-xs uppercase tracking-wider text-dark-white/70">
                Total Challenges
              </p>
              <p class="mt-3 font-bebas-neue text-5xl leading-none tracking-wide">
                {{ allChallenges }}
              </p>
            </div>

            <div class="rounded-2xl border border-dark-white/15 bg-dark-white/10 p-6 text-center backdrop-blur-sm">
              <p class="font-alexandria text-xs uppercase tracking-wider text-dark-white/70">
                Live Challenges
              </p>
              <p class="mt-3 font-bebas-neue text-5xl leading-none tracking-wide">
                {{ activeChallengesCount }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- HIGHLIGHT SECTION -->
    <div class="bg-light-purple">
      <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-20">
        <!-- visual placeholder -->
        <div class="relative flex min-h-[340px] items-center justify-center overflow-hidden rounded-[2rem]">
          <img :src="messiImage" alt="Hero image" class="absolute inset-0 h-full w-full object-cover" />
          <div
            class="absolute inset-0 rounded-[2rem] border border-blue-black/10 bg-gradient-to-br from-white/10 to-white/5">
          </div>
        </div>

        <!-- text -->
        <div class="flex items-center">
          <div class="max-w-xl">
            <p class="font-alexandria text-sm uppercase tracking-[0.3em] text-blue-black/60">
              Challenges
            </p>
            <h2 class="mt-3 font-bebas-neue text-5xl leading-none tracking-wide text-blue-black sm:text-6xl">
              Players
            </h2>

            <p class="mt-5 font-alexandria text-sm leading-7 text-blue-black/80 sm:text-base">
              Explore challenge sets across different skills, improve your score,
              and build season progress step by step. Everything is designed to
              feel competitive, clear, and motivating.
            </p>

            <p class="mt-4 font-alexandria text-sm leading-7 text-blue-black/75 sm:text-base">
              Jump into active tasks, review your stats, and keep moving up the
              leaderboard with every correct submission.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
              <router-link to="/challenges"
                class="inline-flex items-center justify-center rounded-xl border border-dark-purple bg-dark-purple px-6 py-3 font-alexandria text-sm font-semibold text-dark-white transition hover:opacity-90">
                Explore Challenges
              </router-link>

              <router-link to="/profile"
                class="inline-flex items-center justify-center rounded-xl border border-dark-purple/30 px-6 py-3 font-alexandria text-sm font-semibold text-blue-black transition hover:bg-blue-black/5">
                Open Profile
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- PROGRESS SECTION -->
    <div class="relative overflow-hidden bg-charcoal text-dark-white" style="
        background-image:
          linear-gradient(to right, rgba(18, 18, 18, 0.82), rgba(18, 18, 18, 0.58)),
          url('/your-secondary-image.jpg');
        background-size: cover;
        background-position: center;
      ">
      <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="max-w-2xl">
          <p class="font-alexandria text-sm uppercase tracking-[0.3em] text-dark-white/60">
            Progress
          </p>
          <h2 class="mt-3 font-bebas-neue text-5xl leading-none tracking-wide sm:text-6xl">
            Train Smarter
          </h2>

          <p class="mt-6 font-alexandria text-sm leading-7 text-dark-white/80 sm:text-base">
            Validate answers, improve your ranking, and keep your momentum across
            seasons. The platform is built for clarity, speed and measurable
            progression.
          </p>

          <div class="mt-8 flex flex-wrap gap-4">
            <router-link to="/challenges"
              class="inline-flex items-center justify-center rounded-xl bg-dark-purple px-6 py-3 font-alexandria text-sm font-semibold text-dark-white transition hover:opacity-90">
              Explore Challenges
            </router-link>

            <router-link :to="isAuthenticated ? '/dashboard' : '/signup'"
              class="inline-flex items-center justify-center rounded-xl border border-dark-white/30 px-6 py-3 font-alexandria text-sm font-semibold text-dark-white transition hover:bg-dark-white/10">
              {{ isAuthenticated ? 'Start from Dashboard' : 'Start Your Journey' }}
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- FINAL CTA ONLY -->
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
      <div class="rounded-[2rem] bg-dark-purple px-6 py-10 sm:px-8 lg:px-12">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
          <div class="max-w-2xl">
            <p class="font-alexandria text-sm uppercase tracking-[0.3em] text-dark-white/70">
              Ready?
            </p>
            <h3 class="mt-2 font-bebas-neue text-4xl tracking-wide text-dark-white sm:text-5xl">
              Start your challenge today
            </h3>
            <p class="mt-3 font-alexandria text-sm leading-6 text-dark-white/85">
              Choose a challenge, submit your answers and begin collecting points.
            </p>
          </div>

          <router-link to="/challenges"
            class="inline-flex items-center justify-center rounded-xl bg-light-purple px-7 py-3 font-alexandria text-sm font-semibold text-blue-black transition hover:opacity-90">
            Browse Challenges →
          </router-link>
        </div>
      </div>
    </div>

  </section>
</template>

<script setup>
import { useAuth } from '@/composables/useAuth';
import { useChallenges } from '@/composables/useChallenges';
import { apiGetLeaderboards } from '@/api/leaderboards';
import { computed, onMounted, ref } from 'vue';
import heroImage from '../assets/hero.png';
import messiImage from '../assets/messi.png';



const { challenges, totalCount, loadChallenges } = useChallenges();
const playerCount = ref(null);

onMounted(async () => {
  loadChallenges({ auth: false });
  try {
    const { totalItems } = await apiGetLeaderboards({ page: 1, auth: false });
    playerCount.value = Number.isFinite(totalItems) ? totalItems : 0;
  } catch {
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