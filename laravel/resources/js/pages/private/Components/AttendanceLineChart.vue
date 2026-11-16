<template>
    <div class="h-64 relative">
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Line } from "vue-chartjs";
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
} from "chart.js";

// Register required modules for Line Chart + Gradient Fill
ChartJS.register(
    Title,
    Tooltip,
    Legend,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Filler,
);

const props = defineProps({
    trendData: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const chartData = computed(() => {
    const labels = props.trendData.map((d) => d.day);
    const counts = props.trendData.map((d) => d.count || 0);

    return {
        labels,
        datasets: [
            {
                label: "Daily Check-ins",
                data: counts,
                borderColor: "#3B82F6", // Blue-500
                backgroundColor: "rgba(59, 130, 246, 0.12)", // Subtle blue fill
                borderWidth: 3,
                tension: 0.35, // Smooth bezier curves
                fill: true,
                pointBackgroundColor: "#2563EB",
                pointHoverRadius: 6,
                pointRadius: 4,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false, // Hidden legend since there is only 1 metric
        },
        tooltip: {
            callbacks: {
                label: (context) => ` ${context.raw} Check-ins`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
        },
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0, // Force whole integer numbers for headcount
            },
            grid: {
                color: "#F3F4F6",
            },
        },
    },
};
</script>
