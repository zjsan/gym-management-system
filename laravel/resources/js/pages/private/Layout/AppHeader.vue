<template>
    <header
        class="h-16 bg-white/95 backdrop-blur-xs border-b border-zinc-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4 shrink-0 z-30"
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
                    class="hidden sm:inline font-medium text-zinc-500 hover:text-zinc-950 transition-colors truncate max-w-[200px]"
                >
                    {{ branding.appName || "Portal" }}
                </router-link>
                <ChevronRight
                    class="hidden sm:inline w-3.5 h-3.5 text-zinc-400 shrink-0"
                    aria-hidden="true"
                />
                <h1
                    class="font-bold text-[13.5px] text-[#0F1115] tracking-tight truncate"
                >
                    {{ currentRouteTitle }}
                </h1>
            </nav>
        </div>

        <!-- Right: Global Command Search & Primary Action -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <!-- Quick Member Search Trigger -->
            <button
                type="button"
                @click="$emit('open-search')"
                class="inline-flex items-center gap-2 h-9 px-2.5 sm:px-3 rounded-lg bg-zinc-50/70 hover:bg-zinc-100/80 text-zinc-500 border border-zinc-200/90 text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 focus-visible:border-[#F8313B] cursor-pointer"
            >
                <Search class="w-3.5 h-3.5 text-zinc-500 shrink-0" />
                <span class="hidden md:inline font-medium"
                    >Find member or pass...</span
                >
                <kbd
                    class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-white rounded border border-zinc-200/90 shadow-2xs"
                >
                    ⌘K
                </kbd>
            </button>

            <!-- Dynamic Primary CTA Based on Role Access -->
            <router-link
                v-if="headerCta"
                :to="{ name: headerCta.routeName }"
                :class="[
                    'inline-flex items-center gap-1.5 h-9 px-3.5 rounded-lg text-xs font-bold transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/20',
                    route.name === headerCta.routeName
                        ? 'bg-[#F8313B]/10 text-[#F8313B] ring-1 ring-inset ring-[#F8313B]/25'
                        : 'bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white shadow-sm shadow-[#F8313B]/20',
                ]"
            >
                <component :is="headerCta.icon" class="w-3.5 h-3.5 shrink-0" />
                <span>{{ headerCta.label }}</span>
            </router-link>
        </div>
    </header>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import {
    Menu,
    ChevronRight,
    Search,
    QrCode,
    CreditCard,
} from "lucide-vue-next";

defineEmits(["toggle-sidebar", "open-search"]);

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

const headerCta = computed(() => {
    if (authStore.isStaff) {
        return {
            label: "Check-in",
            routeName: "attendance",
            icon: QrCode,
        };
    }

    if (authStore.isAdmin) {
        return {
            label: "Payments",
            routeName: "payment",
            icon: CreditCard,
        };
    }

    return {
        label: "Payments",
        routeName: "payment",
        icon: CreditCard,
    };
});
</script>
