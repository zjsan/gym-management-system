<template>
    <header
        class="h-14 bg-white/95 backdrop-blur-xs border-b border-zinc-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 shrink-0 z-30"
    >
        <!-- Left: Mobile Drawer Trigger & Dynamic Breadcrumb -->
        <div class="flex items-center gap-2.5 min-w-0">
            <button
                type="button"
                @click="$emit('toggle-sidebar')"
                class="lg:hidden -ml-2 p-2 rounded-lg text-zinc-600 hover:text-zinc-950 hover:bg-zinc-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
                aria-label="Open navigation menu"
            >
                <Menu class="w-5 h-5" />
            </button>

            <nav
                aria-label="Breadcrumb"
                class="flex items-center gap-1.5 text-xs min-w-0"
            >
                <router-link
                    :to="{ name: 'Dashboard' }"
                    class="hidden sm:inline font-medium text-zinc-500 hover:text-zinc-950 transition-colors truncate"
                >
                    {{ branding.appName || "Portal" }}
                </router-link>
                <ChevronRight
                    class="hidden sm:inline w-3.5 h-3.5 text-zinc-400 shrink-0"
                    aria-hidden="true"
                />
                <h1
                    class="font-semibold text-[13px] text-zinc-950 tracking-tight truncate"
                >
                    {{ currentRouteTitle }}
                </h1>
            </nav>
        </div>

        <!-- Right: Global Command Search & Primary Front-Desk Action -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <!-- Quick Member / Command Palette Trigger -->
            <button
                type="button"
                @click="$emit('open-command')"
                class="inline-flex items-center gap-2 h-8 px-2.5 sm:px-3 rounded-lg bg-zinc-50 hover:bg-zinc-100/80 text-zinc-500 border border-zinc-200/90 text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
            >
                <Search class="w-3.5 h-3.5 text-zinc-500 shrink-0" />
                <span class="hidden md:inline">Find member or pass...</span>
                <kbd
                    class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-medium text-zinc-500 bg-white rounded border border-zinc-200/90"
                >
                    ⌘K
                </kbd>
            </button>

            <!-- Primary Front-Desk CTA: Instant QR / Walk-in Check-in -->
            <router-link
                v-if="authStore.isStaff || authStore.isAdmin"
                :to="{ name: 'attendance' }"
                class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg bg-zinc-950 hover:bg-zinc-800 text-white text-xs font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B] focus-visible:ring-offset-1"
            >
                <QrCode class="w-3.5 h-3.5 text-[#F8313B]" />
                <span>Check-in</span>
            </router-link>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import { Menu, ChevronRight, Search, QrCode } from "lucide-vue-next";

defineEmits(["toggle-sidebar", "open-command"]);

const route = useRoute();
const authStore = useAuthStore();

const ROUTE_TITLES = {
    Dashboard: "Dashboard Overview",
    MemberManagement: "Members",
    attendance: "Attendance & QR Check-in",
    payment: "Payments & Collections",
    Adminview: "Admin Control",
    SettingsView: "Gym Settings",
    UserProfile: "Account & Shift Profile",
};

const currentRouteTitle = computed(() => {
    return route.meta?.title || ROUTE_TITLES[route.name] || "Overview";
});
</script>
