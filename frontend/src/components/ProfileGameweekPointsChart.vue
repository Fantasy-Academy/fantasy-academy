<template>
  <div class="w-full rounded-xl bg-dark-white p-4">
    <div class="mb-3 flex items-start justify-between gap-3">
      <div>
        <h3 class="font-bold text-blue-black">Gameweek points</h3>
        <p class="text-sm text-cool-gray">Points earned in each gameweek</p>
      </div>

      <div>
        <select
          v-model="selectedRange"
          class="rounded-lg border border-charcoal/10 bg-white px-3 py-2 text-sm text-blue-black shadow-sm outline-none transition focus:border-light-purple"
        >
          <option value="max">Max</option>
          <option value="year">Year</option>
          <option value="3months">3 months</option>
          <option value="month">Month</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="text-sm text-cool-gray">
      Loading points...
    </div>

    <div v-else-if="!filteredGameweeks.length" class="text-sm text-cool-gray">
      No points data available.
    </div>

    <div v-else class="relative w-full h-[340px]">
      <canvas ref="canvasEl"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed, watch } from "vue";
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
} from "chart.js";
import { getToken } from "@/services/tokenService";

Chart.register(
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
);

const props = defineProps({
  playerId: {
    type: String,
    default: null,
  },
});

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

async function resolvePlayerId(headers) {
  if (props.playerId) {
    return props.playerId;
  }

  const meResponse = await fetch(`${BASE_URL}/api/me`, {
    headers,
  });

  if (!meResponse.ok) {
    throw new Error("Failed to load user info");
  }

  const meData = await meResponse.json();
  const userId = meData?.id;

  if (!userId) {
    throw new Error("Missing user id");
  }

  return userId;
}

async function loadPoints() {
  loading.value = true;

  try {
    const token = getToken();

    const headers = {
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
    };

    const resolvedPlayerId = await resolvePlayerId(headers);

    const activityResponse = await fetch(`${BASE_URL}/api/player/${resolvedPlayerId}/activity`, {
      headers,
    });

    if (!activityResponse.ok) {
      throw new Error("Failed to load player activity");
    }

    const activityData = await activityResponse.json();
    gameweeks.value = activityData?.gameweeks ?? [];
  } catch (error) {
    console.error("Failed to load gameweek points:", error);
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

  const colors = [
    "#6A01FE",
    "#8B3DFF",
    "#5C7CFF",
    "#3F9CFF",
    "#C65BFF",
    "#E07BFF",
    "#B14DFF",
  ];

  chart = new Chart(canvasEl.value, {
    type: "bar",
    data: {
      labels: filteredGameweeks.value.map((gw) => `GW ${gw.gameweek}`),
      datasets: [
        {
          label: "Points",
          data: filteredGameweeks.value.map((gw) => gw.pointsEarned ?? 0),
          backgroundColor: filteredGameweeks.value.map((_, index) => colors[index % colors.length]),
          borderRadius: 10,
          borderSkipped: false,
          maxBarThickness: 42,
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
        tooltip: {
          callbacks: {
            label(ctx) {
              const gw = filteredGameweeks.value[ctx.dataIndex];
              return `Points: ${ctx.raw} / ${gw.maxPointsPossible ?? 0}`;
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
  await loadPoints();
  await nextTick();
  buildChart();
  window.addEventListener("resize", handleResize);
});

watch(
  () => props.playerId,
  async () => {
    await loadPoints();
    await nextTick();
    buildChart();
  }
);

watch(
  filteredGameweeks,
  async () => {
    await nextTick();
    buildChart();
  },
  { deep: true }
);

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
  if (chart) chart.destroy();
});
</script>