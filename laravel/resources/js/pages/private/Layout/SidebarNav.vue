<template>
    <!-- Mobile Backdrop Overlay -->
    <div
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
    ></div>

    <!-- Sidebar Container -->
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Brand / Logo Header -->
        <div
            class="h-16 flex items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950/40"
        >
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white shadow-md shadow-emerald-900/20 font-bold"
                >
                    I
                </div>
                <div>
                    <span
                        class="text-sm font-extrabold text-white tracking-wider"
                        >IMAQ GYM</span
                    >
                    <span
                        class="block text-[10px] font-medium text-emerald-400 tracking-widest uppercase"
                        >Operations Portal</span
                    >
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button
                @click="$emit('close')"
                class="lg:hidden text-slate-400 hover:text-white p-1"
            >
                <X class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <template v-for="item in visibleMenuItems" :key="item.name">
                <!-- Section Header (if defined) -->
                <div v-if="item.sectionHeader" class="pt-5 pb-2 px-3">
                    <p
                        class="text-[10px] font-bold uppercase tracking-wider text-slate-500"
                    >
                        {{ item.sectionHeader }}
                    </p>
                </div>

                <router-link
                    :to="{ name: item.routeName }"
                    @click="$emit('close')"
                    class="flex items-center gap-3 px-3.5 py-3 rounded-xl text-xs font-semibold transition group relative"
                    active-class="bg-emerald-600 text-white shadow-lg shadow-emerald-900/30"
                >
                    <component
                        :is="item.icon"
                        class="w-4 h-4 transition-transform group-hover:scale-110"
                    />
                    <span>{{ item.name }}</span>
                </router-link>
            </template>
        </nav>

        <!-- User Profile Quick Footer -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/60">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-slate-800 text-emerald-400 font-bold flex items-center justify-center text-xs border border-slate-700 shadow-inner"
                >
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">
                        {{ authStore.user?.name || "Staff User" }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span
                            class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                        ></span>
                        <span
                            class="text-[10px] font-medium text-slate-400 capitalize truncate"
                            >{{
                                authStore.user?.role?.slug || "Active Shift"
                            }}</span
                        >
                    </div>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import {
    LayoutDashboard,
    Users,
    QrCode,
    CreditCard,
    ShieldCheck,
    Settings,
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

const userInitials = computed(() => {
    const name = authStore.user?.name;
    if (!name) return "SU";
    const parts = name.split(" ");
    if (parts.length >= 2) {
        return `${parts[0][0]}${parts[1][0]}`.toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
});

// Dynamic menu structure using Lucide component mappings
const menuItems = computed(() => [
    {
        name: "Dashboard Overview",
        routeName: "Dashboard",
        icon: LayoutDashboard,
        visible: () => true,
    },
    {
        name: "Member Management",
        routeName: "MemberManagement",
        icon: Users,
        visible: () => authStore.isStaff || authStore.isAdmin,
    },
    {
        name: "Attendance Scanner",
        routeName: "attendance",
        icon: QrCode,
        visible: () => authStore.isStaff || authStore.isAdmin,
    },
    {
        name: "Payments & Ledger",
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

const visibleMenuItems = computed(() => {
    return menuItems.value.filter((item) => item.visible());
});
</script>
