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
    trendData: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const chartData = computed(() => {
    const labels = props.trendData.map((d) => d.day);

    // Updated property names matching your exact JSON output
    const walkinData = props.trendData.map((d) => d.walkin_fee || 0);
    const registrationData = props.trendData.map(
        (d) => d.membership_registration || 0,
    );
    const renewalData = props.trendData.map((d) => d.membership_renewal || 0);

    return {
        labels,
        datasets: [
            {
                label: "Membership Renewals",
                backgroundColor: "#6366F1", // Indigo
                data: renewalData,
                borderRadius: 4,
            },
            {
                label: "Walk-in Passes",
                backgroundColor: "#34D399", // Emerald
                data: walkinData,
                borderRadius: 4,
            },
            {
                label: "New Registrations",
                backgroundColor: "#F59E0B", // Amber
                data: registrationData,
                borderRadius: 4,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "top",
            labels: {
                usePointStyle: true,
                boxWidth: 8,
                font: { size: 12 },
            },
        },
        tooltip: {
            callbacks: {
                label: (context) => {
                    const val = context.raw || 0;
                    return ` ${context.dataset.label}: ₱${val.toLocaleString(
                        "en-PH",
                        {
                            minimumFractionDigits: 2,
                        },
                    )}`;
                },
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
                callback: (value) => "₱" + value.toLocaleString(),
            },
            grid: {
                color: "#F3F4F6",
            },
        },
    },
};
</script>
