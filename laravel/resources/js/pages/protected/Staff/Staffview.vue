<template>
    <div class="container">
        <h1>Staff Dashboard</h1>
        <p>
            Welcome to the Staff dashboard! This page is protected and requires
            authentication and only for staff.
        </p>

        <div class="max-w-7xl mx-auto p-6">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        Staff Dashboard
                    </h1>
                    <p class="text-gray-600">
                        Gym Member Management & Check-ins
                    </p>
                </div>
                <button
                    @click="showAddModal = true"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                >
                    + Register New Member
                </button>
            </header>

            <!-- Simple Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-gray-100"
                >
                    <p class="text-sm text-gray-500 uppercase font-bold">
                        Total Members
                    </p>
                    <p class="text-3xl font-black">
                        {{ memberStore.members.length }}
                    </p>
                </div>
                <div
                    class="bg-white p-6 rounded-xl shadow-sm border border-gray-100"
                >
                    <p class="text-sm text-green-500 uppercase font-bold">
                        Active Now
                    </p>
                    <p class="text-3xl font-black text-green-600">
                        {{ activeCount }}
                    </p>
                </div>
            </div>

            <!-- Members Table -->
            <div
                class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden"
            >
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Member
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Status
                            </th>
                            <th
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                            >
                                Expiry
                            </th>
                            <th
                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="member in memberStore.members"
                            :key="member.id"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <img
                                            :src="
                                                member.photo_path
                                                    ? `/storage/${member.photo_path}`
                                                    : '/default-avatar.png'
                                            "
                                            class="h-10 w-10 rounded-full object-cover"
                                        />
                                    </div>
                                    <div class="ml-4">
                                        <div
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ member.first_name }}
                                            {{ member.last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ member.membership_no }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="
                                        member.is_active
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-red-100 text-red-800'
                                    "
                                    class="px-2 py-1 rounded-full text-xs font-semibold"
                                >
                                    {{
                                        member.is_active ? "Active" : "Inactive"
                                    }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ formatDate(member.membership_end) }}
                            </td>
                            <td
                                class="px-6 py-4 text-right text-sm font-medium"
                            >
                                <button
                                    @click="renewMember(member.id)"
                                    class="text-blue-600 hover:text-blue-900 mr-3"
                                >
                                    Renew
                                </button>
                                <button
                                    @click="toggleStatus(member.id)"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    Toggle Status
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script setup>
import { computed, ref } from "vue";
import { useMemberStore } from "@/stores/memberStore";
import { onMounted } from "vue";

const memberStore = useMemberStore();
const member = computed(() => memberStore.member);

const initialState = {
    first_name: "",
    last_name: "",
    contact_number: "",
    emergency_contact_number: "",
    address: "",
    role: "staff",
    password: "",
    password_confirmation: "",
};

const isEditing = ref(false);
const currentMemberId = ref(null);
</script>
