<template>
    <div class="p-6 max-w-7xl mx-auto">
        <h1 class="text-3xl font-extrabold mb-6 text-gray-800 tracking-tight">
            Gym Management Dashboard
        </h1>

        <div>
            <button
                @click="goToAttendance"
                class="bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition"
            >
                Click here for Attendance
            </button>
        </div>
        <div class="w-50 mt-5">
            <Input
                v-model="searchQuery"
                type="text"
                placeholder="Search member..."
                class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none bg-white transition-all text-slate-700 placeholder:text-slate-400/90 shadow-inner"
            />
        </div>
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
                        v-model="memberForm.email"
                        placeholder="Email"
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
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4 text-right">
                                    Actions / Adjustment Controls
                                </th>
                                <th class="p-4 text-right">QR Code</th>
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
                                            :src="member.photo_url"
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
                                        {{ member.membership_start }}
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
                                        {{ member.membership_end }}
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span
                                        :class="
                                            member.is_active
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                : 'bg-rose-50 text-rose-700 border-rose-200'
                                        "
                                        class="inline-block px-3 py-1 rounded-full text-xs font-bold border shadow-sm select-none"
                                    >
                                        {{
                                            member.is_active
                                                ? "ACTIVE"
                                                : "INACTIVE"
                                        }}
                                    </span>
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
                                <button
                                    @click="openQrModal(member)"
                                    class="text-indigo-600 hover:text-indigo-900 font-bold text-xs tracking-wide uppercase transition"
                                    title="View/Print Member QR Badge"
                                >
                                    QR BADGE
                                </button>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--pagination control-->
                <div
                    class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 bg-slate-50/40 border-t border-slate-200/80 gap-4"
                >
                    <div
                        class="text-xs text-slate-500 font-medium order-2 sm:order-1"
                    >
                        Showing
                        <span class="text-slate-800 font-semibold">{{
                            rangeStart
                        }}</span>
                        to
                        <span class="text-slate-800 font-semibold">{{
                            rangeEnd
                        }}</span>
                        of
                        <span class="text-slate-800 font-semibold">{{
                            totalItems
                        }}</span>
                        entries
                    </div>

                    <div
                        class="flex items-center gap-1.5 order-1 sm:order-2 w-full sm:w-auto justify-end"
                    >
                        <button
                            @click="prevPage"
                            :disabled="currentPage === 1 || memberStore.loading"
                            class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium shadow-sm transition-all hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-white disabled:cursor-not-allowed cursor-pointer select-none"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5 mr-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                            Prev
                        </button>

                        <div class="hidden md:flex items-center gap-1">
                            <button
                                v-if="visiblePages[0] > 1"
                                @click="goToPage(1)"
                                :disabled="allowedEmailsStore.loading"
                                class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50"
                            >
                                1
                            </button>

                            <span
                                v-if="visiblePages[0] > 2"
                                class="text-slate-400 text-xs px-1"
                                >...</span
                            >

                            <button
                                v-for="page in visiblePages"
                                :key="page"
                                @click="goToPage(page)"
                                :disabled="memberStore.loading"
                                :class="[
                                    'w-8 h-8 rounded-lg text-xs font-semibold border transition-all cursor-pointer select-none',
                                    currentPage === page
                                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-sm shadow-indigo-500/10'
                                        : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50',
                                ]"
                            >
                                {{ page }}
                            </button>

                            <span
                                v-if="
                                    visiblePages[visiblePages.length - 1] <
                                    lastPage - 1
                                "
                                class="text-slate-400 text-xs px-1"
                                >...</span
                            >

                            <button
                                v-if="
                                    visiblePages[visiblePages.length - 1] <
                                    lastPage
                                "
                                @click="goToPage(lastPage)"
                                :disabled="memberStore.loading"
                                class="w-8 h-8 rounded-lg text-xs font-semibold border bg-white text-slate-600 border-slate-200 hover:bg-slate-50 disabled:opacity-50"
                            >
                                {{ lastPage }}
                            </button>
                        </div>

                        <span
                            class="text-xs font-medium text-slate-500 md:hidden px-2"
                        >
                            Page {{ currentPage }} of {{ lastPage }}
                        </span>

                        <button
                            @click="nextPage"
                            :disabled="
                                currentPage === lastPage || memberStore.loading
                            "
                            class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-medium shadow-sm transition-all hover:bg-slate-50 disabled:opacity-40 disabled:hover:bg-white disabled:cursor-not-allowed cursor-pointer select-none"
                        >
                            Next
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-3.5 h-3.5 ml-1"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🪪 QR BADGE MODAL -->
    <div
        v-if="showQrModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4"
    >
        <div
            class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-sm w-full p-6 text-center relative animate-in fade-in zoom-in duration-150"
        >
            <!-- Close Button -->
            <button
                @click="closeQrModal"
                class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 font-bold text-sm p-1"
            >
                ✕
            </button>

            <h3 class="text-lg font-bold text-slate-800 mb-1">
                Member QR Badge
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                {{ selectedQrMember?.first_name }}
                {{ selectedQrMember?.last_name }} (#{{
                    selectedQrMember?.membership_no
                }})
            </p>

            <!-- Loading State -->
            <div
                v-if="memberStore.qrLoading"
                class="py-12 text-slate-400 text-xs italic"
            >
                Generating member badge...
            </div>

            <!-- Badge Display Area (printable target) -->
            <div
                v-else-if="memberStore.qrData"
                id="printable-qr-badge"
                class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-2 flex flex-col items-center"
            >
                <!-- Render SVG raw output from backend resource -->
                <div
                    v-if="memberStore.qrData.qr_code"
                    v-html="memberStore.qrData.qr_code"
                    class="w-48 h-48 flex items-center justify-center my-2"
                ></div>
                <div v-else class="text-xs text-slate-400 py-8">
                    No SVG element found in payload.
                </div>

                <div
                    class="font-mono text-xs font-bold text-slate-700 tracking-wider"
                >
                    {{ selectedQrMember?.membership_no }}
                </div>
                <div class="text-[10px] text-slate-400 uppercase mt-0.5">
                    Scan at Front Desk Scanner
                </div>
            </div>

            <!-- Action Controls -->
            <div class="flex flex-col gap-2 mt-4">
                <button
                    @click="printQrBadge"
                    :disabled="!memberStore.qrData || memberStore.qrLoading"
                    class="w-full py-2.5 px-4 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-sm transition disabled:opacity-50"
                >
                    🖨️ Print Badge
                </button>

                <button
                    @click="handleSendQrEmail"
                    :disabled="isEmailingQr"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium disabled:opacity-50"
                >
                    {{ isEmailingQr ? "Sending..." : "✉️ Email QR Code" }}
                </button>

                <button
                    @click="handleRegenerateQr"
                    :disabled="memberStore.qrLoading"
                    class="w-full py-2 px-4 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg border border-rose-200 transition disabled:opacity-50"
                >
                    🔄 Regenerate Token
                </button>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, onMounted, watch, onUnmounted } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import debounce from "lodash.debounce";
