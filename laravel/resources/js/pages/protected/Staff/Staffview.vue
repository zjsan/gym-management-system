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
                                            @click="
                                                memberStore.toggleStatus(
                                                    member.id,
                                                )
                                            "
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

const handleFileUpload = (event) => {
    form.value.photo = event.target.files[0];
};

const submitForm = async () => {
    loading.value = true;

    // We must use FormData for file uploads
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
    if (form.value.photo) {
        data.append("photo", form.value.photo);
    }

    // Debugging: Log FormData entries
    for (let [key, value] of data.entries()) {
        console.log(`${key}:`, value);
    }

    const result = await memberStore.addMember(data);
    if (result.success) {
        // Reset form
        form.value = {
            first_name: "",
            last_name: "",
            contact_number: "",
            gender: "male",
            photo: null,
        };
    }
    loading.value = false;
};

// Helper function to format dates, returns "N/A" if invalid or missing
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return isNaN(date.getTime()) ? "N/A" : date.toLocaleDateString();
};

onMounted(() => memberStore.fetchMembers());
</script>
