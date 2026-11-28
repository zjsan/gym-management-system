<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent
            v-if="member"
            class="w-[calc(100vw-2rem)] sm:max-w-[440px] max-h-[90dvh] flex flex-col bg-[#FFFFFF] border border-[#D9DEDF] rounded-2xl p-0 shadow-lg overflow-hidden text-[#252A2D]"
        >
            <DialogHeader class="sr-only">
                <DialogTitle>
                    {{ member.first_name }} {{ member.last_name }} — Member
                    Profile
                </DialogTitle>
                <DialogDescription>
                    Member profile card, membership dates, and contact details.
                </DialogDescription>
            </DialogHeader>

            <!-- Top Neutral Header Band (#F5F5F4) -->
            <div
                class="relative h-24 bg-[#F5F5F4] border-b border-[#D9DEDF]/80 px-6 pt-4 flex items-start justify-between shrink-0"
            >
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-md bg-[#FFFFFF] border border-[#D9DEDF] font-mono text-[11px] font-medium text-[#697078] tabular-nums select-all"
                >
                    {{ member.membership_no }}
                </span>

                <!-- Top-Right Options Trigger (leave room for Shadcn's close button) -->
                <div class="pr-7 flex items-center gap-1">
                    <button
                        type="button"
                        @click="menuOpen = !menuOpen"
                        aria-label="More profile options"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-[#879198] hover:text-[#252A2D] hover:bg-[#FFFFFF]/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#252A2D]/15 transition-colors cursor-pointer"
                    >
                        <MoreHorizontal class="w-4 h-4" />
                    </button>
                </div>

                <!-- Optional Quick Menu -->
                <div
                    v-if="menuOpen"
                    class="absolute right-12 top-12 z-20 w-44 rounded-xl bg-[#FFFFFF] border border-[#D9DEDF] shadow-md py-1 text-[12px] font-medium text-[#252A2D]"
                >
                    <button
                        type="button"
                        @click="copyMemberId"
                        class="w-full px-3 py-2 text-left flex items-center justify-between hover:bg-[#F5F5F4] transition-colors cursor-pointer"
                    >
                        <span>{{
                            copiedId ? "Copied ID" : "Copy Member ID"
                        }}</span>
                        <Copy class="w-3.5 h-3.5 text-[#879198]" />
                    </button>
                    <button
                        type="button"
                        @click="
                            menuOpen = false;
                            $emit('update:open', false);
                        "
                        class="w-full px-3 py-2 text-left flex items-center justify-between text-[#697078] hover:text-[#252A2D] hover:bg-[#F5F5F4] transition-colors cursor-pointer"
                    >
                        <span>Close Modal</span>
                        <X class="w-3.5 h-3.5 text-[#879198]" />
                    </button>
                </div>
            </div>

            <!-- Scrollable Body -->
            <div
                @click="menuOpen = false"
                class="flex-1 overflow-y-auto px-6 pb-6"
            >
                <!-- Overlapping Square Portrait -->
                <div class="-mt-12 mb-4 flex items-end justify-between gap-4">
                    <div
                        class="w-28 h-28 rounded-xl bg-[#F5F5F4] border-4 border-[#FFFFFF] ring-1 ring-[#D9DEDF] overflow-hidden shrink-0 shadow-xs"
                    >
                        <img
                            v-if="member.photo_url && !imageFailed"
                            :src="member.photo_url"
                            :alt="`${member.first_name} ${member.last_name}`"
                            @error="imageFailed = true"
                            class="w-full h-full object-cover object-center"
                        />
                        <div
                            v-else
                            class="w-full h-full flex flex-col items-center justify-center text-[#879198] gap-1"
                        >
                            <User class="w-8 h-8 stroke-[1.5]" />
                            <span class="text-[11px] font-medium"
                                >No Photo</span
                            >
                        </div>
                    </div>

                    <!-- Status Pill -->
                    <span
                        :class="[
                            'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-medium border mb-1 shrink-0 select-none',
                            member.is_active
                                ? 'bg-[#F5F5F4] text-[#252A2D] border-[#D9DEDF]'
                                : 'bg-[#F8313B]/[0.07] text-[#F8313B] border-[#F8313B]/25',
                        ]"
                    >
                        <span
                            :class="[
                                'w-1.5 h-1.5 rounded-full shrink-0',
                                member.is_active
                                    ? 'bg-emerald-600'
                                    : 'bg-[#F8313B]',
                            ]"
                        />
                        <span>{{
                            member.is_active ? "Active" : "Expired"
                        }}</span>
                    </span>
                </div>

                <!-- Left-Aligned Name & Role/Demographics -->
                <div class="space-y-1">
                    <h2
                        class="text-[20px] font-semibold tracking-tight leading-snug text-[#252A2D] break-words"
                    >
                        {{ member.first_name }} {{ member.last_name }}
                    </h2>
                    <p
                        class="text-[13px] font-normal text-[#697078] capitalize tabular-nums"
                    >
                        <span>{{ member.gender || "Member" }}</span>
                        <span v-if="member.age">
                            &middot; {{ member.age }} yrs</span
                        >
                        <span v-if="member.date_of_birth">
                            &middot; {{ formatBirthdate(member.date_of_birth) }}
                        </span>
                    </p>
                </div>

                <!-- Primary & Secondary Actions Row -->
                <div class="mt-4 flex items-center gap-2">
                    <Button
                        type="button"
                        :disabled="isRenewDisabled"
                        @click="$emit('renew', member.id)"
                        class="flex-1 h-9 px-3.5 rounded-lg bg-[#F8313B] hover:bg-[#df202a] active:scale-[0.99] disabled:bg-[#F5F5F4] disabled:text-[#879198] disabled:border disabled:border-[#D9DEDF] text-[#FFFFFF] text-[12px] font-medium shadow-2xs gap-1.5 transition-all cursor-pointer disabled:cursor-not-allowed"
                    >
                        <RefreshCw
                            :class="[
                                'w-3.5 h-3.5 shrink-0',
                                loading ? 'animate-spin' : '',
                            ]"
                        />
                        <span>{{ renewButtonLabel }}</span>
                    </Button>

                    <Button
                        type="button"
                        variant="outline"
                        @click="$emit('update:open', false)"
                        class="h-9 px-3.5 rounded-lg bg-[#FFFFFF] hover:bg-[#F5F5F4] active:scale-[0.99] border-[#D9DEDF] text-[#252A2D] text-[12px] font-medium gap-1.5 transition-colors cursor-pointer"
                    >
                        <span>Actions</span>
                        <ChevronDown class="w-3.5 h-3.5 text-[#879198]" />
                    </Button>
                </div>

                <!-- Membership Dates Strip (Started & Expires) -->
                <div
                    class="mt-5 grid grid-cols-2 divide-x divide-[#D9DEDF] rounded-xl border border-[#D9DEDF] bg-[#F5F5F4]/60 overflow-hidden"
                >
                    <div class="px-3.5 py-2.5">
                        <p class="text-[11px] font-medium text-[#697078]">
                            Started
                        </p>
                        <p
                            class="text-[13px] font-semibold text-[#252A2D] tabular-nums mt-0.5"
                        >
                            {{ member.membership_start || "—" }}
                        </p>
                    </div>
                    <div class="px-3.5 py-2.5">
                        <p class="text-[11px] font-medium text-[#697078]">
                            Expires
                        </p>
                        <p
                            class="text-[13px] font-semibold tabular-nums mt-0.5"
                            :class="
                                isExpiredDate
                                    ? 'text-[#F8313B]'
                                    : 'text-[#252A2D]'
                            "
                        >
                            {{ member.membership_end || "—" }}
                        </p>
                    </div>
                </div>

                <!-- Contact Information List -->
                <div class="mt-5 pt-4 border-t border-[#D9DEDF]/80 space-y-1">
                    <a
                        v-if="member.email"
                        :href="`mailto:${member.email}`"
                        class="flex items-center gap-3 px-2.5 py-2 -mx-2.5 rounded-lg hover:bg-[#F5F5F4] transition-colors group"
                    >
                        <Mail class="w-4 h-4 text-[#879198] shrink-0" />
                        <span
                            class="text-[13px] text-[#252A2D] group-hover:underline truncate"
                        >
                            {{ member.email }}
                        </span>
                    </a>

                    <a
                        v-if="member.contact_number"
                        :href="`tel:${member.contact_number}`"
                        class="flex items-center gap-3 px-2.5 py-2 -mx-2.5 rounded-lg hover:bg-[#F5F5F4] transition-colors group"
                    >
                        <Phone class="w-4 h-4 text-[#879198] shrink-0" />
                        <span
                            class="text-[13px] text-[#252A2D] tabular-nums group-hover:underline"
                        >
                            {{ member.contact_number }}
                        </span>
                    </a>

                    <a
                        v-if="member.emergency_contact_number"
                        :href="`tel:${member.emergency_contact_number}`"
                        class="flex items-center gap-3 px-2.5 py-2 -mx-2.5 rounded-lg hover:bg-[#F5F5F4] transition-colors group"
                    >
                        <HeartPulse class="w-4 h-4 text-[#879198] shrink-0" />
                        <span
                            class="text-[13px] text-[#697078] tabular-nums truncate"
                        >
                            <span class="text-[#252A2D]">
                                {{ member.emergency_contact_number }}
                            </span>
                            &middot; Emergency
                        </span>
                    </a>

                    <div
                        v-if="member.address"
                        class="flex items-start gap-3 px-2.5 py-2 -mx-2.5 rounded-lg hover:bg-[#F5F5F4] transition-colors"
                    >
                        <MapPin
                            class="w-4 h-4 text-[#879198] shrink-0 mt-0.5"
                        />
                        <span
                            class="text-[13px] text-[#252A2D] leading-relaxed"
                        >
                            {{ member.address }}
                        </span>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { computed } from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import {
    Phone,
    HeartPulse,
    Mail,
    User,
    MapPin,
    Pencil,
    QrCode,
    RefreshCw,
} from "lucide-vue-next";
import { useDateFormat } from "@/composables/useDateFormat";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    member: {
        type: Object,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:open", "edit", "open-qr", "renew"]);

const daysDelta = computed(() => {
    if (!props.member?.membership_end) return null;
    const end = new Date(props.member.membership_end);
    if (Number.isNaN(end.getTime())) return null;

    const today = new Date();
    today.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    return Math.round(
        (end.getTime() - today.getTime()) / (1000 * 60 * 60 * 24),
    );
});

const { formatBirthdate } = useDateFormat();

const isExpiredDate = computed(() => {
    if (!props.member) return false;
    if (!props.member.is_active) return true;
    return daysDelta.value !== null && daysDelta.value < 0;
});

const isRenewDisabled = computed(() => {
    if (!props.member || props.loading) return true;
    return Boolean(props.member.is_active || !props.member.can_renew);
});

const renewButtonLabel = computed(() => {
    if (props.loading) return "Renewing...";
    if (props.member?.is_active) return "Membership Active";
    return "Renew Membership";
});
</script>
