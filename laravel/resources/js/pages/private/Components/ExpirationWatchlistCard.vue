<template>
    <Card class="bg-white border-zinc-200/80 shadow-2xs rounded-xl">
        <CardHeader
            class="p-5 pb-3.5 border-b border-zinc-100 flex flex-row items-center justify-between gap-2 space-y-0"
        >
            <div>
                <CardTitle
                    class="text-[13px] font-bold tracking-tight text-[#0F1115]"
                >
                    Membership Watchlist
                </CardTitle>
                <CardDescription class="text-[11px] text-zinc-500 mt-0.5">
                    Action required for front-desk check-ins &amp; renewals
                </CardDescription>
            </div>
        </CardHeader>

        <CardContent class="p-5 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Expiring Soon -->
                <div
                    class="flex items-center justify-between gap-3 p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50 hover:border-amber-300/80 transition-colors"
                >
                    <div class="flex items-start gap-3 min-w-0">
                        <span
                            class="w-2 h-2 rounded-full bg-amber-500 mt-1.5 shrink-0"
                            aria-hidden="true"
                        />
                        <div class="min-w-0">
                            <p
                                class="text-xs font-bold text-[#0F1115] truncate"
                            >
                                Expiring in 7 Days
                            </p>
                            <p class="text-[11px] text-zinc-500 mt-0.5">
                                Flag for renewal at front desk
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-2xl font-extrabold text-[#0F1115] tabular-nums tracking-tight shrink-0"
                    >
                        {{ Number(watchlist?.expiring_soon || 0) }}
                    </span>
                </div>

                <!-- Recently Expired -->
                <div
                    class="flex items-center justify-between gap-3 p-4 rounded-xl border border-zinc-200/80 bg-zinc-50/50 hover:border-[#F8313B]/40 transition-colors"
                >
                    <div class="flex items-start gap-3 min-w-0">
                        <span
                            class="w-2 h-2 rounded-full bg-[#F8313B] mt-1.5 shrink-0"
                            aria-hidden="true"
                        />
                        <div class="min-w-0">
                            <p
                                class="text-xs font-bold text-[#0F1115] truncate"
                            >
                                Expired Past 7 Days
                            </p>
                            <p class="text-[11px] text-zinc-500 mt-0.5">
                                Requires membership renewal
                            </p>
                        </div>
                    </div>
                    <span
                        class="text-2xl font-extrabold text-[#F8313B] tabular-nums tracking-tight shrink-0"
                    >
                        {{ Number(watchlist?.recently_expired || 0) }}
                    </span>
                </div>
            </div>

            <div class="flex justify-end pt-1">
                <Button
                    type="button"
                    @click="$emit('view-members')"
                    class="h-9 px-4 rounded-lg bg-[#0F1115] hover:bg-zinc-800 active:scale-[0.99] text-white text-xs font-bold transition-all cursor-pointer"
                >
                    View Expiration List
                </Button>
            </div>
        </CardContent>
    </Card>
</template>

<script setup>
import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";

defineProps({
    watchlist: {
        type: Object,
        required: true,
        default: () => ({ expiring_soon: 0, recently_expired: 0 }),
    },
});

defineEmits(["view-members"]);
</script>
