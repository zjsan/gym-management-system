<template>
    <div class="h-64 relative w-full">
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
    const counts = props.trendData.map((d) => Number(d.count || 0));

    return {
        labels,
        datasets: [
            {
                label: "Daily Check-ins",
                data: counts,
                borderColor: "#F8313B",
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return "rgba(248, 49, 59, 0.08)";
                    const gradient = ctx.createLinearGradient(
                        0,
                        chartArea.top,
                        0,
                        chartArea.bottom,
                    );
                    gradient.addColorStop(0, "rgba(248, 49, 59, 0.16)");
                    gradient.addColorStop(1, "rgba(248, 49, 59, 0.0)");
                    return gradient;
                },
                borderWidth: 2.25,
                tension: 0.35,
                fill: true,
                pointBackgroundColor: "#FFFFFF",
                pointBorderColor: "#F8313B",
                pointBorderWidth: 2,
                pointRadius: 3.5,
                pointHoverRadius: 5.5,
                pointHoverBackgroundColor: "#F8313B",
                pointHoverBorderColor: "#FFFFFF",
                pointHoverBorderWidth: 2,
            },
        ],
    };
});

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: "index",
        intersect: false,
    },
    plugins: {
        legend: {
            display: false,
        },
        tooltip: {
            backgroundColor: "#FFFFFF",
            titleColor: "#0F1115",
            bodyColor: "#52525B",
            borderColor: "#E4E4E7",
            borderWidth: 1,
            padding: 10,
            cornerRadius: 8,
            displayColors: true,
            boxWidth: 8,
            boxHeight: 8,
            usePointStyle: true,
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
            border: { display: false, dash: [3, 3] },
            ticks: {
                precision: 0,
                color: "#71717A",
                font: { size: 11, weight: "500" },
            },
            grid: {
                color: "#F4F4F5",
            },
        },
    },
}));
</script>
