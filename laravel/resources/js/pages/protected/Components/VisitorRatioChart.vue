<template>
    <div class="h-64 relative">
        <Doughnut :data="chartData" :options="chartOptions" />
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

const chartData = computed(() => ({
    labels: ["Active Members", "Walk-in Passes"],
    datasets: [
        {
            data: [props.ratioData.members, props.ratioData.walkins],
            backgroundColor: ["#6366F1", "#10B981"], // Indigo vs Emerald
            hoverOffset: 6,
            borderWidth: 2,
        },
    ],
}));

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: "bottom",
            labels: { usePointStyle: true, boxWidth: 8 },
        },
    },
    cutout: "70%",
};
</script>
