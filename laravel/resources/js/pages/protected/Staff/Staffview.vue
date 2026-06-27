<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-extrabold mb-6 text-gray-800 tracking-tight">
            Gym Management Dashboard
        </h1>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <div
                class="lg:col-span-1 bg-white p-5 rounded-xl shadow-sm border border-gray-100"
            >
                <h2
                    class="font-bold text-lg mb-4"
                    :class="isEditing ? 'text-amber-600' : 'text-indigo-600'"
                >
                    {{ isEditing ? "📝 Edit Profile" : "👤 Register Member" }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <input
                        v-model="memberForm.first_name"
                        placeholder="First Name"
                        class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                    />
                    <span
                        v-if="memberStore.errors?.first_name"
                        class="error-msg"
                    >
                        {{ memberStore.errors.first_name[0] }}
                    </span>
                    <input
                        v-model="memberForm.last_name"
                        placeholder="Last Name"
                        class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                    />
                    <span
                        v-if="memberStore.errors?.last_name"
                        class="error-msg"
                    >
                        {{ memberStore.errors.last_name[0] }}
                    </span>
                    <input
                        v-model="memberForm.contact_number"
                        placeholder="Contact Number"
                        class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                    />
                    <input
                        v-model="memberForm.emergency_contact_number"
                        placeholder="Emergency Contact"
                        class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                    />
                    <input
                        v-model="memberForm.address"
                        placeholder="Residential Address"
                        class="w-full border p-2.5 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500"
                        required
                    />

                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 mb-1"
                            >Date of Birth</label
                        >
                        <input
                            type="date"
                            v-model="memberForm.date_of_birth"
                            class="w-full border p-2.5 rounded-lg text-sm text-gray-700"
                            required
                        />
                        <span
                            v-if="memberStore.errors?.date_of_birth"
                            class="error-msg"
                        >
                            {{ memberStore.errors.date_of_birth[0] }}
                        </span>
                    </div>

                    <select
                        v-model="memberForm.gender"
                        class="w-full border p-2.5 rounded-lg text-sm text-gray-700"
                        required
                    >
                        <option disabled value="">-- Select Gender --</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>

                    <div>
                        <label
                            class="block text-xs font-semibold text-gray-500 mb-1"
                            >Member Photo Profile</label
                        >
                        <input
                            type="file"
                            @change="handleFileUpload"
                            accept="image/*"
                            class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                        />
                    </div>

                    <div class="flex flex-col space-y-2 pt-2">
                        <button
                            type="submit"
                            :disabled="memberStore.loading"
                            class="w-full py-2.5 px-4 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition disabled:opacity-50"
                        >
                            {{
                                memberStore.loading
                                    ? "Processing..."
                                    : isEditing
                                      ? "Update Profile"
                                      : "Save Registration"
                            }}
                        </button>
                        <button
                            v-if="isEditing"
                            type="button"
                            @click="resetForm"
                            class="w-full py-2 px-4 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition"
                        >
                            Cancel Modifications
                        </button>
                    </div>
                </form>
            </div>

            <div
                class="lg:col-span-3 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden"
            >
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead
                            class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100"
                        >
                            <tr>
                                <th class="p-4">Profile Info</th>
                                <th class="p-4">Name</th>
                                <th class="p-4 text-center">Age</th>
                                <th class="p-4">Membership Window</th>
                                <th class="p-4 text-center">Status Toggle</th>
                                <th class="p-4 text-right">
                                    Actions / Adjustment Controls
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr
                                v-if="
                                    memberStore.loading &&
                                    !memberStore.members.length
                                "
                            >
                                <td
                                    colspan="6"
                                    class="p-12 text-center text-gray-400 italic"
                                >
                                    Synchronizing database schedules...
                                </td>
                            </tr>
                            <tr v-else-if="memberStore.members.length === 0">
                                <td
                                    colspan="6"
                                    class="p-12 text-center text-gray-400"
                                >
                                    No active operational registries matched
                                    yet.
                                </td>
                            </tr>
                            <tr
                                v-for="member in memberStore.members"
                                :key="member.id"
                                class="hover:bg-gray-50/70 transition"
                            >
                                <td class="p-4">
                                    <div class="flex items-center space-x-3">
                                        <img
                                            :src="
                                                member.photo_path
                                                    ? `/storage/${member.photo_path}`
                                                    : '/placeholder.png'
                                            "
                                            class="w-11 h-11 rounded-full border border-gray-100 object-cover bg-gray-50"
                                        />
                                        <div>
                                            <div
                                                class="font-mono text-xs font-bold text-gray-700"
                                            >
                                                {{ member.membership_no }}
                                            </div>
                                            <div
                                                class="text-xs text-gray-400 capitalize"
                                            >
                                                {{ member.gender }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 font-semibold text-gray-800">
                                    {{ member.first_name }}
                                    {{ member.last_name }}
                                </td>
                                <td class="p-4 text-center text-gray-600">
                                    {{ member.age ?? "N/A" }}
                                </td>
                                <td class="p-4 text-xs space-y-0.5">
                                    <div class="text-gray-600 font-medium">
                                        <span class="text-gray-400"
                                            >Start:</span
                                        >
                                        {{
                                            formatDate(member.membership_start)
                                        }}
                                    </div>
                                    <div
                                        class="font-semibold"
                                        :class="
                                            isExpired(member.membership_end)
                                                ? 'text-rose-500'
                                                : 'text-emerald-600'
                                        "
                                    >
                                        <span class="text-gray-400 font-normal"
                                            >Ends:</span
                                        >
                                        {{ formatDate(member.membership_end) }}
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <button
                                        @click="handleToggleStatus(member.id)"
                                        :class="
                                            member.is_active
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                : 'bg-rose-50 text-rose-700 border-rose-200'
                                        "
                                        class="px-3 py-1 rounded-full text-xs font-bold border hover:opacity-80 transition shadow-sm"
                                    >
                                        {{
                                            member.is_active
                                                ? "ACTIVE"
                                                : "INACTIVE"
                                        }}
                                    </button>
                                </td>
                                <td class="p-4 text-right space-x-2">
                                    <button
                                        @click="editMember(member)"
                                        class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs tracking-wide"
                                    >
                                        EDIT
                                    </button>

                                    <button
                                        @click="handleRenew(member.id)"
                                        :disabled="!member.can_renew"
                                        :class="
                                            member.can_renew
                                                ? 'text-emerald-600 hover:text-emerald-800 font-bold'
                                                : 'text-gray-300 cursor-not-allowed font-medium'
                                        "
                                        class="text-xs tracking-wide uppercase transition"
                                    >
                                        RENEW
                                    </button>

                                    <span
                                        class="inline-flex items-center rounded-md bg-gray-50 border border-gray-200 px-1 py-0.5 shadow-sm"
                                    >
                                        <button
                                            @click="
                                                handleDayAdjustment(
                                                    member.id,
                                                    -1,
                                                )
                                            "
                                            class="px-1 text-gray-400 hover:text-rose-600 font-bold text-xs"
                                            title="Subtract 1 Day"
                                        >
                                            -1d
                                        </button>
                                        <span
                                            class="text-[10px] text-gray-300 px-0.5"
                                            >|</span
                                        >
                                        <button
                                            @click="
                                                handleDayAdjustment(
                                                    member.id,
                                                    1,
                                                )
                                            "
                                            class="px-1 text-gray-500 hover:text-emerald-600 font-bold text-xs"
                                            title="Add 1 Day for Closure Adjustments"
                                        >
                                            +1d
                                        </button>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import debounce from "lodash/debounce"; //for debouncing search input
import { usePagination } from "@/composables/usePagination";

//local states
const memberStore = useMemberStore();
const isEditing = ref(false);
const currentMemberId = ref(null);

// Component-level feedback states
const successMessage = ref("");
const errorMessage = ref("");
const modalErrorMessage = ref("");

//extract states from the store while maintaining reactivity
const { member } = storeToRefs(memberStore);

// ---------------------------------------------------
// Search and Pagination Logic
// ---------------------------------------------------
const loadPage = async (pageNumber, searchKeyword = searchQuery.value) => {
    try {
        errorMessage.value = ""; //clear any existing error messages before attempting to load new data
        await memberStore.fetchAllowedEmails(
            pageNumber,
            memberStore.itemsPerPage,
            searchKeyword,
        );
    } catch (err) {
        errorMessage.value = err?.message || err || "Failed to load registry.";
    }
};

// Custom pagination composable to manage pagination state and logic
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
} = usePagination(allowedEmailsStore, loadPage);

//debounced search function to limit API calls while typing in the search input
const debouncedSearch = debounce((targetQuery) => {
    loadPage(1, targetQuery);
}, 500);

//watch the searchQuery for changes and trigger the debounced search function
watch(searchQuery, (newVal, oldVal) => {
    //trim the search query to prevent unnecessary API calls on whitespace changes
    //fall back to empty string if newVal or oldVal is null or undefined to prevent errors
    const currentText = newVal?.trim() || "";
    const previousText = oldVal?.trim() || "";

    if (currentText === previousText) {
        return;
    }

    debouncedSearch(currentText);
});

// ---------------------------------------------------
// Mobile Number Validation
// ---------------------------------------------------

// Matches: 09171234567, +639171234567, 639171234567
const phMobileRegex = /^(?:\+63|63|0)?9\d{9}$/;

// Checks if the primary contact number is valid
const isContactNumberValid = computed(() => {
    return phMobileRegex.test(memberForm.value.contact_number || "");
});

// Checks if the emergency contact number is valid
const isEmergencyContactValid = computed(() => {
    return phMobileRegex.test(memberForm.value.emergency_contact_number || "");
});

// Overall form contact validity (Both must be valid to submit)
const isContactInfoValid = computed(() => {
    return isContactNumberValid.value && isEmergencyContactValid.value;
});

// ---------------------------------------------------
// Form States
// ---------------------------------------------------

const initialState = {
    first_name: "",
    last_name: "",
    contact_number: "",
    emergency_contact_number: "",
    address: "",
    date_of_birth: "",
    gender: "",
    photo: null,
};

const memberForm = ref({ ...initialState });

const resetForm = () => {
    isEditing.value = false;
    currentMemberId.value = null;
    memberForm.value = { ...initialState };
    memberStore.errors = null;
};

const editMember = (member) => {
    isEditing.value = true;
    currentMemberId.value = member.id;

    // Direct mapping to standard HTML date format parameters (YYYY-MM-DD)
    const rawBirthDate = member.date_of_birth
        ? member.date_of_birth.substring(0, 10)
        : "";

    memberForm.value = {
        first_name: member.first_name,
        last_name: member.last_name,
        contact_number: member.contact_number,
        emergency_contact_number: member.emergency_contact_number,
        address: member.address,
        date_of_birth: rawBirthDate,
        gender: member.gender,
        photo: null,
    };
};

// ---------------------------------------------------
// Photo Handling
// ---------------------------------------------------

const handleFileUpload = (event) => {
    if (event.target.files.length > 0) {
        memberForm.value.photo = event.target.files[0];
    }
};

// ---------------------------------------------------
// Form Submission
// ---------------------------------------------------

const submitForm = async () => {
    //block submission upon missing mobile number
    if (!isContactInfoValid.value) {
        alert("Please provide valid Philippine mobile numbers.");
        return;
    }

    //data initial preparation
    const data = new FormData();
    Object.keys(memberForm.value).forEach((key) => {
        if (key !== "photo" && memberForm.value[key] !== null) {
            data.append(key, memberForm.value[key]);
        }
    });

    if (memberForm.value.photo instanceof File) {
        data.append("photo", memberForm.value.photo);
    }

    try {
        let result;
        if (isEditing.value) {
            data.append("_method", "PUT");
            result = await memberStore.updateMember(
                currentMemberId.value,
                data,
            );
        } else {
            result = await memberStore.addMember(data);
        }

        if (result && result.success) {
            resetForm();
        } else {
            alert(
                result?.message ||
                    "Action failed. Double-check backend validation schemas.",
            );
        }
    } catch (error) {
        // catch unexpected infrastructure errors (Network drop, 500 error, etc.)
        console.error("Form submission failed:", error);

        // Extract a helpful message from the error object if your HTTP client (like Axios) provides it
        const errorMessage =
            error.response?.data?.message ||
            "A network error occurred. Please try again later.";
        alert(errorMessage);
    }
};

// ---------------------------------------------------
// Member Additional Operations
// ---------------------------------------------------

const handleToggleStatus = async (id) => {
    await memberStore.toggleStatus(id);
};

const handleRenew = async (id) => {
    if (confirm("Confirm membership extension by 30 days?")) {
        const res = await memberStore.renewMember(id);
        if (!res.success) {
            alert(res.message);
        }
    }
};

const handleDayAdjustment = async (id, days) => {
    await memberStore.adjustMemberDays(id, days);
};

const isExpired = (endDateString) => {
    if (!endDateString) return false;
    return new Date() > new Date(endDateString);
};

const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime())
        ? "N/A"
        : date.toLocaleDateString(undefined, {
              year: "numeric",
              month: "short",
              day: "numeric",
          });
};

onMounted(() => {
    memberStore.fetchMembers();
});
</script>
