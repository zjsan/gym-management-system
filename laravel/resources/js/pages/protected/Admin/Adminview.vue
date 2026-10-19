<template>
    <div class="max-w-6xl mx-auto p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600">Manage system users and access levels.</p>
        </header>

        <button
            @click="goToSettingsPage"
            class="bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition"
        >
            Gym Settings
        </button>

        <button
            @click="goToDashboard"
            class="bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition"
        >
            Dashboard
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-1">
                <div
                    class="bg-white p-6 rounded-lg shadow-md border border-gray-200 sticky top-6"
                >
                    <h2 class="text-xl font-semibold mb-4 text-gray-700">
                        {{ isEditing ? "Edit User" : " Create New User" }}
                    </h2>

                    <!--form for adding and updating-->
                    <form @submit.prevent="handleSubmit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >First Name</label
                                >
                                <input
                                    v-model="form.first_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"
                                    required
                                />
                            </div>
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                    >Last Name</label
                                >
                                <input
                                    v-model="form.last_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 border p-2"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >Email Address</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                :class="{
                                    'border-red-500': userStore.errors?.email,
                                }"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2"
                                required
                            />
                            <p
                                v-if="userStore.errors?.email"
                                class="text-red-500 text-xs mt-1"
                            >
                                {{ userStore.errors.email[0] }}
                            </p>
                        </div>

                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700"
                                >System Role</label
                            >
                            <select
                                v-model="form.role"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm border p-2"
                            >
                                <option value="staff">Staff</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <p
                                v-if="isEditing"
                                class="text-xs text-gray-500 mb-2 italic"
                            >
                                Leave passwords blank to keep current password.
                            </p>
                            <div v-if="isEditing" class="space-y-4">
                                <input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Password"
                                    class="block w-full rounded-md border-gray-300 shadow-sm border p-2 text-sm"
                                    :required="!isEditing"
                                />
                                <input
                                    v-model="form.password_confirmation"
                                    type="password"
                                    placeholder="Confirm Password"
                                    class="block w-full rounded-md border-gray-300 shadow-sm border p-2 text-sm"
                                    :required="!isEditing"
                                />
                                <p v-if="showWarning" style="color: red">
                                    password do not match!
                                </p>

                                <!-- Server-side Laravel Error -->
                                <p
                                    v-if="userStore.errors?.password"
                                    class="text-red-500 text-xs mt-1"
                                >
                                    {{ userStore.errors.password[0] }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end space-x-3 pt-4"
                        >
                            <button
                                v-if="isEditing"
                                @click="resetForm"
                                type="button"
                                class="text-sm text-gray-600 hover:text-gray-800 underline"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="userStore.loading"
                                :class="
                                    isEditing
                                        ? 'bg-green-600 hover:bg-green-700'
                                        : 'bg-blue-600 hover:bg-blue-700'
                                "
                                class="text-white font-bold py-2 px-6 rounded-md transition duration-200 disabled:opacity-50"
                            >
                                {{
                                    userStore.loading
                                        ? "Processing..."
                                        : isEditing
                                          ? "Update User"
                                          : "Save User"
                                }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!--table for users viewing-->
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden"
                >
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    User
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Status
                                </th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="user in userStore.users"
                                :key="user.id"
                                :class="{
                                    'bg-blue-50': currentUserId === user.id,
                                }"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{ user.first_name }}
                                        {{ user.last_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ user.email }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="
                                            user.role?.slug === 'admin'
                                                ? 'bg-purple-100 text-purple-800'
                                                : 'bg-green-100 text-green-800'
                                        "
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    >
                                        {{ user.role?.slug }}
                                    </span>
                                </td>
                                <td>
                                    <!-- Status Badge -->
                                    <span
                                        :class="
                                            user.status === 'active'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-gray-100 text-gray-800'
                                        "
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                                    >
                                        {{ user.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <button
                                        @click="editUser(user)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4 transition"
                                    >
                                        Edit
                                    </button>
                                    <!-- Dynamic Status Toggle Button -->
                                    <button
                                        @click="handleToggleStatus(user.id)"
                                        :class="
                                            user.status === 'active'
                                                ? 'text-red-600 hover:text-red-900'
                                                : 'text-green-600 hover:text-green-900'
                                        "
                                        class="transition font-semibold"
                                    >
                                        {{
                                            user.status === "active"
                                                ? "Deactivate"
                                                : "Activate"
                                        }}
                                    </button>
                                </td>
                            </tr>
                            <tr
                                v-if="
                                    userStore.users.length === 0 &&
                                    !userStore.loading
                                "
                            >
                                <td
                                    colspan="3"
                                    class="px-6 py-10 text-center text-gray-500"
                                >
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div
                        v-if="userStore.loading"
                        class="p-6 text-center text-gray-500 animate-pulse"
                    >
                        Fetching user records...
                    </div>
                </div>
            </div>
        </div>

        <!-- Temporary Password Modal -->
        <div
            v-if="showTempPasswordModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4"
        >
            <div
                class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative"
            >
                <div class="text-center">
                    <div
                        class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4"
                    >
                        <svg
                            class="h-6 w-6 text-green-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            ></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">
                        User Created Successfully!
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        A temporary password has been automatically generated
                        for <strong>{{ createdUserName }}</strong
                        >. Please copy and hand this over securely. It will not
                        be shown again.
                    </p>
                </div>

                <!-- Password Display Box -->
                <div
                    class="mt-4 bg-gray-50 border border-gray-200 rounded-md p-3 flex items-center justify-between"
                >
                    <span
                        class="font-mono text-lg font-bold text-gray-800 tracking-wider"
                        >{{ generatedTempPassword }}</span
                    >
                    <button
                        @click="copyToClipboard"
                        class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700 transition-colors font-medium"
                    >
                        Copy
                    </button>
                </div>

                <div class="mt-6">
                    <button
                        @click="showTempPasswordModal = false"
                        class="w-full bg-gray-800 text-white py-2 rounded-md hover:bg-gray-900 transition-colors text-sm font-medium"
                    >
                        Done / Close
                    </button>
                </div>
            </div>
        </div>

        <button
            @click="logout"
            class="bg-red-500 text-white font-semibold rounded-lg shadow hover:bg-red-600 transition"
        >
            Logout
        </button>
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useUserStore } from "@/stores/userStore";
import { onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const userStore = useUserStore();
const initialState = {
    first_name: "",
    last_name: "",
    email: "",
    role: "staff",
    password: "",
    password_confirmation: "",
};

const isEditing = ref(false);
const currentUserId = ref(null);

// State for the temporary password modal
const showTempPasswordModal = ref(false);
const generatedTempPassword = ref("");
const createdUserName = ref("");

const editUser = (user) => {
    isEditing.value = true;
    currentUserId.value = user.id;

    // Fill the form with existing data
    form.value = {
        first_name: user.first_name,
        last_name: user.last_name,
        email: user.email,
        role: user.role?.slug,
        password: "", // Leave blank for security
        password_confirmation: "",
    };
};

const form = ref({ ...initialState });

const handleSubmit = async () => {
    if (!passwordMatch.value) {
        alert("Passwords do not match!");
        return;
    }

    try {
        let result;
        const wasEditing = isEditing.value;

        if (wasEditing) {
            result = await userStore.updateUser(
                currentUserId.value,
                form.value,
            );
        } else {
            result = await userStore.addUser(form.value);

            if (result.success) {
                // Capture the generated temp password returned by the backend
                generatedTempPassword.value = result.tempPassword;
                createdUserName.value = `${form.first_name} ${form.last_name}`;

                // Show the temporary password pop-up modal
                showTempPasswordModal.value = true;
            }
        }
        if (result && result.success) {
            alert(isEditing.value ? "User updated!" : "User added!");
            resetForm();
        }
    } catch (error) {
        console.error("Failed operation:", error);
        alert("Failed operation. Please check the console for details.");
    }
};

// Clipboard helper function
const copyToClipboard = () => {
    navigator.clipboard.writeText(generatedTempPassword.value);
    alert("Temporary password copied to clipboard!");
};

const handleToggleStatus = async (id) => {
    await userStore.toggleUserStatus(id);
};

const resetForm = () => {
    isEditing.value = false;
    currentUserId.value = null;
    form.value = { ...initialState };
};

//check matching password
const passwordMatch = computed(() => {
    return form.value.password === form.value.password_confirmation;
});

const showWarning = computed(() => {
    return form.value.password_confirmation.length > 0 && !passwordMatch.value;
});

onMounted(() => {
    userStore.fetchUsers();
});

const logout = async () => {
    try {
        console.log("Attempting to logout...");
        await auth.logout();
        console.log("Success! Redirecting...");
    } catch (error) {
        console.error("Logout failed:", error);
    }
};

const goToSettingsPage = () => {
    router.push({ name: "SettingsView" });
};

const goToDashboard = () => {
    router.push({ name: "DashboardView" });
};
</script>
