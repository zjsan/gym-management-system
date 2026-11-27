<template>
    <Dialog :open="open" @update:open="handleOpenChange">
        <DialogContent
            class="w-[calc(100vw-1.5rem)] sm:max-w-[540px] max-h-[90dvh] flex flex-col bg-white border border-zinc-200/90 rounded-xl p-0 shadow-xl overflow-hidden"
        >
            <!-- Pinned Modal Header -->
            <DialogHeader
                class="px-5 py-4 border-b border-zinc-100 text-left shrink-0"
            >
                <div class="flex items-center gap-3 pr-6">
                    <div
                        :class="[
                            'w-9 h-9 rounded-lg flex items-center justify-center shrink-0',
                            isEditing
                                ? 'bg-amber-50 text-amber-700 border border-amber-200/70'
                                : 'bg-[#F8313B]/10 text-[#F8313B]',
                        ]"
                    >
                        <UserCog v-if="isEditing" class="w-4 h-4" />
                        <UserPlus v-else class="w-4 h-4" />
                    </div>
                    <div class="min-w-0">
                        <DialogTitle
                            class="text-sm font-bold tracking-tight text-[#0F1115] truncate"
                        >
                            {{
                                isEditing
                                    ? "Edit Member Profile"
                                    : "Register New Member"
                            }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-[11px] sm:text-xs text-zinc-500 mt-0.5 line-clamp-1"
                        >
                            {{
                                isEditing
                                    ? "Update member details, contacts, or profile photo."
                                    : "Enroll a member and prepare their QR check-in pass."
                            }}
                        </DialogDescription>
                    </div>
                </div>
            </DialogHeader>

            <!-- Scrollable Form Body -->
            <form
                id="member-form-modal-form"
                @submit.prevent="handleSubmit"
                class="flex-1 overflow-y-auto px-5 py-4 space-y-4"
            >
                <!-- Name Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1">
                        <label
                            for="member-first-name"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            First Name
                        </label>
                        <Input
                            id="member-first-name"
                            v-model="localForm.first_name"
                            placeholder="First name"
                            required
                            class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                        <p
                            v-if="errors?.first_name?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.first_name[0] }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label
                            for="member-last-name"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Last Name
                        </label>
                        <Input
                            id="member-last-name"
                            v-model="localForm.last_name"
                            placeholder="Last name"
                            required
                            class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                        <p
                            v-if="errors?.last_name?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.last_name[0] }}
                        </p>
                    </div>
                </div>

                <!-- Contact Numbers Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1">
                        <label
                            for="member-contact"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Contact Number
                        </label>
                        <Input
                            id="member-contact"
                            v-model="localForm.contact_number"
                            type="tel"
                            placeholder="09XX XXX XXXX"
                            required
                            class="h-9 text-xs text-[#0F1115] tabular-nums border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                        <p
                            v-if="errors?.contact_number?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.contact_number[0] }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label
                            for="member-emergency-contact"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Emergency Contact
                        </label>
                        <Input
                            id="member-emergency-contact"
                            v-model="localForm.emergency_contact_number"
                            type="tel"
                            placeholder="09XX XXX XXXX"
                            required
                            class="h-9 text-xs text-[#0F1115] tabular-nums border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                        <p
                            v-if="errors?.emergency_contact_number?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.emergency_contact_number[0] }}
                        </p>
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label
                        for="member-email"
                        class="block text-[11px] font-semibold text-zinc-600"
                    >
                        Email Address
                    </label>
                    <Input
                        id="member-email"
                        v-model="localForm.email"
                        type="email"
                        placeholder="member@example.com"
                        required
                        class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                    />
                    <p
                        v-if="errors?.email?.length"
                        class="text-[11px] font-medium text-[#F8313B]"
                    >
                        {{ errors.email[0] }}
                    </p>
                </div>

                <!-- Address -->
                <div class="space-y-1">
                    <label
                        for="member-address"
                        class="block text-[11px] font-semibold text-zinc-600"
                    >
                        Residential Address
                    </label>
                    <Input
                        id="member-address"
                        v-model="localForm.address"
                        placeholder="Street, Barangay, City / Municipality"
                        required
                        class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                    />
                    <p
                        v-if="errors?.address?.length"
                        class="text-[11px] font-medium text-[#F8313B]"
                    >
                        {{ errors.address[0] }}
                    </p>
                </div>

                <!-- Birthdate & Gender Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div class="space-y-1">
                        <label
                            for="member-dob"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Date of Birth
                        </label>
                        <Input
                            id="member-dob"
                            type="date"
                            v-model="localForm.date_of_birth"
                            required
                            class="h-9 text-xs text-[#0F1115] border-zinc-200/90 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]"
                        />
                        <p
                            v-if="errors?.date_of_birth?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.date_of_birth[0] }}
                        </p>
                    </div>

                    <div class="space-y-1">
                        <label
                            for="member-gender"
                            class="block text-[11px] font-semibold text-zinc-600"
                        >
                            Gender
                        </label>
                        <select
                            id="member-gender"
                            v-model="localForm.gender"
                            required
                            class="w-full h-9 px-3 rounded-lg bg-white border border-zinc-200/90 text-xs text-[#0F1115] focus:outline-none focus:ring-2 focus:ring-[#F8313B]/20 focus:border-[#F8313B]"
                        >
                            <option disabled value="">Select gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <p
                            v-if="errors?.gender?.length"
                            class="text-[11px] font-medium text-[#F8313B]"
                        >
                            {{ errors.gender[0] }}
                        </p>
                    </div>
                </div>

                <!-- Photo Upload + Compact Preview -->
                <div class="space-y-1.5">
                    <label
                        for="member-photo"
                        class="block text-[11px] font-semibold text-zinc-600"
                    >
                        Profile Photo
                    </label>
                    <div
                        class="flex items-center gap-3 p-2.5 rounded-xl bg-zinc-50/70 border border-zinc-200/80"
                    >
                        <img
                            v-if="previewSrc"
                            :src="previewSrc"
                            alt="Member preview"
                            class="w-10 h-10 rounded-full object-cover border border-zinc-200 bg-white shrink-0"
                        />
                        <div
                            v-else
                            class="w-10 h-10 rounded-full bg-zinc-100 border border-zinc-200/80 text-zinc-400 flex items-center justify-center shrink-0"
                        >
                            <Camera class="w-4 h-4" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <input
                                id="member-photo"
                                type="file"
                                accept="image/*"
                                @change="onFileChange"
                                class="block w-full text-[11px] text-zinc-500 file:mr-2.5 file:py-1.5 file:px-2.5 file:rounded-lg file:border file:border-zinc-200 file:text-[11px] file:font-semibold file:bg-white file:text-[#0F1115] hover:file:bg-zinc-100 cursor-pointer"
                            />
                        </div>
                    </div>
                    <p
                        v-if="
                            errors?.photo?.length || errors?.photo_url?.length
                        "
                        class="text-[11px] font-medium text-[#F8313B]"
                    >
                        {{ (errors.photo || errors.photo_url)[0] }}
                    </p>
                </div>
            </form>

            <!-- Pinned Modal Footer -->
            <DialogFooter
                class="px-5 py-3.5 bg-zinc-50/60 border-t border-zinc-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 shrink-0"
            >
                <Button
                    type="button"
                    variant="outline"
                    :disabled="loading"
                    @click="handleOpenChange(false)"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg border-zinc-200 bg-white hover:bg-zinc-50 text-xs font-semibold text-zinc-700 cursor-pointer"
                >
                    Cancel
                </Button>
                <Button
                    type="submit"
                    form="member-form-modal-form"
                    :disabled="loading"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white text-xs font-bold shadow-sm shadow-[#F8313B]/20 transition-all cursor-pointer"
                >
                    {{
                        loading
                            ? "Saving..."
                            : isEditing
                              ? "Save Changes"
                              : "Complete Registration"
                    }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup>
import { ref, reactive, computed, watch, onBeforeUnmount } from "vue";
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { UserPlus, UserCog, Camera } from "lucide-vue-next";

const props = defineProps({
    open: {
        type: Boolean,
        required: true,
    },
    isEditing: {
        type: Boolean,
        default: false,
    },
    initialData: {
        type: Object,
        default: () => ({}),
    },
    loading: {
        type: Boolean,
        default: false,
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:open", "submit", "file-change"]);

const createEmptyForm = () => ({
    first_name: "",
    last_name: "",
    contact_number: "",
    emergency_contact_number: "",
    email: "",
    address: "",
    date_of_birth: "",
    gender: "",
    photo_url: "",
});

const localForm = reactive(createEmptyForm());
const objectPreviewUrl = ref("");

const revokeObjectPreview = () => {
    if (objectPreviewUrl.value) {
        URL.revokeObjectURL(objectPreviewUrl.value);
        objectPreviewUrl.value = "";
    }
};

const syncFormFromProps = () => {
    revokeObjectPreview();
    const empty = createEmptyForm();
    Object.assign(localForm, empty, props.initialData || {});
};

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            syncFormFromProps();
        } else {
            revokeObjectPreview();
        }
    },
    { immediate: true },
);

const previewSrc = computed(
    () => objectPreviewUrl.value || localForm.photo_url || "",
);

const onFileChange = (event) => {
    const file = event.target?.files?.[0] || null;
    revokeObjectPreview();
    if (file) {
        objectPreviewUrl.value = URL.createObjectURL(file);
    }
    emit("file-change", file, event);
};

const handleSubmit = () => {
    emit("submit", { ...localForm });
};

const handleOpenChange = (value) => {
    if (!value) {
        revokeObjectPreview();
    }
    emit("update:open", value);
};

onBeforeUnmount(() => {
    revokeObjectPreview();
});
</script>
