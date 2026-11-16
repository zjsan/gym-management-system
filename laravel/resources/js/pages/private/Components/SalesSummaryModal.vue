<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 print:p-0 print:bg-white print:static"
    >
        <!-- Modal Container -->
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col print:shadow-none print:max-w-full print:w-full print:h-auto print:max-h-none"
        >
            <!-- Modal Header (Hidden during Print) -->
            <div
                class="p-4 border-b flex items-center justify-between print:hidden"
            >
                <h2 class="text-lg font-bold text-gray-800">
                    Print Sales Summary
                </h2>
                <button
                    @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold"
                >
                    &times;
                </button>
            </div>

            <!-- Period Selection Controls (Hidden during Print) -->
            <div
                class="p-4 bg-gray-50 border-b flex items-center justify-between print:hidden"
            >
                <div class="flex gap-2">
                    <button
                        @click="changePeriod('today')"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'today'
                                ? 'bg-emerald-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        Today
                    </button>
                    <button
                        @click="changePeriod('weekly')"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'weekly'
                                ? 'bg-emerald-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        This Week
                    </button>
                    <button
                        @click="changePeriod('monthly')"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'monthly'
                                ? 'bg-emerald-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        This Month
                    </button>
                </div>

                <button
                    @click="handlePrint"
                    :disabled="paymentStore.loading"
                    class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg flex items-center gap-1.5 shadow-sm transition"
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
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"
                        />
                    </svg>
                    Print Report
                </button>
            </div>

            <!-- Printable Report Content -->
            <div
                class="p-6 overflow-y-auto space-y-6 text-gray-800 print:overflow-visible print:p-0"
                id="printable-sales-report"
            >
                <div
                    v-if="paymentStore.loading"
                    class="py-12 text-center text-gray-400 text-sm"
                >
                    Generating sales report...
                </div>

                <template v-else-if="paymentStore.salesReportData">
                    <!-- Printable Header -->
                    <div class="border-b pb-4 text-center">
                        <h1
                            class="text-xl font-bold uppercase tracking-wide text-gray-900"
                        >
                            Imaq - Gym Management System
                        </h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Financial Collection & Sales Summary Report
                        </p>
                        <div
                            class="mt-3 inline-block px-3 py-1 bg-gray-100 rounded-full text-xs font-semibold text-gray-700 print:bg-transparent print:p-0"
                        >
                            Coverage:
                            {{ paymentStore.salesReportData.period_label }}
                        </div>
                    </div>

                    <!-- Key Totals -->
                    <div
                        class="grid grid-cols-2 gap-4 bg-gray-50 p-4 rounded-lg border print:bg-transparent print:border-gray-300"
                    >
                        <div>
                            <p
                                class="text-xs text-gray-500 uppercase font-semibold"
                            >
                                Total Cash Revenue
                            </p>
                            <p
                                class="text-2xl font-extrabold text-emerald-600 print:text-gray-900"
                            >
                                {{
                                    formatCurrency(
                                        paymentStore.salesReportData
                                            .total_revenue,
                                    )
                                }}
                            </p>
                        </div>
                        <div>
                            <p
                                class="text-xs text-gray-500 uppercase font-semibold"
                            >
                                Total Transactions
                            </p>
                            <p class="text-2xl font-bold text-gray-800">
                                {{
                                    paymentStore.salesReportData
                                        .total_transactions
                                }}
                                receipts
                            </p>
                        </div>
                    </div>

                    <!-- Revenue Category Breakdown -->
                    <div>
                        <h3
                            class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-2"
                        >
                            Revenue Breakdown by Category
                        </h3>
                        <table
                            class="w-full text-left text-xs border border-gray-200"
                        >
                            <thead
                                class="bg-gray-100 border-b text-gray-600 font-semibold print:bg-gray-50"
                            >
                                <tr>
                                    <th class="p-2 border-r">Category</th>
                                    <th class="p-2 border-r text-center">
                                        Count
                                    </th>
                                    <th class="p-2 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(item, key) in paymentStore
                                        .salesReportData.category_breakdown"
                                    :key="key"
                                >
                                    <td class="p-2 border-r font-medium">
                                        {{ item.label }}
                                    </td>
                                    <td class="p-2 border-r text-center">
                                        {{ item.count }}
                                    </td>
                                    <td class="p-2 text-right font-bold">
                                        {{ formatCurrency(item.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Cashier / Staff Collection Breakdown -->
                    <div
                        v-if="
                            paymentStore.salesReportData.cashier_breakdown
                                .length > 0
                        "
                    >
                        <h3
                            class="text-xs font-bold uppercase text-gray-500 tracking-wider mb-2"
                        >
                            Staff Collections
                        </h3>
                        <table
                            class="w-full text-left text-xs border border-gray-200"
                        >
                            <thead
                                class="bg-gray-100 border-b text-gray-600 font-semibold print:bg-gray-50"
                            >
                                <tr>
                                    <th class="p-2 border-r">
                                        Staff / Cashier
                                    </th>
                                    <th class="p-2 border-r text-center">
                                        Receipts Handled
                                    </th>
                                    <th class="p-2 text-right">
                                        Total Processed
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr
                                    v-for="(cashier, idx) in paymentStore
                                        .salesReportData.cashier_breakdown"
                                    :key="idx"
                                >
                                    <td class="p-2 border-r font-medium">
                                        {{ cashier.staff_name }}
                                    </td>
                                    <td class="p-2 border-r text-center">
                                        {{ cashier.count }}
                                    </td>
                                    <td class="p-2 text-right font-bold">
                                        {{ formatCurrency(cashier.total) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Sign-off Block for Print Filing -->
                    <div
                        class="pt-8 grid grid-cols-2 gap-8 text-xs text-gray-600 border-t print:mt-12"
                    >
                        <!-- Prepared By Block -->
                        <div>
                            <p class="font-semibold text-gray-700">
                                Prepared By:
                            </p>

                            <!-- Container for name and line to keep them tightly bound -->
                            <div class="mt-8 w-48 text-center">
                                <p class="font-bold text-gray-900 pb-1">
                                    {{ formattedStaffName }}
                                </p>
                                <div
                                    class="border-b border-gray-400 w-full"
                                ></div>
                                <p class="text-[8px] text-gray-400 mt-1">
                                    Cashier / Staff Signature Over Printed Name
                                </p>
                            </div>
                        </div>

                        <!-- Verified By Block -->
                        <div>
                            <p class="font-semibold text-gray-700">
                                Verified By:
                            </p>

                            <!-- Container for name and line to keep them tightly bound -->
                            <div class="mt-8 w-48 text-center">
                                <p class="font-bold text-gray-900 pb-1">
                                    {{ gymManagerName }}
                                </p>
                                <div
                                    class="border-b border-gray-400 w-full"
                                ></div>
                                <p class="text-[8px] text-gray-400 mt-1">
                                    Gym Manager Signature Over Printed Name
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Report Metadata -->
                    <div class="text-[10px] text-gray-400 text-center pt-2">
                        Report generated on
                        {{ paymentStore.salesReportData.generated_at }}
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, watch, computed } from "vue";
import { usePaymentStore } from "@/stores/paymentStore";
import { useAuthStore } from "@/stores/auth";

const props = defineProps({
    isOpen: Boolean,
});

const gymManagerName = ref("John Doe"); // Replace with actual gym manager's name if available
const emit = defineEmits(["close"]);

const paymentStore = usePaymentStore();
const authStore = useAuthStore();
const period = ref("today");

// Helper function to format numbers as Philippine Peso
const formatCurrency = (value) => {
    if (value === undefined || value === null) return "₱0.00";
    return Number(value).toLocaleString("en-PH", {
        style: "currency",
        currency: "PHP",
    });
};

// Safely compute the logged-in staff name
const formattedStaffName = computed(() => {
    const user = authStore.user;
    if (user?.first_name && user?.last_name) {
        return `${user.first_name} ${user.last_name}`;
    }
    return user?.name || "Authorized Staff";
});

const changePeriod = (newPeriod) => {
    period.value = newPeriod;
    paymentStore.fetchSalesSummary(period.value);
};

const handlePrint = () => {
    // Set custom text for the browser's print header/title
    document.title = "Imaq - Gym Management System";

    window.print();
};

watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            paymentStore.fetchSalesSummary(period.value);
        }
    },
);
</script>

<style>
@media print {
    /* Define page size and strict margins to prevent overflow */
    @page {
        size: portrait;
        margin: 10mm;
    }

    /* Force the page wrapper to strictly lock to 100% height */
    html,
    body {
        height: 100% !important;
        overflow: hidden !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Make only the sales report and its children visible */
    #printable-sales-report,
    #printable-sales-report * {
        visibility: visible !important;
    }

    #printable-sales-report {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: 100% !important; /*Force to container limits */
        max-height: 100% !important;
        overflow: hidden !important; /* Prevents layout leaking downward */
        margin: 0 !important;
        padding: 0 !important;
        background: white !important;
        box-sizing: border-box;
    }
}
</style>