import { usePagination } from "@/composables/usePagination";
import { storeToRefs } from "pinia";
import { useRouter } from "vue-router";
import { Input } from "@/components/ui/input";

//navigation
const router = useRouter();

const goToAttendance = () => {
    router.push({ name: "attendance" });
};

// Local states
const memberStore = useMemberStore();
const isEditing = ref(false);
const currentMemberId = ref(null);
const searchQuery = ref("");

//  QR Badge Modal States
const showQrModal = ref(false);
const selectedQrMember = ref(null);

// Component-level feedback states
const successMessage = ref("");
const errorMessage = ref("");
const modalErrorMessage = ref("");

const isEmailingQr = ref(false);

// FIXED: Extracted 'members' instead of non-existent 'member' key
const { members, loading: isStoreLoading } = storeToRefs(memberStore);

// ---------------------------------------------------
// Search and Pagination Logic
// ---------------------------------------------------
const loadPage = async (pageNumber, searchKeyword = searchQuery.value) => {
    try {
        errorMessage.value = "";
        await memberStore.fetchMembers(
            pageNumber,
            memberStore.itemsPerPage,
            searchKeyword,
        );
    } catch (err) {
        errorMessage.value =
            err?.response?.data?.message ||
            err?.message ||
            "Failed to load registry.";
    }
};

// Custom pagination composable setup
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
} = usePagination(memberStore, loadPage);

const debouncedSearch = debounce((targetQuery) => {
    loadPage(1, targetQuery);
}, 500);

