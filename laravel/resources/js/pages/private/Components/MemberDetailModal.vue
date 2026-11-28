<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent
            v-if="member"
            class="w-[calc(100vw-1.5rem)] sm:max-w-[460px] max-h-[90dvh] flex flex-col bg-white border border-zinc-200 rounded-xl p-0 shadow-xl overflow-hidden"
        >
            <!-- Pinned Modal Top Bar -->
            <DialogHeader
                class="px-5 sm:px-6 py-3.5 border-b border-zinc-200/80 bg-white text-left shrink-0"
            >
                <div class="flex items-center justify-between gap-3 pr-7">
                    <DialogTitle
                        class="text-sm font-semibold tracking-tight text-zinc-900 truncate"
                    >
                        Member ID Pass
                    </DialogTitle>
                    <DialogDescription class="sr-only">
                        Member identification pass and contact details for
                        {{ member.first_name }} {{ member.last_name }}.
                    </DialogDescription>
                </div>
            </DialogHeader>

            <!-- Scrollable Content -->
            <div
                class="flex-1 overflow-y-auto px-5 sm:px-6 py-5 space-y-5 text-xs"
            >
                <!-- Centered Digital ID Card -->
                <section
                    class="rounded-2xl border border-zinc-200/90 bg-zinc-50/60 overflow-hidden shadow-2xs"
                >
                    <!-- Subtle Lanyard / Header Band -->
                    <div
                        class="h-16 bg-gradient-to-b from-zinc-100 to-zinc-100/40 border-b border-zinc-200/60 flex items-start justify-between px-4 pt-3"
                    >
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-md bg-white/90 border border-zinc-200/90 font-mono text-[11px] font-semibold text-zinc-700 tabular-nums select-all"
                        >
                            {{ member.membership_no }}
                        </span>

                        <span
                            :class="[
                                'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold border bg-white select-none',
                                member.is_active
                                    ? 'text-emerald-700 border-emerald-200/90'
                                    : 'text-[#F8313B] border-[#F8313B]/25',
                            ]"
                        >
                            <span
                                :class="[
                                    'w-1.5 h-1.5 rounded-full shrink-0',
                                    member.is_active
                                        ? 'bg-emerald-500'
                                        : 'bg-[#F8313B]',
                                ]"
                            />
                            <span>{{
                                member.is_active ? "Active" : "Expired"
                            }}</span>
                        </span>
                    </div>

                    <!-- Centered Portrait & Identity Stack -->
                    <div
                        class="px-5 pb-5 -mt-10 flex flex-col items-center text-center"
                    >
                        <!-- Large Centered Portrait Frame -->
                        <div
                            class="w-36 h-40 rounded-2xl border-4 border-white ring-1 ring-zinc-200/90 bg-white shadow-sm overflow-hidden shrink-0"
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
                                class="w-full h-full flex flex-col items-center justify-center bg-zinc-100 text-zinc-400 gap-1.5"
                            >
                                <User class="w-9 h-9 stroke-[1.5]" />
                                <span class="text-[11px] font-medium">
                                    No Photo
                                </span>
                            </div>
                        </div>

                        <!-- Name & Demographics Beneath Photo -->
                        <div class="mt-3.5 space-y-1 max-w-full">
                            <h2
                                class="text-lg font-semibold tracking-tight text-zinc-900 leading-snug break-words"
                            >
                                {{ member.first_name }} {{ member.last_name }}
                            </h2>

                            <p
                                class="text-xs text-zinc-500 capitalize tabular-nums"
                            >
                                {{ member.gender || "—" }}
                                <span v-if="member.age">
                                    &middot; {{ member.age }} yrs
                                </span>
                                <span v-if="member.date_of_birth">
                                    &middot;
                                    {{ formatBirthdate(member.date_of_birth) }}
                                </span>
                            </p>
                        </div>

                        <!-- Clean 2-Column Membership Window -->
                        <div
                            class="w-full mt-4 grid grid-cols-2 divide-x divide-zinc-200/80 rounded-xl bg-white border border-zinc-200/80 overflow-hidden text-left"
                        >
                            <div class="px-3.5 py-2.5">
                                <p
                                    class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                                >
                                    Started
                                </p>
                                <p
                                    class="text-xs font-semibold text-zinc-900 tabular-nums mt-0.5"
                                >
                                    {{ member.membership_start || "—" }}
                                </p>
                            </div>

                            <div class="px-3.5 py-2.5">
                                <p
                                    class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                                >
                                    Expires
                                </p>
                                <p
                                    class="text-xs font-semibold tabular-nums mt-0.5"
                                    :class="
                                        isExpiredDate
                                            ? 'text-[#F8313B]'
                                            : 'text-zinc-900'
                                    "
                                >
                                    {{ member.membership_end || "—" }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Structured Contact & Address Section -->
                <section class="space-y-2">
                    <h3
                        class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400"
                    >
                        Contact &amp; Address
                    </h3>

                    <div
                        class="rounded-xl border border-zinc-200/80 bg-white divide-y divide-zinc-100 overflow-hidden"
                    >
                        <!-- Phone & Emergency (2-Col Bento Row) -->
                        <div
                            class="grid grid-cols-1 sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x divide-zinc-100"
                        >
                            <div class="p-3.5">
                                <div
                                    class="flex items-center gap-1.5 text-[11px] font-medium text-zinc-500"
                                >
                                    <Phone
                                        class="w-3.5 h-3.5 text-zinc-400 shrink-0"
                                    />
                                    <span>Phone Number</span>
                                </div>
                                <p
                                    class="text-xs font-semibold text-zinc-900 tabular-nums mt-1 select-all"
                                >
                                    {{ member.contact_number || "—" }}
                                </p>
                            </div>

                            <div class="p-3.5">
                                <div
                                    class="flex items-center gap-1.5 text-[11px] font-medium text-zinc-500"
                                >
                                    <HeartPulse
                                        class="w-3.5 h-3.5 text-zinc-400 shrink-0"
                                    />
                                    <span>Emergency Contact</span>
                                </div>
                                <p
                                    class="text-xs font-semibold text-zinc-900 tabular-nums mt-1 select-all"
                                >
                                    {{ member.emergency_contact_number || "—" }}
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div
                            class="px-3.5 py-3 flex items-center justify-between gap-4"
                        >
                            <div
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0"
                            >
                                <Mail
                                    class="w-3.5 h-3.5 text-zinc-400 shrink-0"
                                />
                                <span>Email</span>
                            </div>
                            <p
                                class="text-zinc-900 font-medium truncate text-right select-all"
                            >
                                {{ member.email || "—" }}
                            </p>
                        </div>

                        <!-- Residential Address -->
                        <div
                            class="px-3.5 py-3 flex items-start justify-between gap-4"
                        >
                            <div
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0 mt-0.5"
                            >
                                <MapPin
                                    class="w-3.5 h-3.5 text-zinc-400 shrink-0"
                                />
                                <span>Address</span>
                            </div>
                            <p
                                class="text-zinc-900 font-medium text-right leading-relaxed max-w-[260px]"
                            >
                                {{ member.address || "—" }}
                            </p>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Pinned Footer -->
            <DialogFooter
                class="px-5 sm:px-6 py-3.5 bg-zinc-50 border-t border-zinc-200/80 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2 shrink-0"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="$emit('update:open', false)"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg border-zinc-200 bg-white hover:bg-zinc-100/70 text-xs font-medium text-zinc-700 cursor-pointer"
                >
                    Close
                </Button>

                <Button
                    type="button"
                    size="sm"
                    :disabled="isRenewDisabled"
                    @click="$emit('renew', member.id)"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg bg-[#F8313B] hover:bg-[#df202a] active:scale-[0.99] disabled:bg-zinc-200 disabled:text-zinc-500 disabled:shadow-none text-white text-xs font-medium shadow-xs gap-1.5 transition-all cursor-pointer disabled:cursor-not-allowed"
                >
                    <RefreshCw
                        :class="[
                            'w-3.5 h-3.5 shrink-0',
                            loading ? 'animate-spin' : '',
                        ]"
                    />
                    <span>{{ renewButtonLabel }}</span>
                </Button>
            </DialogFooter>
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
