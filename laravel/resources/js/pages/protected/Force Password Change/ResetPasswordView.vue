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
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                    </div>

                    <CardTitle
                        class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#0F1115]"
                    >
                        Reset Your Password
                    </CardTitle>
                    <CardDescription
                        class="text-xs font-medium text-slate-500 tracking-wide leading-relaxed"
                    >
                        Please enter a secure new password for your account
                        below.
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
                                    &larr; Click here to log in with your new
                                    password
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
                                    : "Please fix the errors below."
                            }}</span>
                        </div>
                    </transition>

                    <!-- Form Inputs -->
                    <form
                        v-if="!successMessage"
                        @submit.prevent="handleResetPassword"
                        class="space-y-4"
                    >
                        <!-- New Password Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="password"
                                class="text-xs font-bold text-[#0F1115] tracking-wide"
                            >
                                New Password
                            </Label>
                            <div class="relative">
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    minlength="8"
                                    placeholder="At least 8 characters"
                                    class="h-11 w-full px-3.5 pr-10 rounded-md bg-white border border-slate-300 text-[#0F1115] placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 transition-all duration-150"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#0F1115] focus:outline-none rounded p-0.5 transition-colors"
                                >
                                    <svg
                                        v-if="!showPassword"
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="password_confirmation"
                                class="text-xs font-bold text-[#0F1115] tracking-wide"
                            >
                                Confirm New Password
                            </Label>
                            <div class="relative">
                                <Input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    :type="
                                        showConfirmPassword
                                            ? 'text'
                                            : 'password'
                                    "
                                    required
                                    placeholder="Re-enter new password"
                                    class="h-11 w-full px-3.5 pr-10 rounded-md bg-white border border-slate-300 text-[#0F1115] placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 transition-all duration-150"
                                />
                                <button
                                    type="button"
                                    @click="
                                        showConfirmPassword =
                                            !showConfirmPassword
                                    "
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#0F1115] focus:outline-none rounded p-0.5 transition-colors"
                                >
                                    <svg
                                        v-if="!showConfirmPassword"
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="w-4 h-4"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"
                                        />
                                    </svg>
                                </button>
                            </div>
                            <p
                                v-if="showWarning"
                                class="text-[#F8313B] text-xs font-medium mt-1"
                            >
                                Passwords do not match!
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <Button
                            type="submit"
                            :disabled="authStore.loading || !passwordMatch"
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
                                    ? "Resetting Password..."
                                    : "Reset Password"
                            }}</span>
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Reusable Auth Footer Component -->
            <AuthFooter />
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import AuthFooter from "@/components/ui/common/AuthFooter.vue";

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

const route = useRoute();
const authStore = useAuthStore();

const form = reactive({
    password: "",
    password_confirmation: "",
});

const successMessage = ref(null);
const errorMessage = ref(null);
const showPassword = ref(false);
const showConfirmPassword = ref(false);

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
