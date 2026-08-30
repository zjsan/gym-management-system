<template>
    <Dialog
        :open="modelValue"
        @update:open="(val) => emit('update:modelValue', val)"
    >
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle class="text-center">Member QR Badge</DialogTitle>
                <DialogDescription class="text-center">
                    <template v-if="member">
                        {{ member.first_name }} {{ member.last_name }}
                        <span class="font-mono text-xs"
                            >(#{{ member.membership_no }})</span
                        >
                    </template>
                </DialogDescription>
            </DialogHeader>

            <div class="flex flex-col items-center py-2">
                <!-- Loading State -->
                <div
                    v-if="isLoading"
                    class="py-12 text-muted-foreground text-xs italic flex flex-col items-center gap-2"
                >
                    <RefreshCw class="w-5 h-5 animate-spin text-primary" />
                    Generating member badge...
                </div>

                <!-- Badge Display Area -->
                <div
                    v-else-if="qrData"
                    id="printable-qr-badge"
                    class="bg-muted/50 border border-border rounded-xl p-5 my-2 flex flex-col items-center w-full max-w-[260px] shadow-sm"
                >
                    <div
                        v-if="qrData.qr_code"
                        v-html="qrData.qr_code"
                        class="w-44 h-44 flex items-center justify-center my-1 bg-white p-2 rounded-lg shadow-2xs"
                    ></div>
                    <div
                        v-else
                        class="text-xs text-muted-foreground py-8 flex flex-col items-center gap-1"
                    >
                        <QrCodeIcon class="w-8 h-8 opacity-40" />
                        No SVG element found in payload.
                    </div>

                    <div
                        class="font-mono text-sm font-bold tracking-wider text-foreground mt-3"
                    >
                        {{ member?.membership_no }}
                    </div>
                    <div
                        class="text-[10px] font-medium text-muted-foreground uppercase tracking-widest mt-0.5"
                    >
                        Front Desk Scanner
                    </div>
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-col gap-2.5 mt-2">
                <Button
                    @click="emit('print')"
                    :disabled="!qrData || isLoading"
                    class="w-full gap-2 font-semibold"
                >
                    <Printer class="w-4 h-4" />
                    Print Badge
                </Button>

                <Button
                    variant="outline"
                    @click="emit('email')"
                    :disabled="isEmailing"
                    class="w-full gap-2"
                >
                    <Mail class="w-4 h-4" />
                    {{ isEmailing ? "Sending..." : "Email QR Code" }}
                </Button>

                <Button
                    variant="ghost"
                    @click="emit('regenerate')"
                    :disabled="isLoading"
                    class="w-full gap-2 text-destructive hover:text-destructive hover:bg-destructive/10 text-xs"
                >
                    <RefreshCw class="w-3.5 h-3.5" />
                    Regenerate Token
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
<script setup>
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from "@/components/ui/dialog";
import { Button } from "@/components/ui/button";
import {
    Printer,
    Mail,
    RefreshCw,
    QrCode as QrCodeIcon,
} from "lucide-vue-next";

// Define Props for JavaScript
defineProps({
    modelValue: {
        type: Boolean,
        required: true,
    },
    member: {
        type: Object,
        default: null,
    },
    qrData: {
        type: Object,
        default: null,
    },
    isLoading: {
        type: Boolean,
        default: false,
    },
    isEmailing: {
        type: Boolean,
        default: false,
    },
});

// Define Emits
const emit = defineEmits(["update:modelValue", "print", "email", "regenerate"]);
</script>
