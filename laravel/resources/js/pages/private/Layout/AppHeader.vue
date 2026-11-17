<template>
    <header
        class="h-20 bg-white border-b border-gray-100 px-6 md:px-8 flex items-center justify-between shrink-0 shadow-2xs z-30"
    >
        <!-- Left: Mobile Menu Trigger & Dynamic Context Breadcrumb -->
        <div class="flex items-center gap-4">
            <button
                @click="$emit('toggle-sidebar')"
                class="lg:hidden text-gray-500 hover:text-gray-900 p-2 rounded-xl hover:bg-gray-100 transition"
                aria-label="Toggle navigation menu"
            >
                <Menu class="w-5 h-5" />
            </button>

            <div class="flex flex-col">
                <span
                    class="text-[10px] font-bold uppercase tracking-wider text-gray-400"
                    >Portal View</span
                >
                <h1
                    class="text-sm md:text-base font-black text-gray-900 tracking-tight"
                >
                    {{ currentRouteTitle }}
                </h1>
            </div>
        </div>

        <!-- Right: Operational Status & User Profile Trigger -->
        <div class="flex items-center gap-4">
            <!-- Active Shift Indicator Pill -->
            <div
                class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-200/60 rounded-full"
            >
                <span
                    class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                ></span>
                <span
                    class="text-[11px] font-bold text-emerald-700 tracking-wide"
                    >Shift Active</span
                >
            </div>

            <div class="h-6 w-px bg-gray-200 hidden sm:block"></div>

            <!-- Profile & Account Menu Link -->
            <router-link
                :to="{ name: 'UserProfile' }"
                class="flex items-center gap-3 group py-1.5 px-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-200 transition-all duration-200"
            >
                <div class="text-right hidden md:block">
                    <p
                        class="text-xs font-bold text-gray-900 group-hover:text-[#E5433C] transition"
                    >
                        {{ authStore.user?.name || "Administrator" }}
                    </p>
                    <p
                        class="text-[11px] font-medium text-gray-400 truncate max-w-[160px]"
                    >
                        {{ authStore.user?.email || "staff@imaqgym.com" }}
                    </p>
                </div>

                <!-- Crimson Brand Avatar -->
                <div
                    class="w-10 h-10 rounded-xl bg-red-50 border-2 border-[#E5433C]/20 group-hover:border-[#E5433C] flex items-center justify-center text-[#E5433C] shadow-2xs transition-all"
                >
                    <User class="w-5 h-5" />
                </div>
            </router-link>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { Menu, User } from "lucide-vue-next";

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
            return "Attendance & QR Check-in Terminal";
        case "payment":
            return "Financial Ledger & Transactions";
        case "Adminview":
            return "Administration Control Center";
        case "SettingsView":
            return "Gym Settings & Configurations";
        case "UserProfile":
            return "User Profile & Security Settings";
        default:
            return "Gym Management Portal";
    }
});
</script>
