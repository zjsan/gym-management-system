<template>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Dashboard Overview
                </h1>
                <p class="text-sm text-gray-500">
                    Real-time revenue, attendance, and member metrics.
                </p>
            </div>
            <button
                @click="dashboardStore.fetchOverview()"
                :disabled="dashboardStore.loading"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg text-sm transition-colors flex items-center space-x-2 disabled:opacity-50"
            >
                <span>{{
                    dashboardStore.loading ? "Refreshing..." : "Refresh Data"
                }}</span>
            </button>
        </div>

        <!-- Error Banner -->
        <div
            v-if="dashboardStore.error"
            class="p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm"
        >
            {{ dashboardStore.error }}
        </div>

        <!-- KPI Metric Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Today's Revenue -->
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm"
            >
                <p
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                >
                    Today's Revenue
                </p>
                <p class="text-2xl font-bold text-gray-900 mt-2">
                    ₱{{ formatCurrency(dashboardStore.metrics.todayRevenue) }}
                </p>
            </div>

            <!-- Monthly Revenue -->
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm"
            >
                <p
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                >
                    Monthly Revenue
                </p>
                <p class="text-2xl font-bold text-indigo-600 mt-2">
                    ₱{{ formatCurrency(dashboardStore.metrics.monthlyRevenue) }}
                </p>
            </div>

            <!-- Active Members -->
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm"
            >
                <p
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                >
                    Active Members
                </p>
                <p class="text-2xl font-bold text-emerald-600 mt-2">
                    {{ dashboardStore.metrics.activeMembers }}
                </p>
            </div>

            <!-- Expired Members -->
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm"
            >
                <p
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                >
                    Expired Members
                </p>
                <p class="text-2xl font-bold text-amber-600 mt-2">
                    {{ dashboardStore.metrics.expiredMembers }}
                </p>
            </div>

            <!-- Today's Attendance -->
            <div
                class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm"
            >
                <p
                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider"
                >
                    Today's Check-ins
                </p>
                <p class="text-2xl font-bold text-blue-600 mt-2">
                    {{ dashboardStore.metrics.todayAttendance }}
                </p>
            </div>
        </div>

        <!-- Charts & Activity Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div
                class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm"
            >
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    7-Day Revenue & Registration Trend
                </h2>

                <RevenueBarChart
                    :trend-data="dashboardStore.revenueChartData"
                />
            </div>

            <!-- Recent Activity List (1 Column) -->
            <div
                class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm"
            >
                <h2 class="text-lg font-bold text-gray-900 mb-4">
                    Recent Payments
                </h2>
                <div
                    v-if="dashboardStore.recentTransactions.length === 0"
                    class="text-sm text-gray-400 py-8 text-center"
                >
                    No transactions recorded recently.
                </div>
                <div v-else class="space-y-4">
                    <div
                        v-for="tx in dashboardStore.recentTransactions"
                        :key="tx.id"
                        class="flex items-center justify-between pb-3 border-b border-gray-50 last:border-0 last:pb-0"
                    >
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ tx.payer }}
                            </p>
                            <p
                                class="text-xs text-gray-400 uppercase tracking-wider"
                            >
                                {{ tx.type }} • {{ tx.timestamp }}
                            </p>
                        </div>
                        <span class="text-sm font-bold text-emerald-600"
                            >+₱{{ formatCurrency(tx.amount) }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useDashboardStore } from "@/stores/dashboardStore";
import RevenueBarChart from "@/pages/protected/Components/RevenueBarChart.vue";

const dashboardStore = useDashboardStore();

// Format numbers into Philippine Peso layout
const formatCurrency = (val) => {
    return Number(val || 0).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

// Calculate maximum revenue day for bar scaling
const maxRevenue = computed(() => {
    if (!dashboardStore.revenueChartData.length) return 1;
    const totals = dashboardStore.revenueChartData.map(
        (d) => (d.walkin || 0) + (d.renewals || 0),
    );
    return Math.max(...totals, 1);
});

// Relative height calculation for CSS bars
const getBarHeight = (value, max) => {
    if (!max || max === 0) return 0;
    return Math.min(Math.round((value / max) * 100), 100);
};

onMounted(() => {
    dashboardStore.fetchOverview();
});
</script>
