<template>
    <div class="w-full">
        <h3 class="font-bold mb-2 hidden md:block">
            Skills:
        </h3>

        <div class="relative w-full h-[260px]">
            <canvas ref="canvasEl"></canvas>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, computed, nextTick } from "vue";
import {
    Chart,
    PolarAreaController,
    RadialLinearScale,
    ArcElement,
    Tooltip,
    Legend,
} from "chart.js";

Chart.register(
    PolarAreaController,
    RadialLinearScale,
    ArcElement,
    Tooltip,
    Legend
);

const props = defineProps({
    skills: {
        type: Array,
        required: true,
        // [{ name: string, percentage: number }]
    },
});

const canvasEl = ref(null);
let chart = null;

// responsive helper (volitelné)
const width = ref(window.innerWidth);
const isMobile = computed(() => width.value < 768);

function handleResize() {
    width.value = window.innerWidth;
    if (chart) chart.resize();
}

function buildChart() {
    if (!canvasEl.value || !props.skills?.length) return;

    if (chart) {
        chart.destroy();
        chart = null;
    }

    chart = new Chart(canvasEl.value, {
        type: "polarArea",
        data: {
            labels: props.skills.map(s => s.name),
            datasets: [
                {
                    data: props.skills.map(s => s.percentage),
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
            plugins: {
                legend: {
                    position: "right",
                    labels: {
                        boxWidth: 12,
                        font: { size: 12 },
                    },
                },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            return `${ctx.label}: ${(ctx.raw * 100).toFixed(0)} %`;
                        },
                    },
                },
            },
            scales: {
                r: {
                    ticks: { display: false },
                },
            },
        },
    });
}

onMounted(async () => {
    await nextTick();
    buildChart();
    window.addEventListener("resize", handleResize);
});

watch(
    () => props.skills,
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