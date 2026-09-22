<template>
    <div class="p-6 space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Payment History & Financial Ledger
                </h1>
                <p class="text-sm text-gray-500">
                    Track membership revenue, walk-in collections, and
                    transaction receipts.
                </p>
            </div>
        </div>

        <!-- Summary Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <!-- Today's Revenue -->
            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-400"
                    >
                        Today's Revenue
                    </p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">
                        {{
                            formatCurrency(
                                paymentStore.summaryData.today_revenue,
                            )
                        }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center text-emerald-600 font-bold"
                >
                    ₱
                </div>
            </div>

            <!-- Weekly Revenue -->
            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-400"
                    >
                        This Week's Revenue
                    </p>
                    <h3 class="text-2xl font-extrabold text-blue-600 mt-1">
                        {{
                            formatCurrency(
                                paymentStore.summaryData.week_revenue,
                            )
                        }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 font-bold"
                >
                    W
                </div>
            </div>

            <!-- Monthly Revenue -->
            <div
                class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold uppercase tracking-wider text-gray-400"
                    >
                        This Month's Revenue
                    </p>
                    <h3 class="text-2xl font-extrabold text-indigo-600 mt-1">
                        {{
                            formatCurrency(
                                paymentStore.summaryData.month_revenue,
                            )
                        }}
                    </h3>
                </div>
                <div
                    class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600 font-bold"
                >
                    M
                </div>
            </div>
        </div>

        <!-- Filter & Search Controls -->
        <div
            class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-wrap gap-4 items-center justify-between"
        >
            <div class="flex flex-wrap gap-3 items-center flex-1">
                <!-- Search Input -->
                <input
                    v-model="paymentStore.filters.search"
                    @input="handleFilterChange"
                    type="text"
                    placeholder="Search receipt # or customer name..."
                    class="px-3 py-2 text-sm border rounded-lg w-64 focus:ring-2 focus:ring-emerald-500 outline-none"
                />

                <!-- Category Dropdown -->
                <select
                    v-model="paymentStore.filters.category"
                    @change="handleFilterChange"
                    class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none"
                >
                    <option value="">All Categories</option>
                    <option value="membership_registration">
                        Registration
                    </option>
                    <option value="membership_renewal">Renewal</option>
                    <option value="walkin_fee">Walk-In Fee</option>
                </select>

                <!-- Date Range -->
                <input
                    v-model="paymentStore.filters.start_date"
                    @change="handleFilterChange"
                    type="date"
                    class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none"
                />
                <span class="text-gray-400 text-xs">to</span>
                <input
                    v-model="paymentStore.filters.end_date"
                    @change="handleFilterChange"
                    type="date"
                    class="px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none"
                />
            </div>

            <!-- Reset Button -->
            <button
                @click="handleResetFilters"
                class="px-3 py-2 text-xs font-semibold text-gray-600 hover:text-gray-900 border rounded-lg hover:bg-gray-50 transition"
            >
                Reset Filters
            </button>

            <!-- Export CSV Button -->
            <button
                @click="paymentStore.exportPayments"
                class="px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg flex items-center gap-1.5 transition"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
                Export CSV
            </button>

            <button
                @click="isSummaryModalOpen = true"
                class="px-3 py-2 text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-1.5 shadow-sm transition"
            >
                <svg
                    class="w-4 h-4 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                    />
                </svg>
                Print Sales Summary
            </button>
        </div>

        <!-- Transaction Table -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b"
                    >
                        <th class="p-4">Receipt #</th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Amount</th>
                        <th class="p-4">Processed By</th>
                        <th class="p-4">Date & Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <tr v-if="isLoading">
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            Loading payment ledger...
                        </td>
                    </tr>
                    <tr v-else-if="paymentStore.payments.length === 0">
                        <td colspan="6" class="p-8 text-center text-gray-400">
                            No payment records found.
                        </td>
                    </tr>
                    <tr
                        v-else
                        v-for="p in paymentStore.payments"
                        :key="p.id"
                        class="hover:bg-gray-50/50 transition"
                    >
                        <td
                            class="p-4 font-mono text-xs font-bold text-gray-700"
                        >
                            {{ p.receipt_no }}
                        </td>
                        <td class="p-4 font-medium text-gray-900">
                            <span v-if="p.member"
                                >{{ p.member.first_name }}
                                {{ p.member.last_name }} ({{
                                    p.member.membership_no
                                }})</span
                            >
                            <span v-else-if="p.walkin"
                                >{{ p.walkin.name }} (Walk-in)</span
                            >
                            <span v-else class="text-gray-400">N/A</span>
                        </td>
                        <td class="p-4">
                            <span
                                class="px-2 py-1 text-xs rounded-full font-medium"
                                :class="{
                                    'bg-emerald-100 text-emerald-800':
                                        p.category ===
                                        'membership_registration',
                                    'bg-blue-100 text-blue-800':
                                        p.category === 'membership_renewal',
                                    'bg-purple-100 text-purple-800':
                                        p.category === 'walkin_fee',
                                }"
                            >
                                {{ formatCategory(p.category) }}
                            </span>
                        </td>
                        <td class="p-4 font-bold text-gray-900">
                            {{ formatCurrency(p.amount) }}
                        </td>
                        <td class="p-4 text-gray-600">
                            {{ p.processed_by?.first_name || "System" }}
                        </td>
                        <td class="p-4 text-xs text-gray-500">
                            {{ formatDate(p.paid_at) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Pagination Footer using usePagination -->
            <div
                v-if="totalItems > 0"
                class="p-4 border-t flex items-center justify-between text-xs text-gray-500"
            >
                <div>
                    Showing
                    <span class="font-bold text-gray-700">{{
                        rangeStart
                    }}</span>
                    to
                    <span class="font-bold text-gray-700">{{ rangeEnd }}</span>
                    of
                    <span class="font-bold text-gray-700">{{
                        totalItems
                    }}</span>
                    transactions
                </div>

                <div class="flex items-center space-x-1">
                    <button
                        @click="prevPage"
                        :disabled="currentPage === 1 || isLoading"
                        class="px-3 py-1.5 border rounded-lg disabled:opacity-40 hover:bg-gray-50"
                    >
                        Previous
                    </button>

                    <button
                        v-for="page in visiblePages"
                        :key="page"
                        @click="goToPage(page)"
                        :class="[
                            'px-3 py-1.5 border rounded-lg font-medium',
                            page === currentPage
                                ? 'bg-emerald-600 text-white border-emerald-600'
                                : 'hover:bg-gray-50',
                        ]"
                    >
                        {{ page }}
                    </button>

                    <button
                        @click="nextPage"
                        :disabled="currentPage === lastPage || isLoading"
                        class="px-3 py-1.5 border rounded-lg disabled:opacity-40 hover:bg-gray-50"
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Modal -->
    <SalesSummaryModal
        :is-open="isSummaryModalOpen"
        @close="isSummaryModalOpen = false"
    />
</template>

<script setup>
import { onMounted, watch, ref } from "vue";
import { usePaymentStore } from "@/stores/paymentStore";
import { usePagination } from "@/composables/usePagination"; // Adjust import path if needed
import SalesSummaryModal from "../Components/SalesSummaryModal.vue";
import ReceiptModal from "../Components/ReceiptModal.vue";

const paymentStore = usePaymentStore();

// Hook up custom pagination composable
const {
    currentPage,
    lastPage,
    totalItems,
    isLoading,
    visiblePages,
    rangeStart,
    rangeEnd,
    prevPage,
    nextPage,
    goToPage,
} = usePagination(paymentStore, (page) => paymentStore.fetchPayments(page));

// Load summary metrics and initial payment ledger on mount
onMounted(() => {
    paymentStore.fetchSummary();
    paymentStore.fetchPayments(1);
});

//local states
const isSummaryModalOpen = ref(false); //flag for the summary modal
const selectedPayment = ref(null);
const isReceiptModalOpen = ref(false);

const openReceiptModal = (payment) => {
    selectedPayment.value = payment;
    isReceiptModalOpen.value = true;
};

// Helper formatters
const formatCurrency = (val) => {
    const num = parseFloat(val) || 0;
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(num);
};

const formatDate = (dateStr) => {
    if (!dateStr) return "N/A";
    return new Date(dateStr).toLocaleString("en-US", {
        dateStyle: "medium",
        timeStyle: "short",
    });
};

const formatCategory = (cat) => {
    const map = {
        membership_registration: "Registration",
        membership_renewal: "Renewal",
        walkin_fee: "Walk-In Fee",
    };
    return map[cat] || cat;
};

// Filter triggers
const handleFilterChange = () => {
    paymentStore.fetchPayments(1);
};

const handleResetFilters = () => {
    paymentStore.resetFilters();
};
</script>
