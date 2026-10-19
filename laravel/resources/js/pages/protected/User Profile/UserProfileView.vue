<template>
    <div class="max-w-4xl mx-auto p-6">
        <header class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">User Profile</h1>
                <p class="text-gray-600">
                    Manage your account settings and credentials.
                </p>
            </div>
            <button
                @click="router.back()"
                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 transition-colors"
            >
                Back
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- User Info Card -->
            <div
                class="bg-white p-6 rounded-lg shadow-md border border-gray-200 md:col-span-1"
            >
                <div class="text-center">
                    <div
                        class="mx-auto h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold mb-4"
                    >
                        {{ userInitials }}
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ authStore.user?.first_name }}
                        {{ authStore.user?.last_name }}
                    </h3>
                    <p class="text-sm text-gray-500">
                        {{ authStore.user?.email }}
                    </p>
                    <span
                        class="mt-3 inline-block px-3 py-1 text-xs font-semibold rounded-full capitalize"
                        :class="
                            authStore.user?.role?.slug === 'admin'
                                ? 'bg-purple-100 text-purple-800'
                                : 'bg-green-100 text-green-800'
                        "
                    >
                        {{ authStore.user?.role?.slug }}
                    </span>
                </div>
            </div>

            <!-- Password Change Card (Reusing the form logic or embedding it) -->
            <div
                class="bg-white p-6 rounded-lg shadow-md border border-gray-200 md:col-span-2"
            >
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Security Settings
                </h3>

                <!-- Success / Error Banners -->
                <div
                    v-if="successMessage"
                    class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded text-sm"
                >
                    {{ successMessage }}
                </div>
                <div
                    v-if="errorMessage"
                    class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm"
                >
                    {{ errorMessage }}
                </div>

                <form @submit.prevent="handleUpdatePassword" class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Current Password</label
                        >
                        <input
                            type="password"
                            v-model="form.current_password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >New Password</label
                        >
                        <input
                            type="password"
                            v-model="form.password"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-gray-700 mb-1"
                            >Confirm New Password</label
                        >
                        <input
                            type="password"
                            v-model="form.password_confirmation"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500"
                        />
                    </div>
                    <button
                        type="submit"
                        :disabled="authStore.loading"
                        class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 font-medium transition-colors disabled:opacity-50"
                    >
                        {{
                            authStore.loading
                                ? "Updating..."
                                : "Update Password"
                        }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

const form = reactive({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const successMessage = ref(null);
const errorMessage = ref(null);

const userInitials = computed(() => {
    const first = authStore.user?.first_name?.[0] || "";
    const last = authStore.user?.last_name?.[0] || "";
    return `${first}${last}`.toUpperCase();
});

const handleUpdatePassword = async () => {
    successMessage.value = null;
    errorMessage.value = null;

    const result = await authStore.updatePassword(form);

    if (result.success) {
        successMessage.value = "Password updated successfully!";
        form.current_password = "";
        form.password = "";
        form.password_confirmation = "";
    } else {
        errorMessage.value =
            typeof result.message === "string"
                ? result.message
                : "Failed to update password.";
    }
};
</script>
