<template>
    <header
        class="h-16 bg-white/95 backdrop-blur-xs border-b border-slate-200/90 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 z-30"
    >
        <!-- Left: Mobile Drawer Trigger & SaaS Breadcrumb -->
        <div class="flex items-center gap-3">
            <button
                @click="$emit('toggle-sidebar')"
                class="lg:hidden p-2 -ml-1 rounded-xl text-slate-500 hover:text-[#0F1115] hover:bg-slate-100 transition"
                aria-label="Open navigation"
            >
                <Menu class="w-5 h-5" />
            </button>

            <nav class="flex items-center gap-2 text-xs">
                <span class="hidden sm:inline font-medium text-slate-400">
                    {{ branding.appName || "Portal" }}
                </span>
                <ChevronRight
                    class="hidden sm:inline w-3.5 h-3.5 text-slate-300"
                />
                <h1 class="font-bold text-sm text-[#0F1115] tracking-tight">
                    {{ currentRouteTitle }}
                </h1>
            </nav>
        </div>

        <!-- Right: Role Badge, Profile Link & Logout -->
        <div class="flex items-center gap-2.5 sm:gap-3">
            <!-- Role Tag -->
            <span
                class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-50 text-[#F8313B] border border-rose-200/60 capitalize"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-[#F8313B]"></span>
                {{ authStore.user?.role?.slug || "Staff" }}
            </span>

            <!-- User Profile Trigger -->
            <router-link
                :to="{ name: 'UserProfile' }"
                class="flex items-center gap-2.5 py-1.5 px-2.5 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200/80 transition"
            >
                <div class="text-right hidden sm:block">
                    <p
                        class="text-xs font-semibold text-[#0F1115] leading-none"
                    >
                        {{ authStore.user?.name || "User" }}
                    </p>
                    <p
                        class="text-[11px] text-slate-400 mt-1 leading-none max-w-[150px] truncate"
                    >
                        {{ authStore.user?.email || "" }}
                    </p>
                </div>

                <div
                    class="w-8 h-8 rounded-full bg-rose-50 border border-rose-200 flex items-center justify-center text-[#F8313B] shrink-0"
                >
                    <User class="w-4 h-4" />
                </div>
            </router-link>

            <div class="h-5 w-px bg-slate-200"></div>

            <!-- Clean Logout Action -->
            <button
                @click="handleLogout"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-slate-600 hover:text-[#F8313B] hover:bg-rose-50/80 border border-slate-200/80 hover:border-rose-200 transition"
            >
                <LogOut class="w-3.5 h-3.5" />
                <span class="hidden sm:inline">Logout</span>
            </button>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import { Menu, ChevronRight, User, LogOut } from "lucide-vue-next";

defineEmits(["toggle-sidebar"]);

const route = useRoute();
const authStore = useAuthStore();

const currentRouteTitle = computed(() => {
    switch (route.name) {
        case "Dashboard":
            return "Dashboard Overview";
        case "MemberManagement":
            return "Member Management";
        case "attendance":
            return "Attendance & QR Check-in";
        case "payment":
            return "Payment History";
        case "Adminview":
            return "Admin Control";
        case "SettingsView":
            return "Gym Settings";
        case "UserProfile":
            return "User Profile";
        default:
            return "Dashboard";
    }
});

const handleLogout = async () => {
    try {
        await authStore.logout();
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>
