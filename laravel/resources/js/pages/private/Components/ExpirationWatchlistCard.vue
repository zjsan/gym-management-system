<template>
    <div
        class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between"
    >
        <div>
            <h2 class="text-lg font-bold text-gray-900 mb-1">
                Membership Watchlist
            </h2>
            <p class="text-xs text-gray-500 mb-6">
                Action required for desk check-ins
            </p>

            <div class="space-y-4">
                <!-- Expiring Soon -->
                <div
                    class="flex items-center justify-between p-4 bg-amber-50 rounded-lg border border-amber-100"
                >
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                        <div>
                            <p class="text-sm font-semibold text-amber-900">
                                Expiring in 7 Days
                            </p>
                            <p class="text-xs text-amber-700">
                                Flag for renewal at front desk
                            </p>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-amber-600">{{
                        watchlist.expiring_soon
                    }}</span>
                </div>

                <!-- Recently Expired -->
                <div
                    class="flex items-center justify-between p-4 bg-rose-50 rounded-lg border border-rose-100"
                >
                    <div class="flex items-center space-x-3">
                        <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                        <div>
                            <p class="text-sm font-semibold text-rose-900">
                                Expired Past 7 Days
                            </p>
                            <p class="text-xs text-rose-700">
                                Require membership renewal
                            </p>
                        </div>
                    </div>
                    <span class="text-2xl font-black text-rose-600">{{
                        watchlist.recently_expired
                    }}</span>
                </div>
            </div>
        </div>

        <button
            @click="$emit('view-members')"
            class="mt-6 w-full py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-semibold rounded-lg transition"
        >
            View Expiration List
        </button>
    </div>
</template>

<script setup>
defineProps({
    watchlist: {
        type: Object,
        required: true,
        default: () => ({ expiring_soon: 0, recently_expired: 0 }),
    },
});

defineEmits(["view-members"]);
</script>
