<template>
    <div class="h-64 relative">
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Bar } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
} from "chart.js";

ChartJS.register(
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale,
);

const props = defineProps({
    hourlyData: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const chartData = computed(() => ({
    labels: props.hourlyData.map((d) => d.hour),
    datasets: [
        {
            label: "Total Check-ins (30 Days)",
            data: props.hourlyData.map((d) => d.checkins),
            backgroundColor: "#3B82F6", // Blue
            hoverBackgroundColor: "#1D4ED8",
            borderRadius: 4,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
            callbacks: {
                label: (context) => ` ${context.raw} Check-ins`,
            },
        },
    },
    scales: {
        x: { grid: { display: false } },
        y: {
            beginAtZero: true,
            ticks: { precision: 0 },
            grid: { color: "#F3F4F6" },
        },
    },
};
</script>
