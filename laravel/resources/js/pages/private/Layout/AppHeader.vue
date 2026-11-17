<template>
    <header
        class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between shrink-0 shadow-xs z-30"
    >
        <!-- Left: Mobile Menu Toggle & Dynamic Title -->
        <div class="flex items-center gap-4">
            <button
                @click="$emit('toggle-sidebar')"
                class="lg:hidden text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition"
            >
                <Menu class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-2">
                <h1
                    class="text-sm md:text-base font-extrabold text-gray-900 tracking-tight"
                >
                    {{ currentRouteTitle }}
                </h1>
            </div>
        </div>

        <!-- Right: Actions & User Menu -->
        <div class="flex items-center gap-3">
            <!-- Active Shift Badge -->
            <div
                class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200/60 rounded-lg"
            >
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                <span class="text-[11px] font-bold text-emerald-700"
                    >Shift Active</span
                >
            </div>

            <div class="h-5 w-px bg-gray-200 hidden sm:block"></div>

            <!-- Profile Button -->
            <router-link
                :to="{ name: 'UserProfile' }"
                class="flex items-center gap-2 text-xs font-semibold text-gray-700 hover:text-emerald-600 transition bg-gray-50 hover:bg-gray-100 py-2 px-3 rounded-xl border border-gray-200 shadow-2xs"
            >
                <User class="w-4 h-4 text-gray-500" />
                <span class="hidden sm:inline">Profile</span>
            </router-link>

            <!-- Logout Button -->
            <button
                @click="handleLogout"
                class="flex items-center gap-1.5 text-xs font-semibold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 py-2 px-3 rounded-xl transition border border-red-200/80 shadow-2xs"
            >
                <LogOut class="w-4 h-4" />
                <span class="hidden sm:inline">Logout</span>
            </button>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { Menu, User, LogOut } from "lucide-vue-next";

defineEmits(["toggle-sidebar"]);

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
            return "Attendance & QR Check-in";
        case "payment":
            return "Financial Ledger";
        case "Adminview":
            return "Administration Control";
        case "SettingsView":
            return "Gym Settings";
        case "UserProfile":
            return "User Profile & Security";
        default:
            return "Gym Portal";
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
