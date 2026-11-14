<template>
    <div
        class="min-h-screen w-full flex flex-col items-center justify-center bg-[#F8F9FA] px-4 py-12 relative selection:bg-[#F8313B]/15 selection:text-[#F8313B]"
    >
        <!-- Subtle Athletic Top Accent Line -->
        <div class="fixed top-0 left-0 right-0 h-1 bg-[#F8313B] z-50"></div>

        <!-- Subtle Architectural Grid Texture -->
        <div
            class="absolute inset-0 bg-[radial-gradient(#E2E8F0_1px,transparent_1px)] [background-size:20px_20px] opacity-60 pointer-events-none"
        ></div>

        <div class="w-full max-w-[420px] relative z-10 animate-fade-in-up">
            <!-- Shadcn Card Container -->
            <Card
                class="bg-white text-[#0F1115] p-8 sm:p-10 rounded-lg shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/90"
            >
                <!-- Header Section -->
                <CardHeader
                    class="flex flex-col items-center text-center p-0 mb-6 space-y-2"
                >
                    <!-- Optional Icon or Mini Logo Badge -->
                    <div
                        class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center mb-2 border border-red-100 text-[#F8313B]"
                    >
                        <svg
                            class="w-6 h-6"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                            />
                        </svg>
                    </div>

                    <CardTitle
                        class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F1115]"
                    >
                        Forgot Password?
                    </CardTitle>
                    <CardDescription
                        class="text-xs font-medium text-slate-500 tracking-wide leading-relaxed"
                    >
                        Enter your email address and we'll send you a secure
                        link to reset your password.
                    </CardDescription>
                </CardHeader>

                <!-- Content / Form Section -->
                <CardContent class="p-0">
                    <!-- Success Message Box -->
                    <transition name="fade">
                        <div
                            v-if="successMessage"
                            role="alert"
                            class="p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium space-y-3"
                        >
                            <div class="flex items-start gap-2.5">
                                <svg
                                    class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span>{{ successMessage }}</span>
                            </div>
                            <div class="pt-1">
                                <router-link
                                    to="/"
                                    class="inline-flex items-center gap-1 font-bold text-emerald-700 hover:text-emerald-900 transition-colors"
                                >
                                    &larr; Back to Sign In
                                </router-link>
                            </div>
                        </div>
                    </transition>

                    <!-- Error Message Box -->
                    <transition name="fade">
                        <div
                            v-if="errorMessage"
                            role="alert"
                            class="mb-4 p-3 rounded-md bg-red-50 border border-red-200/80 flex items-start gap-2.5 text-[#F8313B] text-xs font-medium"
                        >
                            <svg
                                class="w-4 h-4 shrink-0 mt-0.5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            <span>{{
                                typeof errorMessage === "string"
                                    ? errorMessage
                                    : "Failed to send reset link. Please try again."
                            }}</span>
                        </div>
                    </transition>

                    <!-- Form Inputs -->
                    <form
                        v-if="!successMessage"
                        @submit.prevent="handleSendLink"
                        class="space-y-4"
                    >
                        <div class="space-y-1.5">
                            <Label
                                for="email"
                                class="text-xs font-bold text-[#0F1115] tracking-wide"
                            >
                                Email Address
                            </Label>
                            <Input
                                id="email"
                                v-model="email"
                                type="email"
                                required
                                placeholder="admin@yourgym.ph"
                                class="h-11 w-full px-3.5 rounded-md bg-white border border-slate-300 text-[#0F1115] placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 transition-all duration-150"
                            />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            :disabled="authStore.loading"
                            class="w-full h-11 mt-2 bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white font-bold rounded-md shadow-sm shadow-[#F8313B]/20 transition-all duration-150 flex items-center justify-center gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="authStore.loading"
                                class="animate-spin -ml-1 mr-1.5 h-4 w-4 text-white"
                                fill="none"
                                viewBox="0 0 24 24"
                            >
                                <circle
                                    class="opacity-25"
                                    cx="12"
                                    cy="12"
                                    r="10"
                                    stroke="currentColor"
                                    stroke-width="4"
                                ></circle>
                                <path
                                    class="opacity-75"
                                    fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                ></path>
                            </svg>
                            <span>{{
                                authStore.loading
                                    ? "Sending Reset Link..."
                                    : "Send Reset Link"
                            }}</span>
                        </Button>

                        <!-- Back to Login Link -->
                        <div class="text-center pt-2">
                            <router-link
                                to="/"
                                class="text-xs font-semibold text-slate-600 hover:text-[#F8313B] transition-colors"
                            >
                                Remember your password? Sign in
                            </router-link>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Clean Footer Meta (Matching Login Page & Config) -->
            <div
                class="mt-8 pt-6 border-t border-slate-200/80 text-center space-y-1"
            >
                <p class="text-xs font-semibold text-slate-600 tracking-wide">
                    &copy; {{ currentYear }} {{ branding.appName }}
                </p>
                <p class="text-[11px] text-slate-500 font-medium">
                    Secure Operations Portal &bull;
                    <span class="text-slate-600">{{ branding.location }}</span>
                    <span class="text-[10px] text-slate-400">
                        &bull; v{{ branding.version }}</span
                    >
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding"; // Adjust path if needed

import {
    Card,
    CardHeader,
    CardTitle,
    CardDescription,
    CardContent,
} from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";

const authStore = useAuthStore();

const email = ref("");
const successMessage = ref(null);
const errorMessage = ref(null);
const currentYear = new Date().getFullYear();

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

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
