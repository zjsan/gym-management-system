<template>
    <header
        class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-sm z-10"
    >
        <!-- Page Context Title or Breadcrumb slot -->
        <div class="flex items-center gap-2">
            <h1 class="text-base font-bold text-gray-800 tracking-tight">
                {{ currentRouteTitle }}
            </h1>
        </div>

        <!-- Right Side Actions & Profile Dropdown -->
        <div class="flex items-center gap-4">
            <router-link
                :to="{ name: 'UserProfile' }"
                class="flex items-center gap-2 text-xs font-medium text-gray-700 hover:text-emerald-600 transition bg-gray-50 hover:bg-gray-100 py-1.5 px-3 rounded-lg border border-gray-200"
            >
                <svg
                    class="w-4 h-4 text-gray-500"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                    />
                </svg>
                Profile
            </router-link>

            <button
                @click="handleLogout"
                class="flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 py-1.5 px-3 rounded-lg transition border border-red-200"
            >
                <svg
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                    />
                </svg>
                Logout
            </button>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const authStore = useAuthStore();

const currentRouteTitle = computed(() => {
    const name = route.name;
    switch (name) {
        case "Dashboard":
            return "Dashboard Overview";
        case "MemberManagement":
            return "Member Directory & Management";
        case "attendance":
            return "Attendance & QR Check-in Scanner";
        case "payment":
            return "Financial Ledger & Payment History";
        case "Adminview":
            return "Administration Control Center";
        case "SettingsView":
            return "Gym Settings & Configuration";
        case "UserProfile":
            return "User Profile & Security";
        default:
            return "Gym Management Portal";
    }
});

const handleLogout = async () => {
    try {
        await authStore.logout();
    } catch (error) {
        console.error("Logout error:", error);
    }
};
</script>
