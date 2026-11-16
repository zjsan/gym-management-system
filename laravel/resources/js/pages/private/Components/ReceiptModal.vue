<template>
    <div
        v-if="isOpen && payment"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 print:p-0 print:bg-white print:static"
    >
        <!-- Modal Dialog -->
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-sm flex flex-col print:shadow-none print:max-w-none print:w-full print:h-auto print:m-0 print:p-0"
        >
            <!-- Top Action Bar (Hidden when printing) -->
            <div
                class="p-4 border-b flex items-center justify-between print:hidden"
            >
                <h3 class="text-sm font-bold text-gray-800">
                    Official Cash Receipt
                </h3>
                <button
                    @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 text-lg font-bold"
                >
                    &times;
                </button>
            </div>

            <!-- Thermal Slip Paper View (80mm footprint) -->
            <div
                class="p-6 font-mono text-xs text-gray-800 space-y-4 print:p-2 print:text-black print:w-[80mm]"
                id="printable-receipt"
            >
                <!-- Header -->
                <div
                    class="text-center space-y-1 border-b border-dashed border-gray-400 pb-3"
                >
                    <h1
                        class="text-base font-extrabold uppercase tracking-wider text-gray-900"
                    >
                        GYM MANAGEMENT
                    </h1>
                    <p
                        class="text-[10px] text-gray-500 uppercase print:text-gray-700"
                    >
                        Official Payment Receipt
                    </p>
                    <p class="text-[10px] text-gray-400">
                        Cash Payment Ledger Entry
                    </p>
                </div>

                <!-- Transaction Details -->
                <div
                    class="space-y-1.5 border-b border-dashed border-gray-400 pb-3"
                >
                    <div class="flex justify-between">
                        <span class="text-gray-500">Receipt No:</span>
                        <span class="font-bold text-gray-900">{{
                            payment.receipt_no
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Date/Time:</span>
                        <span>{{ formatDate(payment.paid_at) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Cashier:</span>
                        <span>{{ staffNamed }}</span>
                    </div>
                </div>

                <!-- Customer Details -->
                <div
                    class="space-y-1.5 border-b border-dashed border-gray-400 pb-3"
                >
                    <div class="flex justify-between">
                        <span class="text-gray-500">Customer:</span>
                        <span class="font-bold text-gray-900">{{
                            customerName
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Type:</span>
                        <span>{{ customerType }}</span>
                    </div>
                    <div
                        v-if="payment.member?.membership_no"
                        class="flex justify-between"
                    >
                        <span class="text-gray-500">Member ID:</span>
                        <span>{{ payment.member.membership_no }}</span>
                    </div>
                </div>

                <!-- Items Paid -->
                <div
                    class="space-y-2 border-b border-dashed border-gray-400 pb-3"
                >
                    <div class="flex justify-between font-bold text-gray-700">
                        <span>DESCRIPTION</span>
                        <span>AMOUNT</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ formattedCategory }}</span>
                        <span>{{ formatCurrency(payment.amount) }}</span>
                    </div>
                </div>

                <!-- Total Paid -->
                <div class="pt-1 space-y-1 text-sm font-bold">
                    <div class="flex justify-between items-center">
                        <span>TOTAL PAID (CASH)</span>
                        <span
                            class="text-base text-emerald-600 print:text-black"
                            >{{ formatCurrency(payment.amount) }}</span
                        >
                    </div>
                </div>

                <!-- Footer -->
                <div
                    class="text-center pt-4 space-y-1 text-[10px] text-gray-400 print:text-gray-600"
                >
                    <p class="font-semibold">Thank you for your workout!</p>
                    <p>Please retain this receipt for verification.</p>
                </div>
            </div>

            <!-- Action Buttons (Hidden when printing) -->
            <div
                class="p-4 bg-gray-50 border-t flex gap-2 justify-end print:hidden"
            >
                <button
                    @click="emit('close')"
                    class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-gray-200 rounded-lg transition"
                >
                    Close
                </button>
                <button
                    @click="handlePrint"
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
                    Print Receipt
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    isOpen: Boolean,
    payment: Object,
});

const emit = defineEmits(["close"]);

const customerName = computed(() => {
    if (!props.payment) return "N/A";
    if (props.payment.member) {
        return `${props.payment.member.first_name} ${props.payment.member.last_name}`;
    }
    if (props.payment.walkin) {
        return props.payment.walkin.name;
    }
    return "Walk-in Guest";
});

const staffNamed = computed(() => {
    if (!props.payment) return "System";

    const staff = props.payment.processed_by || props.payment.processedBy;
    if (!staff) return "System";

    // Robust name resolution avoiding undefined fallbacks
    const fullName = `${staff.first_name} ${staff.last_name}`;
    return fullName || staff.name || "System";
});

const customerType = computed(() => {
    if (!props.payment) return "";
    return props.payment.member ? "Member" : "Walk-In Guest";
});

const formattedCategory = computed(() => {
    if (!props.payment?.category) return "";
    return props.payment.category
        .replace(/_/g, " ")
        .replace(/\b\w/g, (l) => l.toUpperCase());
});

const formatCurrency = (val) => {
    return new Intl.NumberFormat("en-PH", {
        style: "currency",
        currency: "PHP",
    }).format(val || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return "";
    const date = new Date(dateStr);
    return date.toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
    });
};

const handlePrint = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printable-receipt,
    #printable-receipt * {
        visibility: visible;
    }
    #printable-receipt {
        position: absolute;
        top: 0;
        left: 0;
        width: 80mm; /* Standard width */
        max-width: 80mm;
        margin: 0;
        padding: 4mm; /* Small safety padding inside edges */
        font-family:
            "Courier New", Courier, monospace; /* Monospace is clearest on thermal */
        font-size: 12px;
        line-height: 1.2;
        color: #000000;
        background: #ffffff;
    }

    /* Remove default browser headers/footers (date, URL) */
    @page {
        size: 80mm auto; /* auto height for variable receipt lengths */
        margin: 0;
    }
}
</style>
