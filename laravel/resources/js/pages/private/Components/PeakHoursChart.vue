<template>
    <div class="h-64 relative w-full">
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

const chartData = computed(() => {
    const counts = props.hourlyData.map((d) => Number(d.checkins || 0));
    const maxVal = Math.max(...counts, 0);

    return {
        labels: props.hourlyData.map((d) => d.hour),
        datasets: [
            {
                label: "Check-ins (30 Days)",
                data: counts,
                backgroundColor: counts.map((val) =>
                    maxVal > 0 && val === maxVal ? "#F8313B" : "#0F1115",
                ),
                hoverBackgroundColor: "#F8313B",
                borderRadius: 4,
                maxBarThickness: 22,
            },
        ],
    };
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        tooltip: {
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
                label: (context) =>
                    ` ${Number(context.raw || 0).toLocaleString("en-PH")} Check-ins`,
            },
        },
    },
    scales: {
        x: {
            grid: { display: false },
            border: { display: false },
            ticks: {
                color: "#71717A",
                font: { size: 11, weight: "500" },
            },
        },
        y: {
            beginAtZero: true,
            border: { display: false },
            ticks: {
                precision: 0,
                color: "#71717A",
                font: { size: 11, weight: "500" },
            },
            grid: { color: "#F4F4F5" },
        },
    },
}));
</script>
