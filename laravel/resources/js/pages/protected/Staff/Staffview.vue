<template>
    <div class="container">
        <h1>Staff Dashboard</h1>
        <p>
            Welcome to the Staff dashboard! This page is protected and requires
            authentication and only for staff.
        </p>

        <div class="p-6 max-w-7xl mx-auto">
            <h1 class="text-2xl font-bold mb-6">Member Management System</h1>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Registration Form -->
                <div class="lg:col-span-1 bg-white p-4 rounded shadow border">
                    <h2 class="font-semibold mb-4 text-blue-600">
                        Register New Member
                    </h2>
                    <form @submit.prevent="submitForm" class="space-y-3">
                        <input
                            v-model="form.first_name"
                            placeholder="First Name"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />
                        <input
                            v-model="form.last_name"
                            placeholder="Last Name"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />
                        <input
                            v-model="form.contact_number"
                            placeholder="Contact #"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />
                        <input
                            v-model="form.emergency_contact_number"
                            placeholder="Emergency contact #"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />
                        <input
                            v-model="form.address"
                            placeholder="Address"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />
                        <input
                            v-model="form.date_of_birth"
                            placeholder="Date of Birth"
                            class="w-full border p-2 rounded text-sm"
                            required
                        />

                        <select
                            v-model="form.gender"
                            class="w-full border p-2 rounded text-sm"
                            required
                        >
                            <option disabled value="">--Select Gender--</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>

                        <div>
                            <label class="block text-xs text-gray-500"
                                >Member Photo</label
                            >
                            <input
                                type="file"
                                @change="handleFileUpload"
                                class="w-full text-xs cursor-pointer"
                                required
                            />
                        </div>

                        <button
                            :disabled="loading"
                            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
                        >
                            {{ loading ? "Saving..." : "Register Member" }}
                        </button>
                    </form>
                </div>

                <!-- Testing Table -->
                <div
                    class="lg:col-span-3 bg-white rounded shadow border overflow-hidden"
                >
                    <!-- Wrapper for scrolling -->
                    <div class="overflow-y-auto max-h-[500px]">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-3">ID / Photo</th>
                                    <th class="p-3">Name</th>
                                    <th class="p-3">Membership Period</th>
                                    <th class="p-3">Status</th>
                                    <th class="p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--show if loading-->
                                <tr v-if="memberStore.loading">
                                    <td
                                        colspan="5"
                                        class="p-10 text-center text-gray-500 italic"
                                    >
                                        Fetching members from database...
                                    </td>
                                </tr>

                                <!--show if no members found-->
                                <tr
                                    v-else-if="memberStore.members.length === 0"
                                >
                                    <td
                                        colspan="5"
                                        class="p-10 text-center text-gray-400"
                                    >
                                        No members registered yet. Use the form
                                        to add one.
                                    </td>
                                </tr>

                                <!--show member rows-->
                                <tr
                                    v-for="member in memberStore.members"
                                    :key="member.id"
                                    class="border-b hover:bg-gray-50"
                                >
                                    <td class="p-3">
                                        <div
                                            class="flex items-center space-x-3"
                                        >
                                            <img
                                                :src="
                                                    member.photo_path
                                                        ? `/storage/${member.photo_path}`
                                                        : '/placeholder.png'
                                                "
                                                class="w-10 h-10 rounded-full bg-gray-200 object-cover"
                                            />
                                            <span class="font-mono text-xs">{{
                                                member.membership_no
                                            }}</span>
                                            <span class="font-mono text-xs">{{
                                                member.date_of_birth
                                            }}</span>
                                            <span class="font-mono text-xs">{{
                                                member.gender
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3 font-medium">
                                        {{ member.first_name }}
                                        {{ member.last_name }}
                                    </td>
                                    <td class="p-3 text-xs">
                                        <div>
                                            Start:
                                            {{
                                                formatDate(
                                                    member.membership_start,
                                                )
                                            }}
                                        </div>
                                        <div class="text-red-500">
                                            End:
                                            {{
                                                formatDate(
                                                    member.membership_end,
                                                )
                                            }}
                                        </div>
                                    </td>
                                    <td class="p-3">
                                        <span
                                            :class="
                                                member.is_active
                                                    ? 'text-green-600'
                                                    : 'text-red-600'
                                            "
                                            class="font-bold"
                                        >
                                            {{
                                                member.is_active
                                                    ? "ACTIVE"
                                                    : "INACTIVE"
                                            }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <button
                                            @click="toggleStatus(member.id)"
                                            :disabled="memberStore.loading"
                                            class="text-blue-500 hover:underline"
                                        >
                                            Toggle
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import { onMounted } from "vue";

const memberStore = useMemberStore();
const loading = ref(false);

const form = ref({
    first_name: "",
    last_name: "",
    contact_number: "",
    gender: "",
    photo: null,
});

const initialFormState = {
    first_name: "",
    last_name: "",
    contact_number: "",
    gender: "",
    photo: null,
};

const isEditing = ref(false);
const currentMemberId = ref(null);

const resetForm = () => {
    isEditing.value = false;
    currentMemberId.value = null;
    form.value = { ...initialState };
};

const editMember = (member) => {
    isEditing.value = true;
    currentMemberId.value = member.id;

    form.value = {
        first_name: member.first_name,
        last_name: member.last_name,
        contact_number: member.contact_number,
        gender: member.gender,
        photo: null, // We won't pre-fill the photo input
    };
};

const handleFileUpload = (event) => {
    form.value.photo = event.target.files[0];
};

const form = ref({ ...initialState });

const submitForm = async () => {
    loading.value = true;
    let result;

    try {
        // 1. Prepare the FormData object (shared by both Create and Update)
        const data = new FormData();
        data.append("first_name", form.value.first_name);
        data.append("last_name", form.value.last_name);
        data.append("contact_number", form.value.contact_number);
        data.append(
            "emergency_contact_number",
            form.value.emergency_contact_number,
        );
        data.append("address", form.value.address);
        data.append("date_of_birth", form.value.date_of_birth);
        data.append("gender", form.value.gender);

        // Only append photo if a new file object is actually selected
        if (form.value.photo && form.value.photo instanceof File) {
            data.append("photo", form.value.photo);
        }

        // Debugging: Log FormData entries
        for (let [key, value] of data.entries()) {
            console.log(`${key}:`, value);
        }

        // 2. Branch logic based on Edit vs. Create mode
        if (isEditing.value) {
            // CRITICAL: Spoof the PUT method for Laravel to read the FormData/Files

            // Pass the member ID along with the payload to your store action
            result = await memberStore.updateMember(form.value.id, data);
        } else {
            // Standard Create operation
            result = await memberStore.addMember(data);
        }

        // 3. Handle the response
        if (result && result.success) {
            // Optional: If you use a modal or routing, you might want to redirect/close here
            resetForm();
            alert(
                isEditing.value
                    ? "Member updated successfully!"
                    : "Member added successfully!",
            );
        } else {
            // Handle validation errors or backend failures returned gracefully
            alert(result.message || "Failed to save member data.");
        }
    } catch (error) {
        console.error("Error submitting form:", error);
        alert("An error occurred while submitting the form");
    } finally {
        // Moving this to 'finally' ensures loading drops back to false
        // no matter if the try blocks succeed OR catch blocks fail.
        loading.value = false;
    }
};

// Helper function to format dates, returns "N/A" if invalid or missing
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? "N/A" : date.toLocaleDateString();
};

const toggleStatus = async (memberId) => {
    await memberStore.toggleStatus(memberId);
};

onMounted(() => memberStore.fetchMembers());
</script>
