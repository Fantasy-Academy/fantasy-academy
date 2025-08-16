<template>
  <div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Challenges</h1>

    <p v-if="error" class="text-red-600 mb-4">{{ error }}</p>
    <p v-if="loading" class="text-gray-600">Načítám…</p>
    <div v-if="!loading && !error && challenges.length === 0" class="text-gray-600">
      Zatím tu nejsou žádné výzvy.
    </div>

    <div class="space-y-4" v-if="!loading && challenges.length">
      <ChallengeCard
        v-for="c in challenges"
        :key="c.id"
        :challenge="c"
        @select="openChallenge(c.id)"
      />
    </div>

    <ChallengeModal
      v-if="showModal"
      :show="showModal"
      :challenge-id="selectedId"
      @close="showModal = false"
      @submitted="handleSubmitted"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useChallenges } from '@/composables/useChallenges';
import ChallengeCard from '@/components/ChallengeCard.vue';
import ChallengeModal from '@/components/ChallengeModal.vue';

const { challenges, loading, error, loadChallenges } = useChallenges();

const showModal = ref(false);
const selectedId = ref(null);

function openChallenge(id) {
  selectedId.value = id;
  showModal.value = true;
}

function handleSubmitted() {
  showModal.value = false;
  loadChallenges();
}

onMounted(() => {
  loadChallenges();
});
</script>