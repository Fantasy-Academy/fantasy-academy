<template>
  <section class="w-full max-w-6xl mx-auto px-4 py-8">
    <header class="mb-6 rounded-lg
                sm:p-5 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">

      <div class="flex flex-col gap-2">
        <GameweekStatus />
      </div>

      <!-- Filter pills -->
      <nav class="flex flex-wrap gap-2 sm:gap-3">
        <button v-for="opt in filters" :key="opt.value" type="button" @click="activeFilter = opt.value" class="px-4 py-2 rounded-full text-sm font-semibold border transition-all
             focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-dark-purple/60" :class="activeFilter === opt.value
              ? 'bg-dark-purple text-white border-dark-purple shadow-sm'
              : 'bg-white text-blue-black border-charcoal/20 hover:bg-dark-white hover:shadow-sm'">

          {{ opt.label }}

          <span v-if="opt.badge != null"
            class="ml-2 rounded-full bg-dark-white px-2 py-0.5 text-xs font-bold text-blue-black">
            {{ opt.badge }}
          </span>
        </button>
      </nav>

    </header>
    <!-- States -->
    <p v-if="error" class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral">
      {{ error }}
    </p>
    <p v-else-if="loading" class="text-cool-gray">Loading…</p>
    <!-- Empty state per filter -->
    <div v-else-if="filteredChallenges.length === 0"
      class="rounded-xl border border-charcoal/10 bg-dark-white p-6 text-cool-gray">
      <span v-if="activeFilter === 'all'">No challenges yet.</span>
      <span v-else-if="activeFilter === 'active'">No active challenges right now.</span>
      <span v-else-if="activeFilter === 'completed'">You haven’t completed any challenges yet.</span>
      <span v-else-if="activeFilter === 'expired'">No expired challenges.</span>
    </div>
    <!-- List -->
    <div v-else   class="grid w-full gap-8 mx-auto
         grid-cols-1
         sm:grid-cols-2
         sm:[grid-template-columns:repeat(2,1fr)]">
      <ChallengeCard v-for="challenge in filteredChallenges" :key="challenge.id" :challenge="challenge"
        @select="openChallenge(challenge.id)" />
    </div>

    <!-- Modal -->
    <ChallengeModal v-if="showModal" :show="showModal" :challenge-id="selectedId" @close="showModal = false"
      @submitted="handleSubmitted" />
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useChallenges } from '@/composables/useChallenges';
import ChallengeCard from '@/components/ChallengeCard.vue';
import ChallengeModal from '@/components/ChallengeModal.vue';
import GameweekStatus from '../components/GameweekStatus.vue';

const { challenges, loading, error, loadChallenges } = useChallenges();

document.title = 'Fantasy Academy | Challenges';

const showModal = ref(false);
const selectedId = ref(null);

// filter state: all | active | completed | expired
const activeFilter = ref('active');

// helpers to derive status from challenge
const isCompleted = (challenge) => !!challenge.isAnswered;
const isExpired = (challenge) => !!challenge.isExpired;
const isActive = (challenge) => {
  // Active = started, not expired, not completed
  const started = challenge.isStarted ?? true;
  return started && !isExpired(challenge) && !isCompleted(challenge);
};

// filtered list
const filteredChallenges = computed(() => {
  const list = challenges.value || [];
  switch (activeFilter.value) {
    case 'active': return list.filter(isActive);
    case 'completed': return list.filter(isCompleted);
    case 'expired': return list.filter(isExpired);
    default: return list;
  }
});

// pill labels + counters
const filters = computed(() => {
  const list = challenges.value || [];
  const counts = {
    all: list.length,
    active: list.filter(isActive).length,
    completed: list.filter(isCompleted).length,
    expired: list.filter(isExpired).length,
  };
  return [
    { value: 'all', label: 'All', badge: counts.all },
    { value: 'active', label: 'Active', badge: counts.active },
    { value: 'completed', label: 'Completed', badge: counts.completed },
    { value: 'expired', label: 'Expired', badge: counts.expired },
  ];
});

function openChallenge(id) {
  selectedId.value = id;
  showModal.value = true;
}

function handleSubmitted() {
  showModal.value = false;
  loadChallenges();
}

onMounted(loadChallenges);
</script>