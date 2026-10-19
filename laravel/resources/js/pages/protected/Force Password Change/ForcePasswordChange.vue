<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">
                    Password Update Required
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    For security reasons, you must change your temporary
                    password before continuing.
                </p>
            </div>

            <!-- General Error Alert -->
            <div
                v-if="errorMessage && typeof errorMessage === 'string'"
                class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm"
            >
                {{ errorMessage }}
            </div>

            <form @submit.prevent="handleSubmit" class="space-y-4">
                <!-- Current (Temporary) Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Temporary Password</label
                    >
                    <input
                        type="password"
                        v-model="form.current_password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter your temporary password"
                    />
                    <span
                        v-if="errors?.current_password"
                        class="text-xs text-red-500 mt-1"
                        >{{ errors.current_password[0] }}</span
                    >
                </div>

                <!-- New Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >New Password</label
                    >
                    <input
                        type="password"
                        v-model="form.password"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="At least 8 characters"
                    />
                    <span
                        v-if="errors?.password"
                        class="text-xs text-red-500 mt-1"
                        >{{ errors.password[0] }}</span
                    >
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Confirm New Password</label
                    >
                    <input
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Re-enter new password"
                    />
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="authStore.loading"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 font-medium transition-colors"
                >
                    <span v-if="authStore.loading">Updating Password...</span>
                    <span v-else>Update Password & Continue</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";

const authStore = useAuthStore();
const router = useRouter();

// Check if this is a forced reset or a voluntary profile update
const isForced = computed(() => route.name === "ForcePasswordChange");

const form = reactive({
    current_password: "",
    password: "",
    password_confirmation: "",
});

const errors = ref(null);
const errorMessage = ref(null);

const handleSubmit = async () => {
    errors.value = null;
    errorMessage.value = null;

    const result = await authStore.updatePassword(form);

    if (result.success) {
        if (isForced.value) {
            alert("Password updated successfully! Welcome to your dashboard.");
            router.replace({ name: "Dashboard" });
        } else {
            successMessage.value = "Password updated successfully!";

            // Clear form fields
            form.current_password = "";
            form.password = "";
            form.password_confirmation = "";
        }
    } else {
        if (typeof result.message === "object") {
            errors.value = result.message;
        } else {
            errorMessage.value = result.message;
        }
    }
};
</script>
