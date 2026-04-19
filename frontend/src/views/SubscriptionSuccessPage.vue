<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useSubscription } from '@/composables/useSubscription';
import { useProfile } from '@/composables/useProfile';

const router = useRouter();
const { pollUntilActive } = useSubscription();
const { load: loadProfile } = useProfile();

const confirmed = ref(false);
const timedOut = ref(false);
const checking = ref(true);

onMounted(async () => {
  // Poll up to 8 times (16 seconds) waiting for webhook to fire
  // This is necessary because Stripe's webhook is asynchronous
  const activated = await pollUntilActive({ maxAttempts: 8, intervalMs: 2000 });

  if (activated) {
    confirmed.value = true;
    // Reload profile so isMember is updated everywhere
    await loadProfile();
  } else {
    // Webhook took too long — subscription is probably fine but just delayed
    // Tell user it may take a moment and let them go to dashboard
    timedOut.value = true;
  }

  checking.value = false;
});
</script>

<template>
    <div class="max-w-lg mx-auto px-4 py-20 text-center">

      <!-- Checking / loading state -->
      <template v-if="checking">
        <div class="animate-spin text-5xl mb-6">⚙️</div>
        <h1 class="font-bebas-neue text-4xl text-blue-black">Activating your membership…</h1>
        <p class="mt-3 text-cool-gray font-source-sans-3">
          Just a moment while we confirm your payment with Stripe.
        </p>
      </template>

      <!-- Confirmed active -->
      <template v-else-if="confirmed">
        <div class="text-6xl mb-6">🎉</div>
        <h1 class="font-bebas-neue text-5xl text-blue-black">Welcome to Premium!</h1>
        <p class="mt-4 text-cool-gray font-source-sans-3 text-lg">
          Your membership is now active. Time to dominate the leaderboard.
        </p>
        <router-link
          to="/dashboard"
          class="mt-8 inline-block bg-dark-purple text-white font-nunito font-bold px-8 py-3 rounded-xl shadow-main hover:opacity-90 transition"
        >
          Go to Dashboard
        </router-link>
      </template>

      <!-- Timed out — probably fine, just slow webhook -->
      <template v-else-if="timedOut">
        <div class="text-6xl mb-6">⏳</div>
        <h1 class="font-bebas-neue text-4xl text-blue-black">Almost there…</h1>
        <p class="mt-4 text-cool-gray font-source-sans-3">
          Your payment was received! It may take a minute for your membership to activate.
          Try refreshing the page in a moment.
        </p>
        <div class="mt-8 flex gap-4 justify-center flex-wrap">
          <button
            @click="() => window.location.reload()"
            class="bg-dark-purple text-white font-nunito font-bold px-6 py-3 rounded-xl"
          >
            Refresh
          </button>
          <router-link
            to="/dashboard"
            class="border-2 border-charcoal text-charcoal font-nunito font-bold px-6 py-3 rounded-xl"
          >
            Go to Dashboard
          </router-link>
        </div>
      </template>

    </div>
</template>