<template>
    <!-- Mobile Backdrop Overlay -->
    <div
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-xs transition-opacity lg:hidden"
    ></div>

    <!-- Quiet-Chrome SaaS Sidebar -->
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-50 w-68 bg-white border-r border-slate-200/90 text-slate-600 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Brand Header (Synced with @/config/branding) -->
        <div
            class="h-16 flex items-center justify-between px-5 border-b border-slate-100 shrink-0"
        >
            <router-link
                :to="{ name: 'Dashboard' }"
                class="flex items-center gap-3 group focus:outline-none"
            >
                <div
                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-[#F8313B] to-rose-500 flex items-center justify-center text-white font-black text-xs tracking-tighter shadow-sm shadow-rose-500/20 group-hover:scale-105 transition-transform"
                >
                    {{ brandInitials }}
                </div>
                <div class="leading-tight">
                    <span
                        class="block text-xs font-extrabold tracking-tight text-[#0F1115]"
                    >
                        {{ branding.appName || "IMAQ Gym" }}
                    </span>
                    <span
                        class="block text-[10px] font-medium text-slate-400 tracking-wide"
                    >
                        Operations Portal
                    </span>
                </div>
            </router-link>

            <!-- Mobile Close Button -->
            <button
                @click="$emit('close')"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition"
                aria-label="Close sidebar"
            >
                <X class="w-4 h-4" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3.5 py-5 space-y-1 overflow-y-auto">
            <template v-for="item in visibleMenuItems" :key="item.name">
                <!-- Optional Section Divider -->
                <div v-if="item.sectionHeader" class="pt-5 pb-1.5 px-3">
                    <p
                        class="text-[10px] font-bold uppercase tracking-widest text-slate-400"
                    >
                        {{ item.sectionHeader }}
                    </p>
                </div>

                <router-link
                    :to="{ name: item.routeName }"
                    @click="$emit('close')"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-medium text-slate-600 hover:text-[#0F1115] hover:bg-slate-50 transition-all duration-150"
                    active-class="!bg-rose-50/90 !text-[#F8313B] !font-semibold ring-1 ring-inset ring-rose-200/70"
                >
                    <component
                        :is="item.icon"
                        class="w-4 h-4 shrink-0 text-slate-400 group-hover:text-slate-700 group-[.router-link-active]:text-[#F8313B] transition-colors"
                    />
                    <span class="truncate">{{ item.name }}</span>
                </router-link>
            </template>
        </nav>

        <!-- Bottom Staff Card & Quick Logout -->
        <div class="p-3.5 border-t border-slate-100 bg-slate-50/60 shrink-0">
            <div class="flex items-center justify-between gap-2">
                <router-link
                    :to="{ name: 'UserProfile' }"
                    @click="$emit('close')"
                    class="flex items-center gap-2.5 min-w-0 flex-1 p-1.5 rounded-xl hover:bg-white transition"
                >
                    <div
                        class="w-8 h-8 rounded-lg bg-rose-100/80 text-[#F8313B] font-bold flex items-center justify-center text-xs border border-rose-200/60 shrink-0"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold text-[#0F1115] truncate"
                        >
                            {{ authStore.user?.name || "Staff User" }}
                        </p>
                        <p
                            class="text-[10px] font-medium text-slate-400 capitalize truncate"
                        >
                            {{ authStore.user?.role?.slug || "Active Shift" }}
                        </p>
                    </div>
                </router-link>

                <button
                    @click="handleLogout"
                    title="End Shift & Logout"
                    class="p-2 rounded-lg text-slate-400 hover:text-[#F8313B] hover:bg-rose-50 transition shrink-0"
                >
                    <LogOut class="w-4 h-4" />
                </button>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import {
    LayoutDashboard,
    Users,
    QrCode,
    CreditCard,
    ShieldCheck,
    Settings,
    LogOut,
    X,
} from "lucide-vue-next";

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["close"]);

const authStore = useAuthStore();

const brandInitials = computed(() => {
    const name = branding.appName || "IMAQ";
    return name.substring(0, 3).toUpperCase();
});

const userInitials = computed(() => {
    const name = authStore.user?.name;
    if (!name) return "SU";
    const parts = name.trim().split(" ");
    if (parts.length >= 2) {
        return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
});

const menuItems = computed(() => [
    {
        name: "Dashboard",
        routeName: "Dashboard",
        icon: LayoutDashboard,
        visible: () => true,
    },
    {
        name: "Members",
        routeName: "MemberManagement",
        icon: Users,
        visible: () => authStore.isStaff || authStore.isAdmin,
    },
    {
        name: "Attendance",
        routeName: "attendance",
        icon: QrCode,
        visible: () => authStore.isStaff || authStore.isAdmin,
    },
    {
        name: "Payments",
        routeName: "payment",
        icon: CreditCard,
        visible: () => true,
    },
    {
        sectionHeader: "Administration",
        name: "Admin Control",
        routeName: "Adminview",
        icon: ShieldCheck,
        visible: () => authStore.isAdmin,
    },
    {
        name: "Gym Settings",
        routeName: "SettingsView",
        icon: Settings,
        visible: () => authStore.isAdmin,
    },
]);

const visibleMenuItems = computed(() =>
    menuItems.value.filter((item) => item.visible()),
);

const handleLogout = async () => {
    try {
        await authStore.logout();
    } catch (error) {
        console.error("Logout failed:", error);
    }
};
</script>
