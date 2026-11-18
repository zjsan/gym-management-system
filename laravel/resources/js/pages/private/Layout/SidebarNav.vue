<template>
    <!-- Mobile Backdrop Overlay -->
    <Transition
        enter-active-class="transition-opacity duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="isOpen"
            @click="$emit('close')"
            class="fixed inset-0 z-40 bg-zinc-950/25 backdrop-blur-[2px] lg:hidden"
            aria-hidden="true"
        />
    </Transition>

    <!-- Quiet-Chrome SaaS Sidebar -->
    <aside
        aria-label="Primary Navigation"
        :class="[
            'fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-zinc-200/80 text-zinc-600 flex flex-col transition-transform duration-200 ease-out lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Brand Header -->
        <div
            class="h-14 flex items-center justify-between px-4 border-b border-zinc-200/70 shrink-0"
        >
            <router-link
                :to="{ name: 'Dashboard' }"
                @click="$emit('close')"
                class="flex items-center gap-2.5 min-w-0 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
            >
                <div
                    class="w-7 h-7 rounded-lg bg-[#F8313B] flex items-center justify-center text-white font-bold text-[11px] tracking-tighter ring-1 ring-inset ring-black/10 shrink-0"
                >
                    {{ brandInitials }}
                </div>
                <div class="min-w-0 leading-none">
                    <span
                        class="block text-[13px] font-semibold tracking-tight text-zinc-950 truncate"
                    >
                        {{ branding.appName || "IMAQ Gym" }}
                    </span>
                    <span
                        class="block text-[11px] font-medium text-zinc-500 mt-0.5 truncate"
                    >
                        Operations Portal
                    </span>
                </div>
            </router-link>

            <!-- Mobile Close Button (44px touch target) -->
            <button
                type="button"
                @click="$emit('close')"
                class="lg:hidden -mr-1.5 p-2 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
                aria-label="Close navigation menu"
            >
                <X class="w-4 h-4" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
            <template v-for="item in visibleMenuItems" :key="item.routeName">
                <div v-if="item.sectionHeader" class="pt-5 pb-1.5 px-2.5">
                    <p
                        class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                    >
                        {{ item.sectionHeader }}
                    </p>
                </div>

                <router-link
                    :to="{ name: item.routeName }"
                    @click="$emit('close')"
                    v-slot="{ isActive }"
                    custom
                >
                    <a
                        :href="$router.resolve({ name: item.routeName }).href"
                        @click.prevent="navigate(item.routeName)"
                        :aria-current="isActive ? 'page' : undefined"
                        :class="[
                            'group flex items-center justify-between gap-2.5 px-2.5 py-2 rounded-lg text-[13px] transition-colors duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]',
                            isActive
                                ? 'bg-zinc-100/90 text-zinc-950 font-semibold'
                                : 'text-zinc-600 font-medium hover:text-zinc-950 hover:bg-zinc-50',
                        ]"
                    >
                        <span class="flex items-center gap-2.5 min-w-0">
                            <component
                                :is="item.icon"
                                :class="[
                                    'w-4 h-4 shrink-0 transition-colors',
                                    isActive
                                        ? 'text-[#F8313B]'
                                        : 'text-zinc-500 group-hover:text-zinc-800',
                                ]"
                            />
                            <span class="truncate">{{ item.name }}</span>
                        </span>

                        <!-- Subtle Active Dot -->
                        <span
                            v-if="isActive"
                            class="w-1.5 h-1.5 rounded-full bg-[#F8313B] shrink-0"
                            aria-hidden="true"
                        />
                    </a>
                </router-link>
            </template>
        </nav>

        <!-- Consolidated Staff Profile & Safe Shift-End Footer -->
        <div class="p-3 border-t border-zinc-200/70 bg-zinc-50/50 shrink-0">
            <div class="flex items-center justify-between gap-1.5">
                <router-link
                    :to="{ name: 'UserProfile' }"
                    @click="$emit('close')"
                    class="flex items-center gap-2.5 min-w-0 flex-1 p-1.5 rounded-lg hover:bg-zinc-100/80 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
                >
                    <div
                        class="w-7 h-7 rounded-md bg-zinc-900 text-white font-semibold flex items-center justify-center text-[11px] tracking-tight shrink-0"
                    >
                        {{ userInitials }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p
                            class="text-xs font-semibold text-zinc-950 truncate leading-tight"
                        >
                            {{ authStore.user?.name || "Staff User" }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span
                                class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"
                            />
                            <p
                                class="text-[11px] font-medium text-zinc-500 capitalize truncate leading-tight"
                            >
                                {{
                                    authStore.user?.role?.slug || "Active Shift"
                                }}
                            </p>
                        </div>
                    </div>
                </router-link>

                <button
                    type="button"
                    @click="handleLogout"
                    :disabled="isLoggingOut"
                    :title="
                        confirmLogout
                            ? 'Click again to confirm logout'
                            : 'End Shift & Logout'
                    "
                    :aria-label="
                        confirmLogout
                            ? 'Confirm logout'
                            : 'End shift and logout'
                    "
                    :class="[
                        'px-2.5 py-2 rounded-lg text-xs font-medium transition-all shrink-0 flex items-center gap-1.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]',
                        confirmLogout
                            ? 'bg-[#F8313B] text-white shadow-xs'
                            : 'text-zinc-500 hover:text-zinc-950 hover:bg-zinc-200/60',
                    ]"
                >
                    <Loader2
                        v-if="isLoggingOut"
                        class="w-3.5 h-3.5 animate-spin"
                    />
                    <LogOut v-else class="w-3.5 h-3.5" />
                    <span v-if="confirmLogout" class="text-[11px] font-semibold"
                        >Confirm</span
                    >
                </button>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
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
    Loader2,
    X,
} from "lucide-vue-next";

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);

const router = useRouter();
const authStore = useAuthStore();

const isLoggingOut = ref(false);
const confirmLogout = ref(false);
let confirmTimer = null;

const navigate = (routeName) => {
    router.push({ name: routeName });
    emit("close");
};

const brandInitials = computed(() => {
    const name = branding.appName || "IMAQ";
    return name.substring(0, 3).toUpperCase();
});

const userInitials = computed(() => {
    const name = authStore.user?.name;
    if (!name) return "SU";
    const parts = name.trim().split(/\s+/);
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
    if (!confirmLogout.value) {
        confirmLogout.value = true;
        clearTimeout(confirmTimer);
        confirmTimer = setTimeout(() => {
            confirmLogout.value = false;
        }, 3500);
        return;
    }

    isLoggingOut.value = true;
    try {
        await authStore.logout();
    } catch (error) {
        console.error("Logout failed:", error);
        isLoggingOut.value = false;
        confirmLogout.value = false;
    }
};
</script>
