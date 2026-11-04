<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
    >
        <!-- Modal Container -->
        <div
            class="bg-white rounded-xl shadow-xl w-full max-w-lg flex flex-col overflow-hidden"
        >
            <!-- Modal Header -->
            <div
                class="p-4 border-b flex items-center justify-between bg-gray-50"
            >
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                            />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-800">
                            Email Sales Summary Report
                        </h2>
                        <p class="text-xs text-gray-500">
                            Dispatch financial reports via Gmail SMTP
                        </p>
                    </div>
                </div>
                <button
                    @click="emit('close')"
                    class="text-gray-400 hover:text-gray-600 text-xl font-bold"
                >
                    &times;
                </button>
            </div>

            <!-- Period Selection Controls -->
            <div
                class="p-4 border-b bg-white flex items-center justify-between"
            >
                <span
                    class="text-xs font-semibold text-gray-600 uppercase tracking-wider"
                    >Select Period:</span
                >
                <div class="flex gap-1.5">
                    <button
                        @click="period = 'today'"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'today'
                                ? 'bg-blue-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        Today
                    </button>
                    <button
                        @click="period = 'weekly'"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'weekly'
                                ? 'bg-blue-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        This Week
                    </button>
                    <button
                        @click="period = 'monthly'"
                        :class="[
                            'px-3 py-1.5 text-xs font-semibold rounded-lg transition',
                            period === 'monthly'
                                ? 'bg-blue-600 text-white'
                                : 'bg-white border text-gray-700 hover:bg-gray-100',
                        ]"
                    >
                        This Month
                    </button>
                </div>
            </div>

            <!-- Form Content -->
            <div class="p-6 space-y-4">
                <!-- Recipient Emails Input -->
                <div>
                    <label
                        class="block text-xs font-semibold uppercase tracking-wider text-gray-600 mb-1"
                    >
                        Recipient Email Address(es)
                    </label>
                    <div class="space-y-2">
                        <div
                            v-for="(email, index) in recipientEmails"
                            :key="index"
                            class="flex gap-2"
                        >
                            <input
                                v-model="recipientEmails[index]"
                                type="email"
                                placeholder="owner@gym.com"
                                class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <button
                                v-if="recipientEmails.length > 1"
                                @click="removeEmailField(index)"
                                type="button"
                                class="px-2.5 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 text-xs font-bold transition"
                            >
                                &times;
                            </button>
                        </div>
                    </div>
                    <button
                        @click="addEmailField"
                        type="button"
                        class="mt-2 text-xs text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1"
                    >
                        + Add Another Recipient
                    </button>
                </div>

                <!-- Info Note -->
                <div
                    class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-xs text-blue-800"
                >
                    <p class="font-semibold mb-0.5">What gets sent?</p>
                    <p>
                        The compiled revenue summary, total transactions, and
                        category breakdowns for
                        <strong>{{ period }}</strong> will be formatted and
                        delivered directly to the inbox(es) above.
                    </p>
                </div>

                <!-- Feedback Messages -->
                <div
                    v-if="successMessage"
                    class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs rounded-lg"
                >
                    {{ successMessage }}
                </div>
                <div
                    v-if="errorMessage"
                    class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg"
                >
                    {{ errorMessage }}
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-gray-50 border-t flex justify-end gap-2">
                <button
                    @click="emit('close')"
                    type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-100 transition"
                >
                    Cancel
                </button>
                <button
                    @click="sendEmailSummary"
                    :disabled="paymentStore.loading"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg flex items-center gap-1.5 shadow-sm transition disabled:opacity-50"
                >
                    <svg
                        v-if="paymentStore.loading"
                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>
                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                        ></path>
                    </svg>
                    Send Report
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { usePaymentStore } from "@/stores/paymentStore";

const props = defineProps({
    isOpen: Boolean,
});

const emit = defineEmits(["close"]);

const paymentStore = usePaymentStore();
const period = ref("today");
const recipientEmails = ref([""]);
const successMessage = ref("");
const errorMessage = ref("");

watch(
    () => props.isOpen,
    (newVal) => {
        if (newVal) {
            successMessage.value = "";
            errorMessage.value = "";
        }
    },
);

const addEmailField = () => {
    recipientEmails.value.push("");
};

const removeEmailField = (index) => {
    recipientEmails.value.splice(index, 1);
};

const sendEmailSummary = async () => {
    successMessage.value = "";
    errorMessage.value = "";

    const payload = {
        emails: recipientEmails.value.filter((email) => email.trim() !== ""),
        period: period.value,
    };

    const result = await paymentStore.emailSalesSummary(payload);

    if (result.success) {
        successMessage.value = result.message;
        setTimeout(() => {
            emit("close");
        }, 1500);
    } else {
        errorMessage.value = result.message;
    }
};
</script>