watch(searchQuery, (newVal, oldVal) => {
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
const phMobileRegex = /^(?:\+63|63|0)?9\d{9}$/;

const isContactNumberValid = computed(() => {
    return phMobileRegex.test(memberForm.value.contact_number || "");
});

const isEmergencyContactValid = computed(() => {
    return phMobileRegex.test(memberForm.value.emergency_contact_number || "");
});

const isContactInfoValid = computed(() => {
    return isContactNumberValid.value && isEmergencyContactValid.value;
});

// email format validation using standard email validation regex pattern
const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

//  Computed validation flag
const isEmailInvalid = computed(() => {
    // If the input is empty, let required handle it
    if (!memberForm.value.email) return false;

    // Returns true if the email fails the regex test
    return !emailRegex.test(memberForm.value.email);
});

// ---------------------------------------------------
// Form States
// ---------------------------------------------------
const initialState = {
    first_name: "",
    last_name: "",
    contact_number: "",
    emergency_contact_number: "",
    email: "",
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

    const rawBirthDate = member.date_of_birth
        ? member.date_of_birth.substring(0, 10)
        : "";

    memberForm.value = {
        first_name: member.first_name,
        last_name: member.last_name,
        contact_number: member.contact_number,
        emergency_contact_number: member.emergency_contact_number,
        email: member.email,
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
    if (!isContactInfoValid.value) {
        alert("Please provide valid Philippine mobile numbers.");
        return;
    }

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
            // Laravel-friendly spoofing fallback method for multipart PUT configurations
            data.append("_method", "PUT");
            result = await memberStore.updateMember(
                currentMemberId.value,
                data,
            );
        } else {
            result = await memberStore.addMember(data);
        }

        if (result && result.success) {
            successMessage.value = isEditing.value
                ? "Member updated successfully!"
                : "Member added successfully!";
            resetForm();
            loadPage(currentPage.value); // Hot-reload current dataset view
        } else {
            alert(
                result?.message ||
                    "Action failed. Double-check backend validation schemas.",
            );
        }
    } catch (error) {
        console.error("Form submission failed:", error);
        const errMsg =
            error.response?.data?.message ||
            "A network error occurred. Please try again later.";
        alert(errMsg);
    }
};

// ---------------------------------------------------
// Member Additional Operations
// ---------------------------------------------------

const handleRenew = async (id) => {
    if (confirm("Confirm membership extension by 30 days?")) {
        const res = await memberStore.renewMember(id);
        if (!res.success) {
            alert(res.message);
        }
    }
};

const handleDayAdjustment = async (id, days) => {
    const res = await memberStore.adjustMemberDays(id, days);
    if (!res.success) alert(res.message);
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

//  Open Modal & Fetch QR Code
const openQrModal = async (member) => {
    selectedQrMember.value = member;
    showQrModal.value = true;
    await memberStore.fetchMemberQrCode(member.id);
};

//  Close Modal & Clear State
const closeQrModal = () => {
    showQrModal.value = false;
    selectedQrMember.value = null;
    memberStore.clearQrData();
};

//  Regenerate QR Token Handler
const handleRegenerateQr = async () => {
    if (!selectedQrMember.value) return;

    const confirmed = confirm(
        "Regenerating this QR code will invalidate any existing printed pass. Proceed?",
    );
    if (!confirmed) return;

    const res = await memberStore.regenerateMemberQrToken(
        selectedQrMember.value.id,
    );
    if (!res.success) {
        alert(res.message);
    }
};

//  Print Badge Handler
const printQrBadge = () => {
    const badgeElement = document.getElementById("printable-qr-badge");
    if (!badgeElement) return;

    const printWindow = window.open("", "_blank", "width=600,height=600");
    printWindow.document.write(`
        <html>
            <head>
                <title>Print QR Badge - ${selectedQrMember.value?.first_name || "Member"}</title>
                <style>
                    body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
                    .badge { text-align: center; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
                    svg { width: 200px; height: 200px; }
                </style>
            </head>
            <body>
                <div class="badge">
                    <h2>${selectedQrMember.value?.first_name} ${selectedQrMember.value?.last_name}</h2>
                    ${badgeElement.innerHTML}
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
};

//QR code email sending function
const handleSendQrEmail = async () => {
    if (!selectedQrMember.value) return;

    isEmailingQr.value = true;
    const res = await memberStore.sendQrCodeEmail(selectedQrMember.value.id);
    isEmailingQr.value = false;

    if (res.success) {
        alert("QR Code email has been queued for sending.");
    } else {
        alert(res.message || "Failed to queue QR Code email.");
    }
};

// ---------------------------------------------------
// Mounting Guards
// ---------------------------------------------------
onMounted(() => {
    console.log("Component mounted, loading initial data.");
    loadPage(currentPage.value, searchQuery.value.trim() || "");
});

onUnmounted(() => {
    debouncedSearch.cancel();
    if (memberStore.currentAbortController) {
        memberStore.currentAbortController.abort();
    }
});
</script>
