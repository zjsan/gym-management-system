<template>
    <div class="space-y-6">
        <!-- Refined Operational Context Bar (No Redundant Page Title) -->
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
        >
            <div class="flex items-center gap-2.5">
                <div
                    class="w-2 h-2 rounded-full bg-[#F8313B] shrink-0"
                    aria-hidden="true"
                />
                <p class="text-xs sm:text-[13px] font-medium text-zinc-600">
                    <span class="font-semibold text-[#0F1115]">{{
                        todayDateLabel
                    }}</span>
                    <span class="mx-1.5 text-zinc-300">&middot;</span>
                    <span>Live gym &amp; sales overview</span>
                </p>
            </div>

            <!-- Unified Split Sync & Refresh Control (Linear / Vercel Pattern) -->
            <div
                class="inline-flex items-center self-start sm:self-auto h-8 rounded-lg bg-white border border-zinc-200/80 divide-x divide-zinc-200/80 shadow-2xs overflow-hidden"
            >
                <div
                    class="flex items-center gap-2 px-3 h-full text-[11px] font-medium text-zinc-600"
                    title="Automatically refreshes every 10 seconds while active"
                >
                    <span class="relative flex h-1.5 w-1.5">
                        <span
                            class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"
                        />
                    </span>
                    <span class="tabular-nums">{{ lastSyncedLabel }}</span>
                </div>

                <button
                    type="button"
                    @click="refreshDashboard"
                    :disabled="isRefreshing"
                    aria-label="Refresh dashboard metrics"
                    class="inline-flex items-center gap-1.5 px-3 h-full text-[11px] font-semibold text-[#0F1115] hover:bg-zinc-50 active:bg-zinc-100/80 transition-colors disabled:opacity-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-[#F8313B] cursor-pointer"
                >
                    <RefreshCw
                        :class="[
                            'w-3 h-3 text-zinc-500 transition-transform',
                            isRefreshing ? 'animate-spin text-[#F8313B]' : '',
                        ]"
                    />
                    <span>{{ isRefreshing ? "Syncing" : "Sync now" }}</span>
                </button>
            </div>
        </div>

        <!-- Error Banner -->
        <div
            v-if="dashboardStore.error"
            role="alert"
            class="flex items-start gap-2.5 p-3.5 bg-[#F8313B]/[0.06] border border-[#F8313B]/20 rounded-xl text-[#F8313B] text-xs font-medium"
        >
            <AlertCircle class="w-4 h-4 shrink-0 mt-0.5" />
            <span>{{ dashboardStore.error }}</span>
        </div>

        <!-- Tier 1: Unified Financial Metric Strip (Stripe Sigma Pattern) -->
        <Card
            class="bg-white border-zinc-200/80 shadow-2xs rounded-xl overflow-hidden"
        >
            <div
                class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-zinc-200/80"
            >
                <!-- Today's Revenue -->
                <div
                    class="group relative p-5 sm:p-6 hover:bg-zinc-50/80 transition-colors"
                >
                    <!-- Subtle Top Brand Indicator on Hover -->
                    <span
                        class="absolute inset-x-0 top-0 h-[2px] bg-[#F8313B] opacity-0 group-hover:opacity-100 transition-opacity"
                        aria-hidden="true"
                    />

                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-semibold text-zinc-500 tracking-tight"
                            >
                                Today's Revenue
                            </span>
                            <Badge
                                class="h-4 px-1.5 text-[10px] font-bold bg-[#F8313B]/10 text-[#F8313B] border-0 hover:bg-[#F8313B]/10 rounded"
                            >
                                TODAY
                            </Badge>
                        </div>

                        <router-link
                            :to="{ name: 'payment' }"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-zinc-500 hover:text-[#F8313B] transition-colors"
                        >
                            <span>View ledger</span>
                            <ArrowUpRight class="w-3.5 h-3.5" />
                        </router-link>
                    </div>

                    <div v-if="isInitialLoading" class="mt-3 space-y-2">
                        <Skeleton class="h-9 w-44 bg-zinc-100" />
                        <Skeleton class="h-3.5 w-36 bg-zinc-100" />
                    </div>
                    <template v-else>
                        <div class="mt-2.5 flex items-baseline gap-1">
                            <span
                                class="text-3xl sm:text-[34px] font-extrabold text-[#0F1115] tabular-nums tracking-tight leading-none"
                            >
                                ₱{{
                                    splitCurrency(
                                        dashboardStore.metrics.todayRevenue,
                                    ).whole
                                }}
                            </span>
                            <span
                                class="text-lg sm:text-xl font-bold text-zinc-400 tabular-nums tracking-tight leading-none"
                            >
                                .{{
                                    splitCurrency(
                                        dashboardStore.metrics.todayRevenue,
                                    ).fraction
                                }}
                            </span>
                        </div>
                        <p class="text-[11px] font-medium text-zinc-500 mt-2.5">
                            Gross collections across walk-in passes &amp;
                            renewals
                        </p>
                    </template>
                </div>

                <!-- Monthly Revenue -->
                <div
                    class="group relative p-5 sm:p-6 hover:bg-zinc-50/80 transition-colors"
                >
                    <span
                        class="absolute inset-x-0 top-0 h-[2px] bg-[#0F1115] opacity-0 group-hover:opacity-100 transition-opacity"
                        aria-hidden="true"
                    />

                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-semibold text-zinc-500 tracking-tight"
                            >
                                Monthly Revenue
                            </span>
                            <Badge
                                variant="secondary"
                                class="h-4 px-1.5 text-[10px] font-semibold bg-zinc-100 text-zinc-600 hover:bg-zinc-100 rounded"
                            >
                                {{ currentMonthShort }}
                            </Badge>
                        </div>

                        <TrendingUp class="w-4 h-4 text-zinc-400 shrink-0" />
                    </div>

                    <div v-if="isInitialLoading" class="mt-3 space-y-2">
                        <Skeleton class="h-9 w-48 bg-zinc-100" />
                        <Skeleton class="h-3.5 w-40 bg-zinc-100" />
                    </div>
                    <template v-else>
                        <div class="mt-2.5 flex items-baseline gap-1">
                            <span
                                class="text-3xl sm:text-[34px] font-extrabold text-[#0F1115] tabular-nums tracking-tight leading-none"
                            >
                                ₱{{
                                    splitCurrency(
                                        dashboardStore.metrics.monthlyRevenue,
                                    ).whole
                                }}
                            </span>
                            <span
                                class="text-lg sm:text-xl font-bold text-zinc-400 tabular-nums tracking-tight leading-none"
                            >
                                .{{
                                    splitCurrency(
                                        dashboardStore.metrics.monthlyRevenue,
                                    ).fraction
                                }}
                            </span>
                        </div>
                        <p class="text-[11px] font-medium text-zinc-500 mt-2.5">
                            Cumulative month-to-date earnings ({{
                                currentMonthLabel
                            }})
                        </p>
                    </template>
                </div>
            </div>
        </Card>

        <!-- Tier 2: Floor & Membership Telemetry Strip (3 Columns) -->
        <Card
            class="bg-white border-zinc-200/80 shadow-2xs rounded-xl overflow-hidden"
        >
            <div
                class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-zinc-200/80"
            >
                <!-- Active Members -->
                <div class="p-5 hover:bg-zinc-50/80 transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="text-xs font-semibold text-zinc-500 tracking-tight"
                        >
                            Active Members
                        </span>
                        <Users class="w-3.5 h-3.5 text-zinc-400 shrink-0" />
                    </div>

                    <div v-if="isInitialLoading" class="mt-2.5 space-y-2">
                        <Skeleton class="h-7 w-20 bg-zinc-100" />
                        <Skeleton class="h-3.5 w-28 bg-zinc-100" />
                    </div>
                    <template v-else>
                        <div class="flex items-center gap-2.5 mt-2">
                            <p
                                class="text-2xl font-extrabold text-[#0F1115] tabular-nums tracking-tight leading-none"
                            >
                                {{ dashboardStore.metrics.activeMembers ?? 0 }}
                            </p>
                            <span
                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-50 text-emerald-700 ring-1 ring-inset ring-emerald-600/15 tabular-nums"
                            >
                                {{ activeMemberPercentage }}% retention
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-500 mt-2">
                            Valid passes currently in good standing
                        </p>
                    </template>
                </div>

                <!-- Expired Members -->
                <div class="p-5 hover:bg-zinc-50/80 transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="text-xs font-semibold text-zinc-500 tracking-tight"
                        >
                            Expired Members
                        </span>
                        <UserX class="w-3.5 h-3.5 text-zinc-400 shrink-0" />
                    </div>

                    <div v-if="isInitialLoading" class="mt-2.5 space-y-2">
                        <Skeleton class="h-7 w-20 bg-zinc-100" />
                        <Skeleton class="h-3.5 w-28 bg-zinc-100" />
                    </div>
                    <template v-else>
                        <div class="flex items-center gap-2.5 mt-2">
                            <p
                                class="text-2xl font-extrabold text-[#0F1115] tabular-nums tracking-tight leading-none"
                            >
                                {{ dashboardStore.metrics.expiredMembers ?? 0 }}
                            </p>
                            <span
                                v-if="
                                    (dashboardStore.metrics.expiredMembers ??
                                        0) > 0
                                "
                                class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-semibold bg-amber-50 text-amber-700 ring-1 ring-inset ring-amber-600/20"
                            >
                                Follow-up due
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[10px] font-medium bg-zinc-100 text-zinc-600"
                            >
                                0 overdue
                            </span>
                        </div>
                        <p class="text-[11px] text-zinc-500 mt-2">
                            Memberships awaiting renewal
                        </p>
                    </template>
                </div>

                <!-- Today's Check-ins -->
                <div class="p-5 hover:bg-zinc-50/80 transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="text-xs font-semibold text-zinc-500 tracking-tight"
                        >
                            Today's Check-ins
                        </span>
                        <QrCode class="w-3.5 h-3.5 text-[#F8313B] shrink-0" />
                    </div>

                    <div v-if="isInitialLoading" class="mt-2.5 space-y-2">
                        <Skeleton class="h-7 w-20 bg-zinc-100" />
                        <Skeleton class="h-3.5 w-28 bg-zinc-100" />
                    </div>
                    <template v-else>
                        <div
                            class="flex items-center justify-between gap-2 mt-2"
                        >
                            <p
                                class="text-2xl font-extrabold text-[#0F1115] tabular-nums tracking-tight leading-none"
                            >
                                {{
                                    dashboardStore.metrics.todayAttendance ?? 0
                                }}
                            </p>
                            <router-link
                                v-if="authStore.isStaff"
                                :to="{ name: 'attendance' }"
                                class="inline-flex items-center gap-0.5 text-[11px] font-semibold text-[#F8313B] hover:text-[#e0242e] transition-colors"
                            >
                                <span>Scanner</span>
                                <ArrowUpRight class="w-3 h-3" />
                            </router-link>
                        </div>
                        <p class="text-[11px] text-zinc-500 mt-2">
                            Verified QR scans &amp; walk-in entries
                        </p>
                    </template>
                </div>
            </div>
        </Card>

        <!-- Analytics & Activity Split Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <!-- Left 8 Columns: Data Storytelling Flow (Trends -> Capacity/Mix -> Retention Action) -->
            <div class="xl:col-span-8 space-y-6">
                <!-- Beat 1: 7-Day Momentum (Financial & Sign-up Trend -> Foot Traffic Trend) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- 1. 7-Day Revenue & Registrations Bar Chart -->
                    <Card
                        class="bg-white border-zinc-200/80 shadow-2xs rounded-xl"
                    >
                        <CardHeader
                            class="p-5 pb-3 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
                        >
                            <div>
                                <CardTitle
                                    class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                                >
                                    Revenue &amp; Registrations
                                </CardTitle>
                                <CardDescription
                                    class="text-[11px] text-zinc-500 mt-0.5"
                                >
                                    Daily collections &amp; new sign-ups
                                </CardDescription>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-zinc-100/80 rounded border border-zinc-200/70"
                            >
                                7D
                            </span>
                        </CardHeader>
                        <CardContent class="p-5 pt-4">
                            <Skeleton
                                v-if="isInitialLoading"
                                class="h-52 w-full rounded-lg bg-zinc-100"
                            />
                            <RevenueBarChart
                                v-else
                                :trend-data="dashboardStore.revenueChartData"
                            />
                        </CardContent>
                    </Card>

                    <!-- 2. 7-Day Attendance Volume Line Chart -->
                    <Card
                        class="bg-white border-zinc-200/80 shadow-2xs rounded-xl"
                    >
                        <CardHeader
                            class="p-5 pb-3 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
                        >
                            <div>
                                <CardTitle
                                    class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                                >
                                    Attendance Volume
                                </CardTitle>
                                <CardDescription
                                    class="text-[11px] text-zinc-500 mt-0.5"
                                >
                                    Daily check-ins over past 7 days
                                </CardDescription>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-zinc-100/80 rounded border border-zinc-200/70"
                            >
                                7D
                            </span>
                        </CardHeader>
                        <CardContent class="p-5 pt-4">
                            <Skeleton
                                v-if="isInitialLoading"
                                class="h-52 w-full rounded-lg bg-zinc-100"
                            />
                            <AttendanceLineChart
                                v-else
                                :trend-data="dashboardStore.attendanceTrendData"
                            />
                        </CardContent>
                    </Card>
                </div>

                <!-- Beat 2: Floor Capacity & Audience Mix (When it peaks -> Who visits) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- 3. Peak Hours Check-in Distribution -->
                    <Card
                        class="bg-white border-zinc-200/80 shadow-2xs rounded-xl"
                    >
                        <CardHeader
                            class="p-5 pb-3 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
                        >
                            <div>
                                <CardTitle
                                    class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                                >
                                    Peak Floor Hours
                                </CardTitle>
                                <CardDescription
                                    class="text-[11px] text-zinc-500 mt-0.5"
                                >
                                    Hourly check-in density
                                </CardDescription>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-zinc-100/80 rounded border border-zinc-200/70"
                            >
                                30D
                            </span>
                        </CardHeader>
                        <CardContent class="p-5 pt-4">
                            <Skeleton
                                v-if="isInitialLoading"
                                class="h-52 w-full rounded-lg bg-zinc-100"
                            />
                            <PeakHoursChart
                                v-else
                                :hourly-data="
                                    dashboardStore.hourlyAttendanceData
                                "
                            />
                        </CardContent>
                    </Card>

                    <!-- 4. Member vs Walk-in Traffic Ratio -->
                    <Card
                        class="bg-white border-zinc-200/80 shadow-2xs rounded-xl"
                    >
                        <CardHeader
                            class="p-5 pb-3 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
                        >
                            <div>
                                <CardTitle
                                    class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                                >
                                    Member vs. Walk-in Mix
                                </CardTitle>
                                <CardDescription
                                    class="text-[11px] text-zinc-500 mt-0.5"
                                >
                                    Traffic split by entry pass type
                                </CardDescription>
                            </div>
                            <span
                                class="px-2 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-zinc-100/80 rounded border border-zinc-200/70"
                            >
                                MIX
                            </span>
                        </CardHeader>
                        <CardContent class="p-5 pt-4">
                            <Skeleton
                                v-if="isInitialLoading"
                                class="h-52 w-full rounded-lg bg-zinc-100"
                            />
                            <VisitorRatioChart
                                v-else
                                :ratio-data="dashboardStore.visitorRatioData"
                            />
                        </CardContent>
                    </Card>
                </div>

                <!-- Beat 3: Actionable Retention (Expiring Members Watchlist for Staff) -->
                <ExpirationWatchlistCard
                    v-if="authStore.isStaff"
                    :watchlist="dashboardStore.expirationWatchlist"
                    @view-members="handleViewExpiringMembers"
                />
            </div>

            <!-- Right 4 Columns: Live Transaction Ledger Feed -->
            <Card
                class="xl:col-span-4 bg-white border-zinc-200/80 shadow-2xs rounded-xl overflow-hidden xl:sticky xl:top-0"
            >
                <CardHeader
                    class="p-5 pb-3.5 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
                >
                    <div>
                        <CardTitle
                            class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                        >
                            Recent Payments
                        </CardTitle>
                        <CardDescription
                            class="text-[11px] text-zinc-500 mt-0.5"
                        >
                            Latest verified front-desk collections
                        </CardDescription>
                    </div>
                    <router-link
                        :to="{ name: 'payment' }"
                        class="inline-flex items-center gap-1 text-xs font-semibold text-[#F8313B] hover:text-[#e0242e] transition-colors shrink-0"
                    >
                        <span>View all</span>
                        <ArrowUpRight class="w-3.5 h-3.5" />
                    </router-link>
                </CardHeader>

                <CardContent class="p-0">
                    <!-- Initial Skeleton Rows -->
                    <div v-if="isInitialLoading" class="p-5 space-y-4">
                        <div
                            v-for="n in 5"
                            :key="n"
                            class="flex items-center justify-between gap-3"
                        >
                            <div class="space-y-1.5 flex-1">
                                <Skeleton class="h-3.5 w-32 bg-zinc-100" />
                                <Skeleton class="h-3 w-24 bg-zinc-100" />
                            </div>
                            <Skeleton class="h-4 w-16 bg-zinc-100" />
                        </div>
                    </div>

                    <!-- Clean Empty State -->
                    <div
                        v-else-if="
                            dashboardStore.recentTransactions.length === 0
                        "
                        class="m-5 flex flex-col items-center justify-center text-center py-10 px-4 border border-dashed border-zinc-200/90 rounded-xl bg-zinc-50/50"
                    >
                        <div
                            class="w-9 h-9 rounded-lg bg-zinc-100 text-zinc-500 flex items-center justify-center mb-2.5"
                        >
                            <ReceiptText class="w-4 h-4" />
                        </div>
                        <p class="text-xs font-semibold text-[#0F1115]">
                            No recent payments
                        </p>
                        <p
                            class="text-[11px] text-zinc-500 mt-0.5 max-w-[200px]"
                        >
                            Completed membership and walk-in transactions will
                            appear here.
                        </p>
                    </div>

                    <!-- High-Craft Ledger Rows -->
                    <div v-else class="divide-y divide-zinc-100">
                        <div
                            v-for="tx in dashboardStore.recentTransactions"
                            :key="tx.id"
                            class="flex items-center justify-between gap-3 px-5 py-3.5 hover:bg-zinc-50/80 transition-colors"
                        >
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div
                                    class="w-8 h-8 rounded-lg bg-zinc-100 text-[#0F1115] font-bold text-[11px] flex items-center justify-center shrink-0 border border-zinc-200/70"
                                >
                                    {{ getInitials(tx.payer) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p
                                        class="text-xs font-semibold text-[#0F1115] truncate leading-tight"
                                    >
                                        {{ tx.payer }}
                                    </p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span
                                            class="text-[10px] font-semibold uppercase tracking-wider text-zinc-500"
                                        >
                                            {{ tx.type }}
                                        </span>
                                        <span class="text-zinc-300"
                                            >&middot;</span
                                        >
                                        <span
                                            class="text-[11px] text-zinc-500 truncate"
                                        >
                                            {{ tx.timestamp }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="text-xs font-bold text-[#0F1115] tabular-nums shrink-0"
                            >
                                +₱{{ formatCurrency(tx.amount) }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import { useDashboardStore } from "@/stores/dashboardStore";
import { useAuthStore } from "@/stores/auth";
import { useMemberStore } from "@/stores/memberStore";

import RevenueBarChart from "@/pages/private/Components/RevenueBarChart.vue";
import AttendanceLineChart from "@/pages/private/Components/AttendanceLineChart.vue";
import PeakHoursChart from "@/pages/private/Components/PeakHoursChart.vue";
import VisitorRatioChart from "@/pages/private/Components/VisitorRatioChart.vue";
import ExpirationWatchlistCard from "@/pages/private/Components/ExpirationWatchlistCard.vue";

import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";

import {
    RefreshCw,
    AlertCircle,
    TrendingUp,
    Users,
    UserX,
    QrCode,
    ArrowUpRight,
    ReceiptText,
} from "lucide-vue-next";

const router = useRouter();
const dashboardStore = useDashboardStore();
const authStore = useAuthStore();
const memberStore = useMemberStore();

const isInitialLoading = ref(true);
const isRefreshing = ref(false);
const lastSyncedAt = ref(null);
let intervalId = null;

const formatCurrency = (val) => {
    return Number(val || 0).toLocaleString("en-PH", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    });
};

// Stripe/Mercury style split currency for crisp visual hierarchy
const splitCurrency = (val) => {
    const formatted = formatCurrency(val);
    const [whole, fraction] = formatted.split(".");
    return { whole, fraction };
};

const getInitials = (name) => {
    if (!name) return "WK";
    const parts = String(name).trim().split(/\s+/);
    if (parts.length >= 2) {
        return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
    }
    return String(name).substring(0, 2).toUpperCase();
};

const todayDateLabel = computed(() => {
    return new Date().toLocaleDateString("en-PH", {
        weekday: "long",
        month: "short",
        day: "numeric",
        year: "numeric",
    });
});

const currentMonthLabel = computed(() => {
    return new Date().toLocaleDateString("en-PH", {
        month: "long",
        year: "numeric",
    });
});

const currentMonthShort = computed(() => {
    return new Date()
        .toLocaleDateString("en-PH", { month: "short" })
        .toUpperCase();
});

const activeMemberPercentage = computed(() => {
    const active = Number(dashboardStore.metrics.activeMembers || 0);
    const expired = Number(dashboardStore.metrics.expiredMembers || 0);
    const total = active + expired;
    if (total === 0) return 0;
    return Math.round((active / total) * 100);
});

const lastSyncedLabel = computed(() => {
    if (!lastSyncedAt.value) return "Live";
    return `Synced ${lastSyncedAt.value.toLocaleTimeString("en-PH", {
        hour: "numeric",
        minute: "2-digit",
    })}`;
});

const refreshDashboard = async () => {
    if (isRefreshing.value) return;
    isRefreshing.value = true;
    try {
        await dashboardStore.fetchOverview();
        lastSyncedAt.value = new Date();
    } finally {
        isRefreshing.value = false;
    }
};

const handleViewExpiringMembers = () => {
    memberStore.statusFilter = "expiring_soon";

    router.push({
        name: "MemberManagement",
        query: { filter: "expiring_soon" },
    });
};

onMounted(async () => {
    try {
        await dashboardStore.fetchOverview();
        lastSyncedAt.value = new Date();
    } finally {
        isInitialLoading.value = false;
    }

    intervalId = setInterval(async () => {
        if (document.visibilityState === "visible" && !isRefreshing.value) {
            await dashboardStore.fetchOverview();
            lastSyncedAt.value = new Date();
        }
    }, 10000);
});

onUnmounted(() => {
    if (intervalId) {
        clearInterval(intervalId);
        intervalId = null;
    }
});
</script>
