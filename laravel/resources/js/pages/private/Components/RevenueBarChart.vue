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
    trendData: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const chartData = computed(() => {
    const labels = props.trendData.map((d) => d.day);
    const renewalData = props.trendData.map((d) =>
        Number(d.membership_renewal || 0),
    );
    const walkinData = props.trendData.map((d) => Number(d.walkin_fee || 0));
    const registrationData = props.trendData.map((d) =>
        Number(d.membership_registration || 0),
    );

    return {
        labels,
        datasets: [
            {
                label: "Renewals",
                backgroundColor: "#F8313B",
                hoverBackgroundColor: "#e0242e",
                data: renewalData,
                borderRadius: 4,
                maxBarThickness: 18,
            },
            {
                label: "Walk-ins",
                backgroundColor: "#0F1115",
                hoverBackgroundColor: "#27272A",
                data: walkinData,
                borderRadius: 4,
                maxBarThickness: 18,
            },
            {
                label: "Registrations",
                backgroundColor: "#A1A1AA",
                hoverBackgroundColor: "#71717A",
                data: registrationData,
                borderRadius: 4,
                maxBarThickness: 18,
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
            position: "top",
            align: "start",
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
                    return ` ${context.dataset.label}: ₱${val.toLocaleString(
                        "en-PH",
                        {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                        },
                    )}`;
                },
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
                color: "#71717A",
                font: { size: 11, weight: "500" },
                callback: (value) =>
                    "₱" + Number(value || 0).toLocaleString("en-PH"),
            },
            grid: {
                color: "#F4F4F5",
            },
        },
    },
}));
</script>
