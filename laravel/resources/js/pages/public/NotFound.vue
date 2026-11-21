<template>
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
        <div
            class="max-w-md w-full text-center bg-white p-8 rounded-2xl shadow-xl border border-gray-100"
        >
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-red-50 text-red-600 rounded-full text-2xl font-bold mb-4"
            >
                404
            </div>
            <h1 class="text-xl font-extrabold text-gray-900 mb-2">
                Page Not Found
            </h1>
            <p class="text-xs text-gray-500 mb-6">
                The page you are looking for doesn't exist, has been moved, or
                the link is broken.
            </p>

            <!-- Dynamic Action Link Based on Auth Status -->
            <router-link
                :to="
                    authStore.isAuthenticated
                        ? { name: 'Dashboard' }
                        : { name: 'Login' }
                "
                class="inline-block w-full py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition"
            >
                {{
                    authStore.isAuthenticated
                        ? "Return to Dashboard"
                        : "Return to Login"
                }}
            </router-link>
        </div>
    </div>
</template>

<script setup>
import { useAuthStore } from "@/stores/auth";

const authStore = useAuthStore();
</script>
