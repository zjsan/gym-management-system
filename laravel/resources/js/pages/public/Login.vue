<template>
    <div
        class="min-h-screen w-full flex items-center justify-center bg-[#0F1115] relative overflow-hidden px-4"
    >
        <!-- Subtle Background Glow -->
        <div
            class="absolute -top-32 -left-32 w-96 h-96 bg-[#F8313B]/10 rounded-full blur-3xl pointer-events-none"
        ></div>
        <div
            class="absolute -bottom-32 -right-32 w-96 h-96 bg-[#F8313B]/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <div class="w-full max-w-md relative z-10">
            <!-- Shadcn Card Container -->
            <Card
                class="bg-white text-slate-900 p-8 sm:p-10 rounded-xl shadow-2xl border border-slate-200"
            >
                <!-- Logo & Header Section -->
                <CardHeader
                    class="flex flex-col items-center text-center p-0 mb-6 space-y-3"
                >
                    <!-- Actual Logo Image Asset -->
                    <div
                        class="w-20 h-20 rounded-2xl bg-slate-50 flex items-center justify-center p-2 border border-slate-100 shadow-sm"
                    >
                        <img
                            src="@/assets/images/logo.png"
                            alt="IMAQ Gym Logo"
                            class="w-full h-full object-contain"
                        />
                    </div>

                    <CardTitle
                        class="text-xl sm:text-2xl font-black tracking-tight text-[#0F1115]"
                    >
                        IMAQ GYM MANAGEMENT SYSTEM
                    </CardTitle>
                    <CardDescription
                        class="text-xs font-semibold text-slate-500 uppercase tracking-wider"
                    >
                        Ilocos Norte Operations Portal
                    </CardDescription>
                </CardHeader>

                <!-- Form Content -->
                <CardContent class="p-0">
                    <form @submit.prevent="login" class="space-y-4">
                        <!-- Email Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="email"
                                class="text-xs font-bold uppercase tracking-wider text-slate-700"
                            >
                                Email
                            </Label>
                            <Input
                                id="email"
                                type="email"
                                v-model="email"
                                placeholder="johndoe@gmail.com"
                                class="w-full px-4 py-2.5 rounded-lg bg-white border-2 border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-0 transition-all duration-200"
                                required
                            />
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-1.5">
                            <Label
                                for="password"
                                class="text-xs font-bold uppercase tracking-wider text-slate-700"
                            >
                                Password
                            </Label>
                            <div class="relative">
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    v-model="password"
                                    placeholder="password"
                                    class="w-full px-4 py-2.5 pr-10 rounded-lg bg-white border-2 border-slate-200 text-slate-900 placeholder:text-slate-400 text-sm focus-visible:outline-none focus-visible:border-[#F8313B] focus-visible:ring-0 transition-all duration-200"
                                    required
                                />
                                <!-- Password Toggle Icon -->
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 focus:outline-none"
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
                            <!-- Forgot Password Link -->
                            <div class="flex justify-end pt-1">
                                <a
                                    href="#"
                                    @click.prevent="goToForgotPassword"
                                    class="text-xs font-semibold text-[#111111] hover:text-[#F8313B] transition"
                                >
                                    Forgot Password?
                                </a>
                            </div>
                        </div>

                        <!-- Error Alert Box -->
                        <transition name="fade">
                            <div
                                v-if="auth.error"
                                class="p-3 rounded-lg bg-red-50 border border-red-200 flex items-start gap-2.5 text-red-700 text-xs font-medium"
                            >
                                <svg
                                    class="w-4 h-4 text-[#F8313B] shrink-0 mt-0.5"
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

                        <!-- Shadcn Button with #F8313B Accent -->
                        <Button
                            type="submit"
                            :disabled="auth.loading"
                            class="w-full mt-4 py-3 bg-[#F8313B] hover:bg-[#d9252f] active:scale-[0.98] text-white font-bold rounded-lg shadow-md shadow-[#F8313B]/25 transition-all duration-150 flex items-center justify-center gap-2 text-sm disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                            <svg
                                v-if="auth.loading"
                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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

            <!-- Footer Meta -->
            <p class="text-center text-xs text-slate-500 mt-6">
                &copy; 2026 IMAQ Gym Management. All rights reserved.
            </p>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from "../../stores/auth";
import { ref } from "vue";
import { useRouter } from "vue-router";

// Import ShadcnVue components (adjust path if your components alias uses '@/components/ui')
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

const auth = useAuthStore();
const email = ref("");
const password = ref("");
const showPassword = ref(false);
const router = useRouter();

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
</style>
