<template>
    <div class="max-w-6xl mx-auto p-6">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
            <p class="text-gray-600">Manage system users and access levels.</p>
        </header>

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
                            <div class="space-y-4">
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
                                 <p v-if="showWarning" style="color: red;">
                                    password do not match!
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
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                >
                                    <button
                                        @click="editUser(user)"
                                        class="text-indigo-600 hover:text-indigo-900 mr-4 transition"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        @click="confirmDelete(user.id)"
                                        class="text-red-600 hover:text-red-900 transition"
                                    >
                                        Delete
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
    </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useUserStore } from "@/stores/userStore";
import { onMounted } from "vue";

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

const confirmDelete = async (id) => {
    if (confirm("Are you sure you want to delete this user?")) {
        await userStore.deleteUser(id);
    }
};

const resetForm = () => {
    isEditing.value = false;
    currentUserId.value = null;
    form.value = { ...initialState };
};

//check matching password
const passwordMatch = computed(() =>{
    return form.value.password === form.value.password_confirmation
});

const showWarning = computed(() => {
  return form.value.password_confirmation.length > 0 && !passwordMatch.value;
});

onMounted(() => {
    userStore.fetchUsers();
});
</script>
