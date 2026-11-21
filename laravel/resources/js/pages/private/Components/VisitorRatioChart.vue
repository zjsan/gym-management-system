<template>
    <div class="h-64 relative w-full flex items-center justify-center">
        <Doughnut :data="chartData" :options="chartOptions" />
        <div
            class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center pb-6"
        >
            <span
                class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
            >
                Total Visits
            </span>
            <span
                class="text-lg font-extrabold text-[#0F1115] tabular-nums leading-tight mt-0.5"
            >
                {{ totalVisits.toLocaleString("en-PH") }}
            </span>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Doughnut } from "vue-chartjs";
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from "chart.js";

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
    ratioData: {
        type: Object,
        required: true,
        default: () => ({ members: 0, walkins: 0 }),
    },
});

const membersCount = computed(() => Number(props.ratioData?.members || 0));
const walkinsCount = computed(() => Number(props.ratioData?.walkins || 0));
const totalVisits = computed(() => membersCount.value + walkinsCount.value);

const chartData = computed(() => {
    const hasData = totalVisits.value > 0;

    return {
        labels: ["Active Members", "Walk-in Passes"],
        datasets: [
            {
                data: hasData
                    ? [membersCount.value, walkinsCount.value]
                    : [1, 1],
                backgroundColor: hasData
                    ? ["#0F1115", "#F8313B"]
                    : ["#E4E4E7", "#F4F4F5"],
                hoverBackgroundColor: hasData
                    ? ["#27272A", "#e0242e"]
                    : ["#E4E4E7", "#F4F4F5"],
                borderColor: "#FFFFFF",
                borderWidth: 2,
                hoverOffset: hasData ? 4 : 0,
            },
        ],
    };
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    cutout: "72%",
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                usePointStyle: true,
                pointStyle: "circle",
                boxWidth: 7,
                boxHeight: 7,
                padding: 14,
                color: "#52525B",
                font: { size: 11, weight: "600" },
            },
        },
        tooltip: {
            enabled: totalVisits.value > 0,
            backgroundColor: "#FFFFFF",
            titleColor: "#0F1115",
            bodyColor: "#52525B",
            borderColor: "#E4E4E7",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            titleFont: { size: 11, weight: "700" },
            bodyFont: { size: 12, weight: "600" },
            callbacks: {
                label: (context) => {
                    const val = Number(context.raw || 0);
                    const pct =
                        totalVisits.value > 0
                            ? Math.round((val / totalVisits.value) * 100)
                            : 0;
                    return ` ${context.label}: ${val.toLocaleString("en-PH")} (${pct}%)`;
                },
            },
        },
    },
}));
</script>
