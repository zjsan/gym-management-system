<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8"
    >
        <div
            class="max-w-md w-full space-y-8 bg-white p-8 rounded-lg shadow-md border border-gray-200"
        >
            <div>
                <h2 class="text-center text-3xl font-extrabold text-gray-900">
                    Forgot Password?
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Enter your email address and we'll send you a link to reset
                    your password.
                </p>
            </div>

            <!-- Success Message -->
            <div
                v-if="successMessage"
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded text-sm space-y-2"
            >
                <p>{{ successMessage }}</p>
                <div class="pt-2">
                    <router-link
                        to="/login"
                        class="font-medium underline hover:text-green-800"
                    >
                        &larr; Back to Login
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
                        : "Failed to send reset link. Please try again."
                }}
            </div>

            <form
                v-if="!successMessage"
                @submit.prevent="handleSendLink"
                class="mt-8 space-y-4"
            >
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1"
                        >Email Address</label
                    >
                    <input
                        v-model="email"
                        type="email"
                        required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="admin@yourgym.ph"
                    />
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="authStore.loading"
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition"
                    >
                        {{
                            authStore.loading
                                ? "Sending Reset Link..."
                                : "Send Reset Link"
                        }}
                    </button>
                </div>

                <div class="text-center mt-4">
                    <router-link
                        to="/login"
                        class="text-sm font-medium text-blue-600 hover:text-blue-500"
                    >
                        Remember your password? Sign in
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/authStore";

const authStore = useAuthStore();

const email = ref("");
const successMessage = ref(null);
const errorMessage = ref(null);

const handleSendLink = async () => {
    successMessage.value = null;
    errorMessage.value = null;

    const result = await authStore.sendPasswordResetLink(email.value);

    if (result.success) {
        successMessage.value =
            "If an account with that email exists, a password reset link has been sent. Please check your inbox.";
        email.value = ""; // Clear input
    } else {
        errorMessage.value = result.message;
    }
};
</script>
