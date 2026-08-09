<template>
    <AlertDialog :open="isOpen" @update:open="handleClose">
        <AlertDialogContent class="sm:max-w-[425px]">
            <AlertDialogHeader>
                <AlertDialogTitle>Member Verification</AlertDialogTitle>
                <AlertDialogDescription>
                    Verify member identity before confirming check-in.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <!-- Member Details Card -->
            <div
                v-if="member"
                class="flex flex-col items-center py-4 space-y-3"
            >
                <!-- Photo with Avatar Fallback -->
                <div class="relative w-28 h-28">
                    <img
                        v-if="member.photo_url"
                        :src="member.photo_url"
                        :alt="member.full_name"
                        class="w-full h-full object-cover rounded-full border-2 border-border shadow-sm"
                    />
                    <div
                        v-else
                        class="w-full h-full rounded-full bg-muted flex items-center justify-center text-3xl font-bold text-muted-foreground border-2 border-border"
                    >
                        {{ member.full_name.charAt(0) }}
                    </div>

                    <!-- Active/Expired Badge -->
                    <span
                        class="absolute bottom-0 right-0 px-2 py-0.5 text-[10px] font-bold uppercase rounded-full text-white shadow-sm"
                        :class="
                            member.is_active
                                ? 'bg-emerald-600'
                                : 'bg-destructive'
                        "
                    >
                        {{ member.is_active ? "Active" : "Expired" }}
                    </span>
                </div>

                <!-- Member Info -->
                <div class="text-center">
                    <h3 class="text-lg font-bold text-foreground">
                        {{ member.full_name }}
                    </h3>
                    <p class="text-xs font-mono text-muted-foreground">
                        {{ member.membership_no }}
                    </p>
                </div>

                <!-- Membership Dates -->
                <div
                    class="w-full bg-muted/50 p-3 rounded-lg border text-xs space-y-1 mt-2"
                >
                    <div class="flex justify-between">
                        <span class="text-muted-foreground">Start Date:</span>
                        <span class="font-medium text-foreground">{{
                            member.membership_start
                        }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-muted-foreground"
                            >Expiration Date:</span
                        >
                        <span class="font-medium text-foreground">{{
                            member.membership_end
                        }}</span>
                    </div>
                </div>

                <!-- Error Message in Modal -->
                <div
                    v-if="attendanceStore.errorMessage"
                    class="w-full p-2.5 bg-destructive/10 text-destructive text-xs rounded-md"
                >
                    {{ attendanceStore.errorMessage }}
                </div>
            </div>

            <AlertDialogFooter>
                <AlertDialogCancel
                    :disabled="attendanceStore.isLoading"
                    @click="handleClose"
                >
                    Cancel
                </AlertDialogCancel>

                <AlertDialogAction
                    @click="handleConfirmCheckIn"
                    :disabled="!member?.is_active || attendanceStore.isLoading"
                    :class="
                        !member?.is_active
                            ? 'bg-muted text-muted-foreground cursor-not-allowed hover:bg-muted'
                            : ''
                    "
                >
                    {{
                        attendanceStore.isLoading
                            ? "Checking in..."
                            : "Confirm Check-In"
                    }}
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup>
import { computed } from "vue";
import { useAttendanceStore } from "@/stores/attendance";
import {
    AlertDialog,
    AlertDialogContent,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogCancel,
    AlertDialogAction,
} from "@/components/ui/alert-dialog";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["update:open"]);

const attendanceStore = useAttendanceStore();

// Computed property for double-binding open state with parent
const isOpen = computed({
    get: () => props.open,
    set: (val) => emit("update:open", val),
});

const member = computed(() => attendanceStore.lookupMemberData);

const handleClose = () => {
    attendanceStore.clearLookup();
    isOpen.value = false;
};

const handleConfirmCheckIn = async () => {
    if (!member.value || !member.value.is_active || !member.value.id) {
        console.warn("Attempted check-in with invalid or null member state.");
        return;
    }

    try {
        const res = await attendanceStore.submitCheckIn({
            member_id: member.value.id,
            entry_method: "manual_member", //Keep consistent if your backend tracks entry method
        });

        if (res?.success) {
            isOpen.value = false;
            // Clear the member reference safely after success
            attendanceStore.clearLookup();
        }
    } catch (error) {
        console.error("Check-in submission error:", error);
    }
};
</script>
