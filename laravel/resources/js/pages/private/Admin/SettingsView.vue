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
                    <AlertDialog v-model:open="isDialogOpen">
                        <AlertDialogTrigger as-child>
                            <button
                                type="button"
                                :disabled="settingStore.saving"
                                class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm flex items-center gap-2 transition disabled:opacity-50 cursor-pointer"
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
                                <span>Save Settings</span>
                            </button>
                        </AlertDialogTrigger>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle
                                    >Are you sure?</AlertDialogTitle
                                >
                                <AlertDialogDescription>
                                    This will update the default gym rates
                                    across the system. Future member sign-ups
                                    and walk-in passes will use these updated
                                    amounts.
                                </AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel as-child>
                                    <Button variant="outline">Cancel</Button>
                                </AlertDialogCancel>
                                <AlertDialogAction as-child>
                                    <Button
                                        @click="handleSubmit"
                                        class="bg-emerald-600 hover:bg-emerald-700 text-white"
                                    >
                                        Confirm & Save
                                    </Button>
                                </AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </div>
            </form>
        </div>

        <!-- Audit Logs / Rate Change History Section -->
        <div
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4 mt-6"
        >
            <div
                class="flex items-center justify-between border-b border-gray-100 pb-4"
            >
                <div>
                    <h2 class="text-base font-semibold text-gray-800">
                        Rate Change Audit History
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Track recent pricing updates and responsible admin
                        users.
                    </p>
                </div>
                <button
                    @click="loadPage"
                    class="text-xs text-emerald-600 hover:text-emerald-700 font-medium flex items-center gap-1 transition cursor-pointer"
                >
                    <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                        />
                    </svg>
                    Refresh Log
                </button>
            </div>

            <!-- Loading Skeleton -->
            <div v-if="settingStore.loadingHistory" class="space-y-3">
                <div
                    v-for="i in 3"
                    :key="i"
                    class="h-12 bg-gray-50 animate-pulse rounded-lg"
                ></div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="!settingStore.history.length"
                class="text-center py-6 text-xs text-gray-400"
            >
                No recent setting changes recorded.
            </div>

            <!-- History List -->
            <div v-else class="space-y-4">
                <div class="divide-y divide-gray-100">
                    <div
                        v-for="item in settingStore.history"
                        :key="item.id"
                        class="py-3.5 flex items-center justify-between text-xs hover:bg-gray-50/50 px-2 rounded-lg transition"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="p-2 bg-emerald-50 text-emerald-700 rounded-lg flex-shrink-0"
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
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">
                                    {{ formatKeyName(item.key) }}
                                </p>
                                <p class="text-[11px] text-gray-400 mt-0.5">
                                    Updated by
                                    <span class="font-medium text-gray-600">{{
                                        formatAdminName(item)
                                    }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="text-right space-y-0.5">
                            <div
                                class="flex items-center justify-end gap-1.5 font-mono text-[11px]"
                            >
                                <span
                                    v-if="item.old_value !== null"
                                    class="text-gray-400 line-through"
                                >
                                    ₱{{ Number(item.old_value).toFixed(2) }}
                                </span>
                                <span
                                    v-if="item.old_value !== null"
                                    class="text-gray-400"
                                    >→</span
                                >
                                <span
                                    class="px-2 py-0.5 rounded font-bold bg-emerald-50 text-emerald-700"
                                >
                                    ₱{{ Number(item.new_value).toFixed(2) }}
                                </span>
                            </div>
                            <p class="text-[10px] text-gray-400">
                                {{ formatDate(item.created_at) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!--pagination control-->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-slate-50/40 border-t border-slate-200/80 gap-4"
                >
                    <div
                        class="text-xs text-slate-500 font-medium order-2 sm:order-1"
                    >
                        Showing
                        <span class="text-slate-800 font-semibold">{{
                            rangeStart
                        }}</span>
                        to
                        <span class="text-slate-800 font-semibold">{{
                            rangeEnd
                        }}</span>
                        of
                        <span class="text-slate-800 font-semibold">{{
                            totalItems
                        }}</span>
                        entries
                    </div>

                    <div
                        class="flex items-center gap-1.5 order-1 sm:order-2 w-full sm:w-auto justify-end"
                    >
                        <button
                            @click="prevPage"
                            :disabled="
                                currentPage === 1 || settingStore.loading
                            "
                            class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium shadow-sm transition-all hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-white disabled:cursor-not-allowed cursor-pointer select-none"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                            Prev
                        </button>

                        <div class="hidden md:flex items-center gap-1">
                            <button
                                v-if="visiblePages[0] > 1"
                                @click="goToPage(1)"
                                :disabled="settingStore.loading"
                                class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50"
                            >
                                1
                            </button>

                            <span
                                v-if="visiblePages[0] > 2"
                                class="text-slate-400 text-xs px-1"
                                >...</span
                            >

                            <button
                                v-for="page in visiblePages"
                                :key="page"
                                @click="goToPage(page)"
                                :disabled="settingStore.loading"
                                :class="[
                                    'w-8 h-8 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none',
                                    currentPage === page
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/10'
                                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50',
                                ]"
                            >
                                {{ page }}
                            </button>

                            <span
                                v-if="
                                    visiblePages[visiblePages.length - 1] <
                                    lastPage - 1
                                "
                                class="text-slate-400 text-xs px-1"
                                >...</span
                            >

                            <button
                                v-if="
                                    visiblePages[visiblePages.length - 1] <
                                    lastPage
                                "
                                @click="goToPage(lastPage)"
                                :disabled="settingStore.loading"
                                class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50"
                            >
                                {{ lastPage }}
                            </button>
                        </div>

                        <span
                            class="text-xs font-medium text-slate-500 md:hidden px-2"
                        >
                            Page {{ currentPage }} of {{ lastPage }}
                        </span>

                        <button
                            @click="nextPage"
                            :disabled="
                                currentPage === lastPage || settingStore.loading
                            "
                            class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium shadow-sm transition-all hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-white disabled:cursor-not-allowed cursor-pointer select-none"
                        >
                            Next
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5 ml-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, watch, reactive } from "vue";
import { useSettingStore } from "@/stores/settingStore";
import { usePagination } from "@/composables/usePagination";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";

const settingStore = useSettingStore();

const isDialogOpen = ref(false);

const formData = ref({
    walkin_daily_fee: 0,
    monthly_membership_fee: 0,
});

const errorMessage = ref("");

const loadPage = async (pageNumber) => {
    try {
        errorMessage.value = "";
        await settingStore.fetchAuditHistory(pageNumber);
    } catch (err) {
        errorMessage.value =
            err?.response?.data?.message ||
            err?.message ||
            "Failed to load registry.";
    }
};

// Custom pagination composable setup
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
} = usePagination(settingStore, loadPage);

