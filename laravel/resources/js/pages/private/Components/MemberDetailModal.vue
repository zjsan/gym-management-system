<template>
    <Dialog :open="open" @update:open="$emit('update:open', $event)">
        <DialogContent
            v-if="member"
            class="w-[calc(100vw-1.5rem)] sm:max-w-[500px] max-h-[90dvh] flex flex-col bg-white border border-zinc-200/90 rounded-xl p-0 shadow-2xl overflow-hidden"
        >
            <!-- Pinned Identity Header -->
            <DialogHeader
                class="px-5 sm:px-6 py-4 border-b border-zinc-100 bg-zinc-50/40 text-left shrink-0"
            >
                <div class="flex items-start justify-between gap-3 pr-7">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <img
                            :src="member.photo_url"
                            :alt="`${member.first_name} ${member.last_name}`"
                            class="w-14 h-14 rounded-xl border border-zinc-200/90 object-cover bg-zinc-100 shrink-0"
                        />
                        <div class="min-w-0">
                            <DialogTitle
                                class="text-base font-bold tracking-tight text-[#0F1115] truncate leading-snug"
                            >
                                {{ member.first_name }} {{ member.last_name }}
                            </DialogTitle>
                            <DialogDescription
                                class="flex items-center gap-2 mt-1"
                            >
                                <span
                                    class="px-2 py-0.5 rounded bg-zinc-100 border border-zinc-200/80 font-mono text-[11px] font-semibold text-zinc-700 tabular-nums"
                                >
                                    {{ member.membership_no }}
                                </span>
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[11px] font-semibold border select-none',
                                        member.is_active
                                            ? 'bg-white text-[#0F1115] border-zinc-200/90'
                                            : 'bg-[#F8313B]/[0.06] text-[#F8313B] border-[#F8313B]/20',
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
                            </DialogDescription>
                        </div>
                    </div>
                </div>
            </DialogHeader>

            <!-- Scrollable Body -->
            <div
                class="flex-1 overflow-y-auto px-5 sm:px-6 py-4 space-y-5 text-xs"
            >
                <!-- Membership Validity Strip -->
                <div
                    class="grid grid-cols-3 divide-x divide-zinc-200/80 rounded-xl bg-zinc-50/70 border border-zinc-200/80 overflow-hidden"
                >
                    <div class="p-3">
                        <p
                            class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                        >
                            Started
                        </p>
                        <p
                            class="font-semibold text-[#0F1115] tabular-nums mt-1"
                        >
                            {{ member.membership_start || "—" }}
                        </p>
                    </div>

                    <div class="p-3">
                        <p
                            class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                        >
                            Expires
                        </p>
                        <p
                            class="font-bold tabular-nums mt-1"
                            :class="
                                isExpiredDate
                                    ? 'text-[#F8313B]'
                                    : 'text-[#0F1115]'
                            "
                        >
                            {{ member.membership_end || "—" }}
                        </p>
                    </div>

                    <div class="p-3">
                        <p
                            class="text-[10px] font-semibold uppercase tracking-wider text-zinc-400"
                        >
                            Standing
                        </p>
                        <p
                            class="font-semibold tabular-nums mt-1"
                            :class="
                                isExpiredDate
                                    ? 'text-[#F8313B]'
                                    : 'text-emerald-700'
                            "
                        >
                            {{ standingLabel }}
                        </p>
                    </div>
                </div>

                <!-- Contact & Personal Details -->
                <div class="space-y-1.5">
                    <span
                        class="block text-[10px] font-bold uppercase tracking-wider text-zinc-400"
                    >
                        Contact &amp; Profile Details
                    </span>

                    <dl
                        class="divide-y divide-zinc-100 rounded-xl border border-zinc-200/80 bg-white px-3.5"
                    >
                        <div
                            class="py-2.5 flex items-center justify-between gap-4"
                        >
                            <dt
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0"
                            >
                                <Phone class="w-3.5 h-3.5 text-zinc-400" />
                                <span>Phone</span>
                            </dt>
                            <dd
                                class="text-[#0F1115] font-semibold tabular-nums text-right"
                            >
                                {{ member.contact_number || "—" }}
                            </dd>
                        </div>

                        <div
                            class="py-2.5 flex items-center justify-between gap-4"
                        >
                            <dt
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0"
                            >
                                <HeartPulse class="w-3.5 h-3.5 text-zinc-400" />
                                <span>Emergency</span>
                            </dt>
                            <dd
                                class="text-[#0F1115] font-semibold tabular-nums text-right"
                            >
                                {{ member.emergency_contact_number || "—" }}
                            </dd>
                        </div>

                        <div
                            class="py-2.5 flex items-center justify-between gap-4"
                        >
                            <dt
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0"
                            >
                                <Mail class="w-3.5 h-3.5 text-zinc-400" />
                                <span>Email</span>
                            </dt>
                            <dd
                                class="text-[#0F1115] font-semibold truncate text-right"
                            >
                                {{ member.email || "—" }}
                            </dd>
                        </div>

                        <div
                            class="py-2.5 flex items-center justify-between gap-4"
                        >
                            <dt
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0"
                            >
                                <User class="w-3.5 h-3.5 text-zinc-400" />
                                <span>Demographics</span>
                            </dt>
                            <dd
                                class="text-[#0F1115] font-semibold capitalize tabular-nums text-right"
                            >
                                {{ member.age ? `${member.age} yrs` : "—" }}
                                &middot;
                                {{ member.gender || "—" }}
                            </dd>
                        </div>

                        <div
                            class="py-2.5 flex items-start justify-between gap-4"
                        >
                            <dt
                                class="flex items-center gap-2 text-zinc-500 font-medium shrink-0 mt-0.5"
                            >
                                <MapPin class="w-3.5 h-3.5 text-zinc-400" />
                                <span>Address</span>
                            </dt>
                            <dd
                                class="text-[#0F1115] font-medium text-right leading-relaxed max-w-[260px]"
                            >
                                {{ member.address || "—" }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Pinned Action Footer -->
            <DialogFooter
                class="px-5 sm:px-6 py-3.5 bg-zinc-50/70 border-t border-zinc-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2 shrink-0"
            >
                <!-- Left Secondary Utilities: Edit + QR Pass -->
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="handleEdit"
                        class="flex-1 sm:flex-initial h-9 px-3 rounded-lg border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-700 gap-1.5 cursor-pointer"
                    >
                        <Pencil class="w-3.5 h-3.5 text-zinc-500" />
                        <span>Edit</span>
                    </Button>

                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="handleOpenQr"
                        class="flex-1 sm:flex-initial h-9 px-3 rounded-lg border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-semibold text-[#0F1115] gap-1.5 cursor-pointer"
                    >
                        <QrCode class="w-3.5 h-3.5 text-[#F8313B]" />
                        <span>QR Pass</span>
                    </Button>
                </div>

                <!-- Right Primary Action: Renew (Disabled when Active) -->
                <Button
                    type="button"
                    size="sm"
                    :disabled="isRenewDisabled"
                    @click="$emit('renew', member.id)"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg bg-[#F8313B] hover:bg-[#e0242e] disabled:bg-zinc-200/90 disabled:text-zinc-500 disabled:shadow-none text-white text-xs font-bold shadow-sm shadow-[#F8313B]/20 gap-1.5 transition-all cursor-pointer disabled:cursor-not-allowed"
                >
                    <RefreshCw
                        :class="['w-3.5 h-3.5', loading ? 'animate-spin' : '']"
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

const isExpiredDate = computed(() => {
    if (!props.member) return false;
    if (!props.member.is_active) return true;
    return daysDelta.value !== null && daysDelta.value < 0;
});

const standingLabel = computed(() => {
    if (daysDelta.value === null) {
        return props.member?.is_active ? "Good standing" : "Expired";
    }
    if (daysDelta.value > 1) return `${daysDelta.value} days left`;
    if (daysDelta.value === 1) return "1 day left";
    if (daysDelta.value === 0) return "Expires today";
    const overdue = Math.abs(daysDelta.value);
    return overdue === 1 ? "1 day overdue" : `${overdue} days overdue`;
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

const handleEdit = () => {
    emit("update:open", false);
    emit("edit", props.member);
};

const handleOpenQr = () => {
    emit("update:open", false);
    emit("open-qr", props.member);
};
</script>
