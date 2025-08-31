<template>
    <section class="mx-auto max-w-6xl px-4 py-8">
        <!-- Header -->
        <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="font-bebas-neue text-3xl tracking-wide text-blue-black">My Answers</h1>
        </header>

        <!-- States -->
        <p v-if="error"
            class="mb-4 rounded-xl border border-vibrant-coral/30 bg-vibrant-coral/10 p-3 text-vibrant-coral">
            {{ error }}
        </p>
        <p v-else-if="loading" class="text-cool-gray">Loading…</p>

        <!-- Empty -->
        <div v-else-if="filteredMyAnswers.length === 0"
            class="rounded-2xl border border-charcoal/10 bg-dark-white p-6 text-cool-gray">
            No answers yet.
        </div>

        <!-- List -->
        <ul v-else class="space-y-4">
            <li v-for="a in filteredMyAnswers" :key="a.id"
                class="rounded-2xl border border-charcoal/10 bg-white p-4 shadow-main">
                <!-- Top row: challenge + answeredAt + type -->
                <div class="mb-2 flex flex-wrap items-center gap-2 text-xs">
                    <span
                        class="inline-flex items-center rounded-full bg-dark-white px-2 py-0.5 font-semibold text-blue-black"
                        title="Challenge">
                        {{ a.challengeName || 'Challenge' }}
                    </span>
                    <span class="text-cool-gray">·</span>
                    <span class="text-cool-gray" title="Answered at">
                        {{ formatDate(a.answeredAt) }}
                    </span>
                    <span
                        class="ml-auto inline-flex items-center rounded-full bg-golden-yellow/20 px-2 py-0.5 font-semibold text-golden-yellow">
                        {{ a.type }}
                    </span>
                </div>

                <!-- Question -->
                <p class="font-alexandria text-blue-black font-semibold">
                    {{ a.questionText }}
                </p>

                <!-- Answer -->
                <div class="flex flex-row items-center gap-1">
                    <p class="mt-1 text-md text-cool-gray">
                        Your Answer:
                    </p>
                    <p class="mt-1 text-sm text-cool-gray">
                        {{ a.answerText }}
                    </p>
                </div>

            </li>
        </ul>
    </section>
</template>

<script setup>
import { onMounted, computed } from 'vue';
import { useMyAnswers } from '@/composables/useMyAnswers';

const { myAnswers, loading, error, loadMyAnswers } = useMyAnswers();
const filteredMyAnswers = computed(() => myAnswers.value);

onMounted(() => {
    loadMyAnswers({ page: 1, auth: true });
});

function formatDate(dt) {
    if (!dt) return '—';
    try {
        return new Intl.DateTimeFormat('en-GB', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        }).format(new Date(dt));
    } catch {
        return new Date(dt).toLocaleString();
    }
}
</script>