onMounted(async () => {
    //  Fetch settings from backend
    const result = await settingStore.fetchSettings();

    //  debugging
    console.log("Fetched Settings from Store:", settingStore.settings);

    // Sync fetched values to the reactive form state
    if (result.success && settingStore.settings) {
        console.log("results");
        formData.walkin_daily_fee = Number(
            settingStore.settings.walkin_daily_fee,
        );
        formData.monthly_membership_fee = Number(
            settingStore.settings.monthly_membership_fee,
        );
    }

    // Fetch audit history logs
    await loadPage(currentPage.value);
});

//automatically update the form values when it gets change
watch(
    () => settingStore.settings,
    (newSettings) => {
        if (newSettings) {
            formData.value.walkin_daily_fee = Number(
                newSettings.walkin_daily_fee ?? 0,
            );
            formData.value.monthly_membership_fee = Number(
                newSettings.monthly_membership_fee ?? 0,
            );
        }
    },
    { immediate: true, deep: true },
);

const handleSubmit = async () => {
    isDialogOpen.value = false; // Close the dialog
    const result = await settingStore.updateSettings(formData.value);
    loadPage(currentPage.value); // Hot-reload current dataset view
    if (result.success) {
        setTimeout(() => {
            settingStore.successMessage = "";
        }, 4000);
    }
};

// Helper function to format key names cleanly
const formatKeyName = (key) => {
    const labels = {
        walkin_daily_fee: "Walk-in Daily Fee",
        monthly_membership_fee: "Monthly Membership Fee",
    };
    return labels[key] || key;
};

const formatAdminName = (item) => {
    if (!item) return "Admin System";
    const staff = item.updated_by;

    if (!staff) return "Admin System";

    // Robust name resolution avoiding undefined fallbacks
    const fullName = `${staff.first_name} ${staff.last_name}`;
    return fullName || staff.name || "Admin System";
};

// Helper function for dates
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    return new Date(dateString).toLocaleString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
        hour: "numeric",
        minute: "2-digit",
        hour12: true,
    });
};
</script>
