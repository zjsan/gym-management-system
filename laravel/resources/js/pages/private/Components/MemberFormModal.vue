<template>
    <Dialog :open="open" @update:open="handleOpenChange">
        <DialogContent
            class="w-[calc(100vw-1.5rem)] sm:max-w-[560px] max-h-[90dvh] flex flex-col bg-white border border-zinc-200 rounded-xl p-0 shadow-xl overflow-hidden"
        >
            <!-- Pinned Modal Header -->
            <DialogHeader
                class="px-5 sm:px-6 py-4 border-b border-zinc-200/80 bg-white text-left shrink-0"
            >
                <div class="flex items-center gap-3 pr-7">
                    <div
                        class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 bg-zinc-100 text-zinc-700 border border-zinc-200/80"
                    >
                        <UserCog v-if="isEditing" class="w-4 h-4" />
                        <UserPlus v-else class="w-4 h-4" />
                    </div>
                    <div class="min-w-0">
                        <DialogTitle
                            class="text-[15px] font-semibold tracking-tight text-zinc-900 truncate"
                        >
                            {{
                                isEditing
                                    ? "Edit Member Profile"
                                    : "Register New Member"
                            }}
                        </DialogTitle>
                        <DialogDescription
                            class="text-xs text-zinc-500 mt-0.5 line-clamp-1"
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
                class="flex-1 overflow-y-auto px-5 sm:px-6 py-5 space-y-6"
            >
                <!-- Section 1: Identity & Photo -->
                <section class="space-y-4">
                    <h3
                        class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400"
                    >
                        Identity &amp; Photo
                    </h3>

                    <!-- Avatar Upload Row -->
                    <div
                        class="flex items-center justify-between gap-3 p-3 rounded-lg border border-zinc-200/90 bg-zinc-50/60"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <img
                                v-if="previewSrc"
                                :src="previewSrc"
                                alt="Member preview"
                                class="w-11 h-11 rounded-full object-cover border border-zinc-200 bg-white shrink-0"
                            />
                            <div
                                v-else
                                class="w-11 h-11 rounded-full bg-white border border-zinc-200 text-zinc-400 flex items-center justify-center shrink-0"
                            >
                                <Camera class="w-4 h-4" />
                            </div>
                            <div class="min-w-0">
                                <p
                                    class="text-xs font-medium text-zinc-900 truncate"
                                >
                                    {{
                                        previewSrc
                                            ? "Profile photo attached"
                                            : "Upload member photo"
                                    }}
                                </p>
                                <p class="text-[11px] text-zinc-500 mt-0.5">
                                    PNG, JPG, or WEBP for member profile &amp;
                                    pass
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 shrink-0">
                            <button
                                v-if="
                                    previewSrc &&
                                    typeof removePhoto === 'function'
                                "
                                type="button"
                                @click="removePhoto"
                                class="h-8 px-2.5 rounded-md text-xs font-medium text-zinc-600 hover:text-zinc-900 hover:bg-zinc-200/60 transition-colors cursor-pointer"
                            >
                                Remove
                            </button>
                            <label
                                for="member-photo"
                                class="inline-flex items-center justify-center h-8 px-3 rounded-md bg-white border border-zinc-200 hover:bg-zinc-50 hover:border-zinc-300 text-xs font-medium text-zinc-800 shadow-2xs transition-colors cursor-pointer"
                            >
                                {{ previewSrc ? "Replace" : "Browse" }}
                            </label>
                        </div>

                        <input
                            id="member-photo"
                            type="file"
                            accept="image/*"
                            @change="onFileChange"
                            class="sr-only"
                        />
                    </div>
                    <p
                        v-if="
                            errors?.photo?.length || errors?.photo_url?.length
                        "
                        class="text-[11px] font-medium text-red-600"
                    >
                        {{ (errors.photo || errors.photo_url)[0] }}
                    </p>

                    <!-- First & Last Name -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label
                                for="member-first-name"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                First Name
                            </label>
                            <Input
                                id="member-first-name"
                                v-model="localForm.first_name"
                                autocomplete="given-name"
                                placeholder="First name"
                                required
                                :aria-invalid="
                                    Boolean(errors?.first_name?.length)
                                "
                                :class="[
                                    'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                    errors?.first_name?.length
                                        ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            />
                            <p
                                v-if="errors?.first_name?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.first_name[0] }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <label
                                for="member-last-name"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                Last Name
                            </label>
                            <Input
                                id="member-last-name"
                                v-model="localForm.last_name"
                                autocomplete="family-name"
                                placeholder="Last name"
                                required
                                :aria-invalid="
                                    Boolean(errors?.last_name?.length)
                                "
                                :class="[
                                    'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                    errors?.last_name?.length
                                        ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            />
                            <p
                                v-if="errors?.last_name?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.last_name[0] }}
                            </p>
                        </div>
                    </div>

                    <!-- Birthdate & Gender -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label
                                for="member-dob"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                Date of Birth
                            </label>
                            <Input
                                id="member-dob"
                                type="date"
                                v-model="localForm.date_of_birth"
                                autocomplete="bday"
                                required
                                :aria-invalid="
                                    Boolean(errors?.date_of_birth?.length)
                                "
                                :class="[
                                    'h-9 bg-white text-xs text-zinc-900 tabular-nums focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                    errors?.date_of_birth?.length
                                        ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            />
                            <p
                                v-if="errors?.date_of_birth?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.date_of_birth[0] }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <label
                                for="member-gender"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                Gender
                            </label>
                            <select
                                id="member-gender"
                                v-model="localForm.gender"
                                required
                                :aria-invalid="Boolean(errors?.gender?.length)"
                                :class="[
                                    'w-full h-9 px-3 rounded-md bg-white border text-xs transition-colors focus:outline-none focus:ring-2 focus:ring-[#F8313B]/20 focus:border-[#F8313B]',
                                    !localForm.gender
                                        ? 'text-zinc-400'
                                        : 'text-zinc-900',
                                    errors?.gender?.length
                                        ? 'border-red-500 focus:border-red-500 focus:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            >
                                <option disabled value="">Select gender</option>
                                <option value="male" class="text-zinc-900">
                                    Male
                                </option>
                                <option value="female" class="text-zinc-900">
                                    Female
                                </option>
                                <option value="other" class="text-zinc-900">
                                    Other
                                </option>
                            </select>
                            <p
                                v-if="errors?.gender?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.gender[0] }}
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Contact & Address -->
                <section class="pt-5 border-t border-zinc-200/80 space-y-4">
                    <h3
                        class="text-[11px] font-semibold uppercase tracking-wider text-zinc-400"
                    >
                        Contact &amp; Address
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5">
                            <label
                                for="member-contact"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                Contact Number
                            </label>
                            <Input
                                id="member-contact"
                                v-model="localForm.contact_number"
                                type="tel"
                                autocomplete="tel"
                                placeholder="09XX XXX XXXX"
                                required
                                :aria-invalid="
                                    Boolean(errors?.contact_number?.length)
                                "
                                :class="[
                                    'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 tabular-nums focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                    errors?.contact_number?.length
                                        ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            />
                            <p
                                v-if="errors?.contact_number?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.contact_number[0] }}
                            </p>
                        </div>

                        <div class="space-y-1.5">
                            <label
                                for="member-emergency-contact"
                                class="block text-xs font-medium text-zinc-700"
                            >
                                Emergency Contact
                            </label>
                            <Input
                                id="member-emergency-contact"
                                v-model="localForm.emergency_contact_number"
                                type="tel"
                                placeholder="09XX XXX XXXX"
                                required
                                :aria-invalid="
                                    Boolean(
                                        errors?.emergency_contact_number
                                            ?.length,
                                    )
                                "
                                :class="[
                                    'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 tabular-nums focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                    errors?.emergency_contact_number?.length
                                        ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                        : 'border-zinc-200',
                                ]"
                            />
                            <p
                                v-if="errors?.emergency_contact_number?.length"
                                class="text-[11px] font-medium text-red-600"
                            >
                                {{ errors.emergency_contact_number[0] }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label
                            for="member-email"
                            class="block text-xs font-medium text-zinc-700"
                        >
                            Email Address
                        </label>
                        <Input
                            id="member-email"
                            v-model="localForm.email"
                            type="email"
                            autocomplete="email"
                            placeholder="member@example.com"
                            required
                            :aria-invalid="Boolean(errors?.email?.length)"
                            :class="[
                                'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                errors?.email?.length
                                    ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                    : 'border-zinc-200',
                            ]"
                        />
                        <p
                            v-if="errors?.email?.length"
                            class="text-[11px] font-medium text-red-600"
                        >
                            {{ errors.email[0] }}
                        </p>
                    </div>

                    <div class="space-y-1.5">
                        <label
                            for="member-address"
                            class="block text-xs font-medium text-zinc-700"
                        >
                            Residential Address
                        </label>
                        <Input
                            id="member-address"
                            v-model="localForm.address"
                            autocomplete="street-address"
                            placeholder="Street, Barangay, City / Municipality"
                            required
                            :aria-invalid="Boolean(errors?.address?.length)"
                            :class="[
                                'h-9 bg-white text-xs text-zinc-900 placeholder:text-zinc-400 focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 focus-visible:border-[#F8313B]',
                                errors?.address?.length
                                    ? 'border-red-500 focus-visible:border-red-500 focus-visible:ring-red-500/20'
                                    : 'border-zinc-200',
                            ]"
                        />
                        <p
                            v-if="errors?.address?.length"
                            class="text-[11px] font-medium text-red-600"
                        >
                            {{ errors.address[0] }}
                        </p>
                    </div>
                </section>
            </form>

            <!-- Pinned Modal Footer -->
            <DialogFooter
                class="px-5 sm:px-6 py-3.5 bg-zinc-50 border-t border-zinc-200/80 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 shrink-0"
            >
                <Button
                    type="button"
                    variant="outline"
                    :disabled="loading"
                    @click="handleOpenChange(false)"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg border-zinc-200 bg-white hover:bg-zinc-100/70 text-xs font-medium text-zinc-700 cursor-pointer"
                >
                    Cancel
                </Button>
                <Button
                    type="submit"
                    form="member-form-modal-form"
                    :disabled="loading"
                    class="w-full sm:w-auto h-9 px-4 rounded-lg bg-[#F8313B] hover:bg-[#df202a] active:scale-[0.99] text-white text-xs font-medium shadow-xs transition-all cursor-pointer"
                >
                    <Loader2
                        v-if="loading"
                        class="w-3.5 h-3.5 mr-1.5 animate-spin"
                    />
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
import { UserPlus, UserCog, Camera, Loader2 } from "lucide-vue-next";

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
