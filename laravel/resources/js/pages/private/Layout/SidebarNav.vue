<template>
    <!-- Mobile Backdrop with Smooth Transition -->
    <div
        v-if="isOpen"
        @click="$emit('close')"
        class="fixed inset-0 z-40 bg-slate-950/50 backdrop-blur-xs transition-opacity lg:hidden"
    ></div>

    <!-- Sidebar Shell -->
    <aside
        :class="[
            'fixed inset-y-0 left-0 z-50 w-72 bg-[#E5433C] text-white flex flex-col shadow-xl transition-transform duration-300 ease-in-out lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Brand Header -->
        <div
            class="h-20 flex items-center justify-between px-6 border-b border-white/10 shrink-0"
        >
            <div class="flex items-center gap-3.5">
                <div
                    class="w-10 h-10 rounded-xl bg-white/10 border border-white/20 flex items-center justify-center shadow-inner"
                >
                    <span class="text-white font-black text-sm tracking-tighter"
                        >IMQ</span
                    >
                </div>
                <div>
                    <span
                        class="block text-xs font-extrabold tracking-wider uppercase text-white"
                        >IMAQ GYM</span
                    >
                    <span
                        class="block text-[10px] font-medium tracking-widest text-white/70 uppercase"
                        >Operations Suite</span
                    >
                </div>
            </div>
            <!-- Mobile Close Button -->
            <button
                @click="$emit('close')"
                class="lg:hidden p-2 rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition"
            >
                <X class="w-5 h-5" />
            </button>
        </div>

        <!-- Navigation Links Container -->
        <nav
            class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto scrollbar-thin"
        >
            <template v-for="item in visibleMenuItems" :key="item.name">
                <!-- Section Group Header -->
                <div v-if="item.sectionHeader" class="pt-6 pb-2 px-3">
                    <p
                        class="text-[10px] font-bold uppercase tracking-wider text-white/60"
                    >
                        {{ item.sectionHeader }}
                    </p>
                </div>

                <!-- Nav Item Link -->
                <router-link
                    :to="{ name: item.routeName }"
                    @click="$emit('close')"
                    class="group flex items-center gap-3.5 px-4 py-3 rounded-xl text-xs font-semibold text-white/80 hover:text-white hover:bg-white/10 transition-all duration-200"
                    active-class="!bg-white !text-slate-900 !font-bold shadow-md shadow-black/5"
                >
                    <component
                        :is="item.icon"
                        class="w-4 h-4 shrink-auto transition-transform duration-200 group-hover:scale-110"
                    />
                    <span class="truncate">{{ item.name }}</span>
                </router-link>
            </template>
        </nav>

        <!-- User Quick-Profile / Shift Status Footer -->
        <div class="p-4 border-t border-white/10 bg-black/10 shrink-0">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-white/20 text-white font-extrabold flex items-center justify-center text-xs border border-white/25 shadow-inner shrink-0"
                >
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-white truncate">
                        {{ authStore.user?.name || "Staff User" }}
                    </p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span
                            class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"
                        ></span>
                        <span
                            class="text-[10px] font-medium text-white/80 capitalize truncate"
                        >
                            {{ authStore.user?.role?.slug || "Active Shift" }}
                        </span>
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
    ShieldCheck,
    Users,
    QrCode,
    CreditCard,
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
