<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div
            class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md border border-gray-200"
        >
            <div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Reset Your Password
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Please enter your new password below.
                </p>
            </div>

            <!-- Success Message -->
            <div
                v-if="successMessage"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm"
            >
                {{ successMessage }}
                <div class="mt-2">
                    <router-link
                        to="/"
                        class="font-medium underline hover:text-green-800"
                    >
                        Click here to log in with your new password
                    </router-link>
                </div>
            </div>

            <!-- Error Message -->
            <div
                v-if="errorMessage"
                class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded text-sm"
            >
                {{
                    typeof errorMessage === "string"
                        ? errorMessage
                        : "Please fix the errors below."
                }}
            </div>

            <form
                v-if="!successMessage"
                @submit.prevent="handleResetPassword"
                class="mt-8 space-y-4"
            >
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >New Password</label
                    >
                    <input
                        v-model="form.password"
                        type="password"
                        required
                        minlength="8"
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="At least 8 characters"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Confirm New Password</label
                    >
                    <input
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Re-enter new password"
                    />
                    <p v-if="showWarning" class="text-red-500 text-xs mt-1">
                        Passwords do not match!
                    </p>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="authStore.loading || !passwordMatch"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition"
                    >
                        {{
                            authStore.loading
                                ? "Resetting Password..."
                                : "Reset Password"
                        }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const authStore = useAuthStore();

const form = reactive({
    password: "",
    password_confirmation: "",
});

const successMessage = ref(null);
const errorMessage = ref(null);

const token = route.query.token;
const email = route.query.email;

const passwordMatch = computed(() => {
    return form.password === form.password_confirmation;
});

const showWarning = computed(() => {
    return form.password_confirmation.length > 0 && !passwordMatch.value;
});

const handleResetPassword = async () => {
    if (!token || !email) {
        errorMessage.value =
            "Invalid or missing password reset token. Please request a new link.";
        return;
    }

    errorMessage.value = null;
    successMessage.value = null;

    const result = await authStore.resetPassword({
        token: token,
        email: email,
        password: form.password,
        password_confirmation: form.password_confirmation,
    });

    if (result.success) {
        successMessage.value = result.message;
    } else {
        errorMessage.value = result.message;
    }
};
</script>
