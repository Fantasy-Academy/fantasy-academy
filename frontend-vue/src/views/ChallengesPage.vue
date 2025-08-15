<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Challenges</h1>

    <p v-if="error" class="text-red-600">{{ error }}</p>
    <p v-if="loading" class="text-gray-600">Loading...</p>

    <div class="space-y-4" v-if="!loading">
      <ChallengeCard
        v-for="c in challenges"
        :key="c.id"
        :challenge="c"
        @select="openChallenge"
      />
    </div>

    <ChallengeModal
      :show="modalOpen"
      :challenge="selectedChallenge"
      @close="modalOpen = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useChallenges } from '../composables/useChallenges';
import ChallengeCard from '../components/ChallengeCard.vue';
import ChallengeModal from '../components/ChallengeModal.vue';

const { challenges, loading, error, loadChallenges } = useChallenges();

const modalOpen = ref(false);
const selectedChallenge = ref(null);

function openChallenge(challenge) {
  selectedChallenge.value = challenge;
  modalOpen.value = true;
}

onMounted(loadChallenges);
</script>