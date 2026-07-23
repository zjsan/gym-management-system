<template>
    <div class="p-6 max-w-7xl mx-auto space-y-6">
        <!-- Header with Back Button -->
        <div class="flex items-center justify-between border-b pb-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Gym Attendance Dashboard
                </h1>
                <p class="text-sm text-gray-500">
                    Scan member QR codes or record manual check-ins.
                </p>
            </div>
            <router-link
                :to="{ name: 'members.index' }"
                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition border shadow-sm flex items-center gap-2"
            >
                &larr; Back to Member Management
            </router-link>
        </div>

        <!-- Alert Notifications -->
        <div
            v-if="attendanceStore.errorMessage"
            class="p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-lg flex items-center justify-between"
        >
            <span>{{ attendanceStore.errorMessage }}</span>
            <button
                @click="attendanceStore.clearStatus()"
                class="font-bold text-rose-900"
            >
                &times;
            </button>
        </div>

        <div
            v-if="attendanceStore.successData"
            class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg flex items-center gap-4"
        >
            <img
                v-if="attendanceStore.successData.photo_path"
                :src="`/storage/${attendanceStore.successData.photo_path}`"
                class="w-12 h-12 rounded-full object-cover"
            />
            <div>
                <p class="font-bold">
                    {{ attendanceStore.successData.message }}
                </p>
                <p class="text-sm">{{ attendanceStore.successData.name }}</p>
            </div>
        </div>

        <!-- Main Split-Pane Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- LEFT PANE: Input Controls -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Webcam Scanner Card -->
                <div class="bg-white p-4 rounded-xl border shadow-sm">
                    <div class="flex justify-between items-center mb-3">
                        <h2
                            class="text-sm font-semibold text-gray-700 uppercase tracking-wider"
                        >
                            Webcam Scanner
                        </h2>
                        <button
                            @click="toggleScanner"
                            class="text-xs px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded border font-medium"
                        >
                            {{ isScanning ? "Stop Camera" : "Start Camera" }}
                        </button>
                    </div>

                    <div
                        class="relative w-full bg-gray-900 rounded-lg overflow-hidden min-h-[220px] flex items-center justify-center"
                    >
                        <!-- QR Mount Element (Kept untouched by Vue conditional rendering) -->
                        <div id="qr-reader" class="w-full"></div>

                        <div
                            v-if="!isScanning"
                            class="absolute inset-0 flex items-center justify-center bg-gray-900 text-gray-400 text-xs p-4 text-center"
                        >
                            <span>{{
                                cameraError ||
                                'Camera inactive. Click "Start Camera" to scan.'
                            }}</span>
                        </div>
                    </div>
                </div>

                <!-- Manual Entry Tabs -->
                <div class="bg-white p-5 rounded-xl border shadow-sm">
                    <div class="flex border-b mb-4">
                        <button
                            type="button"
                            @click="activeTab = 'member'"
                            :class="
                                activeTab === 'member'
                                    ? 'border-b-2 border-indigo-600 text-indigo-600 font-bold'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="flex-1 py-2 text-center text-sm transition-colors"
                        >
                            Member Search
                        </button>
                        <button
                            type="button"
                            @click="activeTab = 'walkin'"
                            :class="
                                activeTab === 'walkin'
                                    ? 'border-b-2 border-indigo-600 text-indigo-600 font-bold'
                                    : 'text-gray-500 hover:text-gray-700'
                            "
                            class="flex-1 py-2 text-center text-sm transition-colors"
                        >
                            Walk-in Guest
                        </button>
                    </div>

                    <!-- TAB 1: Member Lookup Form -->
                    <div v-show="activeTab === 'member'" class="space-y-4">
                        <div class="relative">
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1"
                                >Search Name or ID</label
                            >
                            <input
                                v-model="searchQuery"
                                @input="handleInstantSearch"
                                type="text"
                                placeholder="Type member name or ID..."
                                class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                            />

                            <!-- Instant Autocomplete Dropdown -->
                            <div
                                v-if="attendanceStore.searchResults.length > 0"
                                class="absolute z-20 w-full mt-1 bg-white border rounded-lg shadow-xl max-h-48 overflow-y-auto"
                            >
                                <div
                                    v-for="member in attendanceStore.searchResults"
                                    :key="member.id"
                                    @click="selectMember(member)"
                                    class="p-2.5 hover:bg-indigo-50 cursor-pointer flex items-center justify-between border-b last:border-b-0"
                                >
                                    <div>
                                        <p
                                            class="text-sm font-semibold text-gray-800"
                                        >
                                            {{ member.first_name }}
                                            {{ member.last_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ member.membership_no }}
                                        </p>
                                    </div>
                                    <span
                                        :class="
                                            member.is_active
                                                ? 'bg-emerald-100 text-emerald-800'
                                                : 'bg-rose-100 text-rose-800'
                                        "
                                        class="text-[10px] px-2 py-0.5 rounded font-bold"
                                    >
                                        {{
                                            member.is_active
                                                ? "ACTIVE"
                                                : "EXPIRED"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="submitManualMember"
                            :disabled="
                                !selectedMemberNo || attendanceStore.isLoading
                            "
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg text-sm transition disabled:opacity-50"
                        >
                            Check-In Member
                        </button>
                    </div>

                    <!-- TAB 2: Walk-In Form -->
                    <div v-show="activeTab === 'walkin'" class="space-y-4">
                        <div>
                            <label
                                class="block text-xs font-semibold text-gray-600 mb-1"
                                >Guest Full Name</label
                            >
                            <input
                                v-model="walkinName"
                                type="text"
                                placeholder="Enter guest name..."
                                class="w-full border rounded-lg p-2.5 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                            />
                        </div>
                        <button
                            type="button"
                            @click="submitWalkin"
                            :disabled="
                                !walkinName.trim() || attendanceStore.isLoading
                            "
                            class="w-full bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 rounded-lg text-sm transition disabled:opacity-50"
                        >
                            Log Walk-In (₱100)
                        </button>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANE: Live Check-In Feed -->
            <div class="lg:col-span-7">
                <div class="bg-white p-5 rounded-xl border shadow-sm">
                    <h2
                        class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4"
                    >
                        Today's Check-Ins
                    </h2>

                    <div
                        v-if="
                            attendanceStore.isLoading &&
                            attendanceStore.liveFeed.length === 0
                        "
                        class="text-center py-8 text-gray-400 text-sm"
                    >
                        Loading attendance logs...
                    </div>

                    <div
                        v-else-if="attendanceStore.liveFeed.length === 0"
                        class="text-center py-8 text-gray-400 text-sm"
                    >
                        No check-ins recorded today.
                    </div>

                    <div
                        v-else
                        class="space-y-3 max-h-[550px] overflow-y-auto pr-1"
                    >
                        <div
                            v-for="log in attendanceStore.liveFeed"
                            :key="log.id"
                            class="p-3.5 border rounded-lg flex items-center justify-between hover:bg-gray-50 transition"
                        >
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm border border-indigo-100"
                                >
                                    {{
                                        log.member
                                            ? log.member.first_name[0]
                                            : log.walkin.name[0]
                                    }}
                                </div>
                                <div>
                                    <p
                                        class="text-sm font-semibold text-gray-800"
                                    >
                                        {{
                                            log.member
                                                ? `${log.member.first_name} ${log.member.last_name}`
                                                : log.walkin.name
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{
                                            log.member
                                                ? log.member.membership_no
                                                : "Walk-In Guest"
                                        }}
                                        •
                                        <span class="capitalize">{{
                                            log.entry_method
                                                ? log.entry_method.replace(
                                                      "_",
                                                      " ",
                                                  )
                                                : "Manual"
                                        }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="text-xs font-mono text-gray-600">{{
                                    formatTime(log.check_in)
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Html5Qrcode } from "html5-qrcode";
import { useAttendanceStore } from "@/stores/attendance";
import { useRouter } from "vue-router";

const router = useRouter();

const goBack = () => {
    router.push({ name: "Dashboard" });
};

const attendanceStore = useAttendanceStore();

const activeTab = ref("member");
const searchQuery = ref("");
const selectedMemberNo = ref("");
const walkinName = ref("");
const isScanning = ref(false);
const cameraError = ref(null);
let html5QrCode = null;
let debounceTimeout = null;

onMounted(() => {
    attendanceStore.fetchLiveFeed();
});

onBeforeUnmount(() => {
    stopScanner();
});

// --- QR SCANNER LOGIC ---
const startScanner = async () => {
    cameraError.value = null;

    try {
        // Check if camera devices exist first
        const devices = await Html5Qrcode.getCameras();
        if (!devices || devices.length === 0) {
            cameraError.value = "No camera devices detected on this system.";
            return;
        }

        await nextTick(); // Ensure DOM element is fully rendered

        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("qr-reader");
        }

        isScanning.value = true;

        await html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 200, height: 200 } },
            onScanSuccess,
        );
    } catch (err) {
        console.warn("Camera access failed:", err);
        isScanning.value = false;
        cameraError.value =
            "Unable to start camera. Hardware missing or access blocked.";

        // Clean up instance if it failed
        if (html5QrCode) {
            try {
                await html5QrCode.clear();
            } catch (e) {}
            html5QrCode = null;
        }
    }
};

const stopScanner = async () => {
    if (html5QrCode && isScanning.value) {
        try {
            await html5QrCode.stop();
            await html5QrCode.clear();
        } catch (e) {
            console.error("Scanner stop failed:", e);
        } finally {
            html5QrCode = null;
            isScanning.value = false;
        }
    }
};

const toggleScanner = () => {
    if (isScanning.value) {
        stopScanner();
    } else {
        startScanner();
    }
};

const onScanSuccess = async (decodedText) => {
    // When a QR is scanned, submit directly as 'qr_scan'
    await attendanceStore.submitCheckIn({
        entry_method: "qr_scan",
        membership_no: decodedText,
    });
};

// --- MANUAL MEMBER SEARCH ---
const handleSearch = () => {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        attendanceStore.searchMembers(searchQuery.value);
    }, 300);
};

const selectMember = (member) => {
    searchQuery.value = `${member.first_name} ${member.last_name} (${member.membership_no})`;
    selectedMemberNo.value = member.membership_no;
    attendanceStore.searchResults = [];
};

const submitManualMember = async () => {
    if (!selectedMemberNo.value) return;

    const res = await attendanceStore.submitCheckIn({
        entry_method: "manual_member",
        membership_no: selectedMemberNo.value,
    });

    if (res.success) {
        searchQuery.value = "";
        selectedMemberNo.value = "";
    }
};

// --- WALKIN SUBMISSION ---
const submitWalkin = async () => {
    const res = await attendanceStore.submitCheckIn({
        entry_method: "manual_walkin",
        walkin_name: walkinName.value,
    });
    if (res.success) {
        walkinName.value = "";
    }
};

// Helper format function
const formatTime = (dateStr) => {
    return new Date(dateStr).toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
    });
};
</script>
