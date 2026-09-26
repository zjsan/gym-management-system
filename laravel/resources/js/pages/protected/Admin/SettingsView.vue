<template>
    <div class="max-w-4xl mx-auto p-6 space-y-6">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Gym Settings</h1>
            <p class="text-sm text-gray-500">
                Configure core rates, pricing rules, and business settings.
            </p>
        </div>

        <!-- Alert Notifications -->
        <div
            v-if="settingStore.successMessage"
            class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 text-sm rounded-r-lg"
        >
            {{ settingStore.successMessage }}
        </div>

        <div
            v-if="typeof settingStore.errors === 'string'"
            class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 text-sm rounded-r-lg"
        >
            {{ settingStore.errors }}
        </div>

        <!-- Main Settings Form -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
        >
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-800">
                    Rates & Pricing Configuration
                </h2>
                <p class="text-xs text-gray-500 mt-0.5">
                    These rates determine default amounts charged during member
                    signups and walk-in entries.
                </p>
            </div>

            <!-- Loading Skeleton -->
            <div v-if="settingStore.loading" class="p-6 space-y-4">
                <div class="h-10 bg-gray-100 rounded-lg animate-pulse"></div>
                <div class="h-10 bg-gray-100 rounded-lg animate-pulse"></div>
            </div>

            <!-- Form Content -->
            <form v-else @submit.prevent="handleSubmit" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Walk-in Daily Fee -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-gray-700"
                        >
                            Walk-in Daily Pass Fee (₱)
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-semibold"
                            >
                                ₱
                            </div>
                            <input
                                v-model.number="formData.walkin_daily_fee"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm text-gray-900"
                                placeholder="100.00"
                            />
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Standard rate charged for non-member day passes.
                        </p>
                        <span
                            v-if="settingStore.errors?.walkin_daily_fee"
                            class="text-xs text-rose-600"
                        >
                            {{ settingStore.errors.walkin_daily_fee[0] }}
                        </span>
                    </div>

                    <!-- Monthly Membership Fee -->
                    <div class="space-y-1.5">
                        <label
                            class="block text-xs font-bold uppercase tracking-wider text-gray-700"
                        >
                            Monthly Membership Fee (₱)
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400 font-semibold"
                            >
                                ₱
                            </div>
                            <input
                                v-model.number="formData.monthly_membership_fee"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                class="block w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm text-gray-900"
                                placeholder="1200.00"
                            />
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Default rate for new member registrations and
                            monthly renewals.
                        </p>
                        <span
                            v-if="settingStore.errors?.monthly_membership_fee"
                            class="text-xs text-rose-600"
                        >
                            {{ settingStore.errors.monthly_membership_fee[0] }}
                        </span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button
                        type="submit"
                        :disabled="settingStore.saving"
                        class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm flex items-center gap-2 transition disabled:opacity-50"
                    >
                        <svg
                            v-if="settingStore.saving"
                            class="animate-spin h-4 w-4 text-white"
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
                        <span>{{
                            settingStore.saving
                                ? "Saving Changes..."
                                : "Save Settings"
                        }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import { useSettingStore } from "@/stores/settingStore";

const settingStore = useSettingStore();

const formData = ref({
    walkin_daily_fee: 0,
    monthly_membership_fee: 0,
});

onMounted(async () => {
    await settingStore.fetchSettings();
    formData.value = { ...settingStore.settings };
});

const handleSubmit = async () => {
    const result = await settingStore.updateSettings(formData.value);
    if (result.success) {
        // Clear message after 4 seconds
        setTimeout(() => {
            settingStore.successMessage = "";
        }, 4000);
    }
};
</script>
