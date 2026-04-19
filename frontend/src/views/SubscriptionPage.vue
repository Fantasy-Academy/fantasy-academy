<!-- src/views/SubscriptionPage.vue -->
<script setup>
import { onMounted, computed } from 'vue';
import { useProfile } from '@/composables/useProfile';
import { useSubscription } from '@/composables/useSubscription';
import PricingCard from '@/components/subscription/PricingCard.vue';
import SubscriptionDetails from '@/components/subscription/SubscriptionDetails.vue';

const { me, load: loadProfile } = useProfile();
const {
  status,
  loading,
  error,
  isActive,
  loadStatus,
  redirectToCheckout,
  redirectToPortal,
} = useSubscription();

// isMember comes from /api/me (fast, already loaded if user is logged in)
// isActive comes from /api/subscription/status (detailed, loaded here)
const isMember = computed(() => me.value?.isMember ?? false);

onMounted(async () => {
  // Load both: profile (for isMember) and detailed status (for plan info)
  await loadProfile();
  if (isMember.value) {
    // Only load detailed status if they're already a member
    await loadStatus();
  }
});

async function handleSubscribe(plan) {
  await redirectToCheckout(plan);
}

async function handleManage() {
  await redirectToPortal();
}
</script>

<template>
    <div class="max-w-4xl mx-auto px-4 py-12">

      <!-- Error banner -->
      <div v-if="error" class="mb-6 p-4 rounded-xl bg-vibrant-coral/10 border border-vibrant-coral text-vibrant-coral text-sm">
        {{ error }}
      </div>

      <!-- Non-member: show pricing -->
      <template v-if="!isMember">
        <div class="text-center mb-10">
          <h1 class="font-bebas-neue text-5xl text-blue-black tracking-wide">
            Unlock Your Full Potential
          </h1>
          <p class="mt-3 text-cool-gray font-source-sans-3 text-lg">
            Join Fantasy Academy Premium and compete at the highest level.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <PricingCard
            plan="monthly"
            title="Monthly"
            price="9.99"
            period="month"
            :loading="loading"
            @select="handleSubscribe('monthly')"
          />
          <PricingCard
            plan="yearly"
            title="Yearly"
            price="99.90"
            period="year"
            badge="Save 17%"
            :loading="loading"
            @select="handleSubscribe('yearly')"
          />
        </div>

        <!-- Feature list -->
        <ul class="mt-10 space-y-3 text-blue-black font-source-sans-3">
          <li v-for="feature in features" :key="feature" class="flex items-center gap-3">
            <span class="text-pistachio text-xl">✓</span>
            {{ feature }}
          </li>
        </ul>
      </template>

      <!-- Member: show current plan + manage button -->
      <template v-else>
        <div class="text-center mb-8">
          <h1 class="font-bebas-neue text-5xl text-blue-black tracking-wide">
            Your Subscription
          </h1>
        </div>

        <SubscriptionDetails
          :status="status"
          :loading="loading"
          @manage="handleManage"
        />
      </template>

    </div>
</template>

<script>
// Define features list outside setup for clarity
const features = [
  'Access all challenges every gameweek',
  'Detailed skill analytics & polar charts',
  'Compare answers with other players',
  'Full leaderboard access',
  'Priority support',
];
</script>