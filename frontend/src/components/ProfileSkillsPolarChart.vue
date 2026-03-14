<template>
  <div class="w-full h-full rounded-xl bg-dark-white p-4">
    <div class="mb-3">
      <h3 class="font-bold text-blue-black">Skill distribution</h3>
      <p class="text-sm text-cool-gray">Your current skill profile</p>
    </div>

    <div v-if="loading" class="text-sm text-cool-gray">
      Loading skills...
    </div>

    <div v-else-if="!skills.length" class="text-sm text-cool-gray">
      No skill data available.
    </div>

    <div v-else class="relative w-full h-[320px] sm:h-[360px]">
      <canvas ref="canvasEl"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, nextTick, computed } from "vue";
import {
  Chart,
  PolarAreaController,
  RadialLinearScale,
  ArcElement,
  Tooltip,
  Legend,
} from "chart.js";
import { getToken } from "@/services/tokenService";

Chart.register(
  PolarAreaController,
  RadialLinearScale,
  ArcElement,
  Tooltip,
  Legend
);

const BASE_URL =
  import.meta.env.VITE_BACKEND_URL ??
  import.meta.env.VITE_API_BASE_URL ??
  "";

const loading = ref(false);
const skills = ref([]);
const canvasEl = ref(null);
const width = ref(window.innerWidth);

let chart = null;

const isMobile = computed(() => width.value < 640);

function handleResize() {
  width.value = window.innerWidth;
  buildChart();
}

async function loadSkills() {
  loading.value = true;

  try {
    const token = getToken();

    const response = await fetch(`${BASE_URL}/api/me`, {
      headers: {
        "Content-Type": "application/json",
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    });

    if (!response.ok) {
      throw new Error("Failed to load user skills");
    }

    const data = await response.json();
    skills.value = data?.overallStatistics?.skills ?? [];
  } catch (error) {
    console.error("Failed to load profile skills:", error);
    skills.value = [];
  } finally {
    loading.value = false;
  }
}

function buildChart() {
  if (!canvasEl.value || !skills.value.length) return;

  if (chart) {
    chart.destroy();
    chart = null;
  }

  chart = new Chart(canvasEl.value, {
    type: "polarArea",
    data: {
      labels: skills.value.map((s) => s.name),
      datasets: [
        {
          data: skills.value.map((s) => s.percentage),
          backgroundColor: [
            "#6A01FE",
            "#8B3DFF",
            "#5C7CFF",
            "#3F9CFF",
            "#C65BFF",
            "#E07BFF",
            "#B14DFF",
          ],
          hoverBackgroundColor: [
            "#5A00E0",
            "#7A30E8",
            "#4E6CE8",
            "#2F8AE8",
            "#B84DE8",
            "#D86AE8",
            "#9D3AE8",
          ],
          borderColor: "#ffffff",
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: {
        animateRotate: true,
        animateScale: true,
      },
      plugins: {
        legend: {
          position: isMobile.value ? "bottom" : "right",
          labels: {
            boxWidth: 12,
            padding: isMobile.value ? 14 : 10,
            font: {
              size: isMobile.value ? 11 : 12,
            },
            color: "#1f2937",
          },
        },
        tooltip: {
          callbacks: {
            label(ctx) {
              return `${ctx.label}: ${ctx.raw}%`;
            },
          },
        },
      },
      scales: {
        r: {
          ticks: {
            display: false,
          },
          grid: {
            color: "rgba(106, 1, 254, 0.12)",
          },
          angleLines: {
            color: "rgba(106, 1, 254, 0.12)",
          },
        },
      },
    },
  });
}

onMounted(async () => {
  await loadSkills();
  await nextTick();
  buildChart();
  window.addEventListener("resize", handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", handleResize);
  if (chart) chart.destroy();
});
</script>