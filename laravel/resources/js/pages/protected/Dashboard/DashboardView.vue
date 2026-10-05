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
            <!-- 7-Day Revenue Trend (2 Columns) -->
            <div
                class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between"
            >
                <div>
                    <h2 class="text-lg font-bold text-gray-900">
                        7-Day Revenue Trend
                    </h2>
                    <div
                        class="flex items-center space-x-4 mt-2 text-xs text-gray-500"
                    >
                        <span class="flex items-center space-x-1">
                            <span
                                class="w-3 h-3 bg-indigo-500 rounded-full inline-block"
                            ></span>
                            <span>Renewals</span>
                        </span>
                        <span class="flex items-center space-x-1">
                            <span
                                class="w-3 h-3 bg-emerald-400 rounded-full inline-block"
                            ></span>
                            <span>Walk-ins</span>
                        </span>
                    </div>
                </div>

                <!-- Simple SVG Bar Visualizer -->
                <div
                    class="mt-6 h-48 flex items-end justify-between space-x-2 pt-4 border-b border-gray-100"
                >
                    <div
                        v-for="item in dashboardStore.revenueChartData"
                        :key="item.date"
                        class="flex-1 flex flex-col items-center h-full justify-end"
                    >
                        <div
                            class="w-full max-w-[32px] flex flex-col justify-end h-full"
                        >
                            <!-- Renewal Bar segment -->
                            <div
                                class="bg-indigo-500 rounded-t-sm transition-all duration-300"
                                :style="{
                                    height:
                                        getBarHeight(
                                            item.renewals,
                                            maxRevenue,
                                        ) + '%',
                                }"
                                :title="`Renewals: ₱${item.renewals}`"
                            ></div>
                            <!-- Walk-in Bar segment -->
                            <div
                                class="bg-emerald-400 rounded-b-sm transition-all duration-300"
                                :style="{
                                    height:
                                        getBarHeight(item.walkin, maxRevenue) +
                                        '%',
                                }"
                                :title="`Walk-ins: ₱${item.walkin}`"
                            ></div>
                        </div>
                        <span class="text-xs text-gray-400 mt-2">{{
                            item.day
                        }}</span>
                    </div>
                </div>
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
