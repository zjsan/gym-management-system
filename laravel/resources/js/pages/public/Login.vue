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
                <!-- Logo & Header Section -->
                <CardHeader
                    class="flex flex-col items-center text-center p-0 mb-8 space-y-3"
                >
                    <div
                        class="w-20 h-20 flex items-center justify-center mb-1"
                    >
                        <img
                            :src="logoImg"
                            alt="IMAQ Gym Logo"
                            class="w-full h-full object-contain drop-shadow-sm"
                        />
                    </div>

                    <div class="space-y-1">
                        <CardTitle
                            class="text-xl sm:text-2xl font-extrabold tracking-tight text-[#F8313B]"
                        >
                            IMAQ GYM MANAGEMENT SYSTEM
                        </CardTitle>
                        <CardDescription
                            class="text-xs font-medium text-slate-500 tracking-wide"
                        >
                            Sign in to access your operations portal
                        </CardDescription>
                    </div>
                </CardHeader>

                <!-- Form Content -->
                <CardContent class="p-0">
                    <form @submit.prevent="login" class="space-y-5">
                        <!-- Email Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="email"
                                class="text-xs font-bold text-[#0F1115] tracking-wide"
                            >
                                Email
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="email"
                                placeholder="johndoe@gmail.com"
                                class="h-11 w-full px-3.5 rounded-md bg-white border border-slate-300 text-[#0F1115] placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 transition-all duration-150"
                                required
                            />
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="password"
                                class="text-xs font-bold text-[#0F1115] tracking-wide"
                            >
                                Password
                            </Label>
                            <div class="relative">
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="password"
                                    placeholder="password"
                                    class="h-11 w-full px-3.5 pr-10 rounded-md bg-white border border-slate-300 text-[#0F1115] placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 transition-all duration-150"
                                    required
                                />
                                <!-- Password Toggle Icon -->
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    :aria-label="
                                        showPassword
                                            ? 'Hide password'
                                            : 'Show password'
                                    "
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#0F1115] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B] rounded p-0.5 transition-colors"
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

                            <!-- Forgot Password Link aligned right below password field -->
                            <div class="flex justify-end pt-1">
                                <a
                                    href="#"
                                    @click.prevent="goToForgotPassword"
                                    class="text-xs font-semibold text-slate-600 hover:text-[#F8313B] transition-colors"
                                >
                                    Forgot Password?
                                </a>
                            </div>
                        </div>

                        <!-- Error Alert Box -->
                        <transition name="fade">
                            <div
                                v-if="auth.error"
                                role="alert"
                                class="p-3 rounded-md bg-red-50 border border-red-200/80 flex items-start gap-2.5 text-[#F8313B] text-xs font-medium"
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
                                <span>{{ auth.error }}</span>
                            </div>
                        </transition>

                        <!-- Primary Submit Button -->
                        <Button
                            type="submit"
                            :disabled="auth.loading"
                            class="w-full h-11 mt-2 bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white font-bold rounded-md shadow-sm shadow-[#F8313B]/20 transition-all duration-150 flex items-center justify-center gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="auth.loading"
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
                                auth.loading ? "Signing in..." : "Sign In"
                            }}</span>
                        </Button>
                    </form>
                </CardContent>
            </Card>

            <!-- Clean Footer Meta -->
            <div
                class="mt-8 pt-6 border-t border-slate-800/80 text-center space-y-1"
            >
                <p class="text-xs font-semibold text-slate-400 tracking-wide">
                    &copy; {{ currentYear }} {{ branding.appName }}
                </p>
                <p class="text-[11px] text-slate-500 font-medium">
                    Secure Operations Portal &bull;
                    <span class="text-slate-400">{{ branding.location }}</span>
                    <span class="text-[10px] text-slate-600">
                        &bull; v{{ branding.version }}</span
                    >
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from "../../stores/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";
import { branding } from "@/config/branding";

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
import logoImg from "@/assets/images/imaq-logo.png";

const auth = useAuthStore();
const email = ref("");
const password = ref("");
const showPassword = ref(false);
const router = useRouter();

// Dynamic year for the footer
const currentYear = ref(new Date().getFullYear());

const login = async () => {
    try {
        await auth.login({
            email: email.value,
            password: password.value,
        });
    } catch (error) {
        console.error("Login failed:", error);
    }
};

const goToForgotPassword = () => {
    router.push({ name: "ForgotPassword" });
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
