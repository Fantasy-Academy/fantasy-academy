<template>
    <div class="w-full h-full rounded-xl bg-dark-white p-4">
        <div class="mb-3 flex items-start justify-between gap-3">
            <div>
                <h3 class="font-bold text-blue-black">Gameweek activity</h3>
                <p class="text-sm text-cool-gray">Points earned in recent gameweeks</p>
            </div>

            <div>
                <select v-model="selectedRange"
                    class="rounded-lg border border-charcoal/10 bg-white px-3 py-2 text-sm text-blue-black shadow-sm outline-none transition focus:border-light-purple">
                    <option value="max">Max</option>
                    <option value="year">Year</option>
                    <option value="3months">3 months</option>
                    <option value="month">Month</option>
                </select>
            </div>
        </div>

        <div v-if="loading" class="text-sm text-cool-gray">
            Loading activity...
        </div>

        <div v-else-if="!filteredGameweeks.length" class="text-sm text-cool-gray">
            No activity data available.
        </div>

        <div v-else class="relative w-full h-[320px]">
            <canvas ref="canvasEl"></canvas>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed, watch } from "vue";
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend,
} from "chart.js";
import { getToken } from "@/services/tokenService";

Chart.register(
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Tooltip,
    Legend
);

const BASE_URL =
    import.meta.env.VITE_BACKEND_URL ??
    import.meta.env.VITE_API_BASE_URL ??
    "";

const loading = ref(false);
const gameweeks = ref([]);
const canvasEl = ref(null);
const selectedRange = ref("3months");

let chart = null;

function handleResize() {
    if (chart) chart.resize();
}

const sortedGameweeks = computed(() => {
    return [...gameweeks.value].sort((a, b) => a.gameweek - b.gameweek);
});

const filteredGameweeks = computed(() => {
    const all = sortedGameweeks.value;

    if (selectedRange.value === "max") {
        return all;
    }

    const ranges = {
        month: 4,
        "3months": 12,
        year: 52,
    };

    const limit = ranges[selectedRange.value] ?? 12;
    return all.slice(-limit);
});

async function loadActivity() {
    loading.value = true;

    try {
        const token = getToken();

        const meResponse = await fetch(`${BASE_URL}/api/me`, {
            headers: {
                "Content-Type": "application/json",
                ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
        });

        if (!meResponse.ok) {
            throw new Error("Failed to load user info");
        }

        const meData = await meResponse.json();
        const userId = meData?.id;

        if (!userId) {
            throw new Error("Missing user id");
        }

        const activityResponse = await fetch(`${BASE_URL}/api/player/${userId}/activity`, {
            headers: {
                "Content-Type": "application/json",
                ...(token ? { Authorization: `Bearer ${token}` } : {}),
            },
        });

        if (!activityResponse.ok) {
            throw new Error("Failed to load player activity");
        }

        const activityData = await activityResponse.json();
        gameweeks.value = activityData?.gameweeks ?? [];
    } catch (error) {
        console.error("Failed to load profile activity:", error);
        gameweeks.value = [];
    } finally {
        loading.value = false;
    }
}

function buildChart() {
    if (!canvasEl.value || !filteredGameweeks.value.length) return;

    if (chart) {
        chart.destroy();
        chart = null;
    }

    chart = new Chart(canvasEl.value, {
        type: "line",
        data: {
            labels: filteredGameweeks.value.map((gw) => `GW ${gw.gameweek}`),
            datasets: [
                {
                    label: "Points",
                    data: filteredGameweeks.value.map((gw) => gw.pointsEarned),
                    borderColor: "#6A01FE",
                    backgroundColor: "rgba(106, 1, 254, 0.18)",
                    pointBackgroundColor: "#D08BFF",
                    pointBorderColor: "#6A01FE",
                    pointBorderWidth: 2,
                    pointStyle: "rectRounded",
                    pointRadius: 7,
                    pointHoverRadius: 10,
                    tension: 0.35,
                    fill: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: true,
                    text: "Points per gameweek",
                    color: "#1f2937",
                    font: {
                        size: 14,
                        weight: "600",
                    },
                },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            return `Points: ${ctx.raw}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        color: "#374151",
                    },
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: "rgba(106, 1, 254, 0.08)",
                    },
                    ticks: {
                        color: "#374151",
                    },
                },
            },
        },
    });
}

onMounted(async () => {
    await loadActivity();
    await nextTick();
    buildChart();
    window.addEventListener("resize", handleResize);
});

watch(filteredGameweeks, async () => {
    await nextTick();
    buildChart();
}, { deep: true });

onBeforeUnmount(() => {
    window.removeEventListener("resize", handleResize);
    if (chart) chart.destroy();
});
</script>