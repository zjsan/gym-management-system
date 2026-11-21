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
            'fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-zinc-200/80 text-zinc-600 flex flex-col transition-transform duration-200 ease-out lg:static lg:translate-x-0',
            isOpen ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Brand Header (Exact h-16) -->
        <div
            class="h-16 flex items-center justify-between gap-2.5 px-4 border-b border-zinc-200/80 shrink-0"
        >
            <router-link
                :to="{ name: 'Dashboard' }"
                @click="$emit('close')"
                class="flex items-center gap-3 min-w-0 flex-1 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
            >
                <div
                    class="w-10 h-10 flex items-center justify-center shrink-0"
                >
                    <img
                        :src="logoImg"
                        :alt="`${branding.appName || 'IMAQ Gym'} Logo`"
                        class="w-full h-full object-contain drop-shadow-xs"
                    />
                </div>
                <div class="min-w-0 flex-1 leading-tight">
                    <span
                        class="block text-[13px] font-extrabold tracking-tight text-[#0F1115] line-clamp-2"
                    >
                        {{ branding.appName || "IMAQ Gym" }}
                    </span>
                    <span
                        class="block text-[11px] font-medium text-zinc-500 mt-0.5"
                    >
                        Operations Portal
                    </span>
                </div>
            </router-link>

            <!-- Mobile Close Button -->
            <button
                type="button"
                @click="$emit('close')"
                class="lg:hidden -mr-1.5 p-2 rounded-lg text-zinc-500 hover:text-zinc-950 hover:bg-zinc-100 transition-colors shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]"
                aria-label="Close navigation menu"
            >
                <X class="w-4 h-4" />
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <template v-for="item in visibleMenuItems" :key="item.routeName">
                <div v-if="item.sectionHeader" class="pt-3.5 pb-1 px-3">
                    <p
                        class="text-[11px] font-semibold uppercase tracking-wider text-zinc-500"
                    >
                        {{ item.sectionHeader }}
                    </p>
                </div>

                <router-link
                    :to="{ name: item.routeName }"
                    v-slot="{ href, navigate, isActive }"
                    custom
                >
                    <a
                        :href="href"
                        @click="handleNavClick($event, navigate)"
                        :aria-current="isActive ? 'page' : undefined"
                        :class="[
                            'group relative flex items-center justify-between gap-2.5 px-3 py-2.5 rounded-lg text-[13px] transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/20',
                            isActive
                                ? 'bg-[#F8313B] hover:bg-[#e0242e] text-white font-semibold ring-1 ring-inset ring-[#F8313B]/20'
                                : 'text-zinc-600 font-medium hover:text-[#0F1115] hover:bg-zinc-100/80',
                        ]"
                    >
                        <span class="flex items-center gap-3 min-w-0">
                            <component
                                :is="item.icon"
                                :class="[
                                    'w-4 h-4 shrink-0 transition-colors',
                                    isActive
                                        ? 'text-white'
                                        : 'text-zinc-500 group-hover:text-[#0F1115]',
                                ]"
                            />
                            <span class="truncate">{{ item.name }}</span>
                        </span>
                    </a>
                </router-link>
            </template>
        </nav>

        <!-- Bottom Staff Card (Name + Role Pill + Email) & Logout Trigger -->
        <div
            class="min-h-16 px-3 py-2.5 border-t border-zinc-200/80 bg-zinc-50/70 flex items-center shrink-0"
        >
            <div class="w-full flex items-center justify-between gap-2">
                <router-link
                    :to="{ name: 'UserProfile' }"
                    @click="$emit('close')"
                    class="group flex items-center gap-2.5 min-w-0 flex-1 py-1 px-1.5 rounded-lg hover:bg-white hover:shadow-2xs border border-transparent hover:border-zinc-200/80 transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/20"
                >
                    <div
                        class="w-8 h-8 rounded-lg bg-[#F8313B] group-hover:bg-[#e0242e] text-white flex items-center justify-center shrink-0 shadow-xs shadow-[#F8313B]/25 transition-colors"
                    >
                        <User class="w-4 h-4 stroke-[2.25]" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <!-- Row 1: Full Name + Formatted Role Badge -->
                        <div class="flex items-center gap-1.5 min-w-0">
                            <p
                                class="text-xs font-semibold text-[#0F1115] truncate leading-tight"
                                :title="fullName"
                            >
                                {{ fullName }}
                            </p>
                            <span
                                class="text-[10px] text-slate-400 shrink-0 leading-none"
                                aria-hidden="true"
                                >&bull;</span
                            >
                            <span
                                class="shrink-0 text-[10px] font-bold capitalize text-[#F8313B] leading-none"
                            >
                                {{ formattedRole }}
                            </span>
                        </div>

                        <!-- Row 2: Email Address -->
                        <p
                            class="text-[11px] font-medium text-zinc-500 truncate leading-tight mt-1"
                            :title="authStore.user?.email"
                        >
                            {{ authStore.user?.email || "No email provided" }}
                        </p>
                    </div>
                </router-link>

                <!-- AlertDialog Trigger Button -->
                <button
                    type="button"
                    @click="openLogoutDialog"
                    title="End Shift & Logout"
                    aria-label="End shift and logout"
                    class="p-2 rounded-lg text-zinc-500 hover:text-[#F8313B] hover:bg-[#F8313B]/[0.08] transition-colors shrink-0 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/20 cursor-pointer"
                >
                    <LogOut class="w-4 h-4" />
                </button>
            </div>
        </div>
    </aside>

    <!-- Shadcn AlertDialog for Intentional Shift-End Confirmation -->
    <AlertDialog v-model:open="isLogoutDialogOpen">
        <AlertDialogContent
            class="sm:max-w-[380px] bg-white border border-zinc-200/90 rounded-xl p-5 shadow-xl"
        >
            <AlertDialogHeader class="space-y-1.5 text-left">
                <AlertDialogTitle
                    class="text-sm font-bold tracking-tight text-[#0F1115]"
                >
                    Sign out of your account?
                </AlertDialogTitle>
                <AlertDialogDescription
                    class="text-xs leading-relaxed text-zinc-500"
                >
                    You will need to sign back in to access your workspace.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter class="mt-4 flex flex-row justify-end gap-2">
                <AlertDialogCancel
                    :disabled="isLoggingOut"
                    class="mt-0 h-8 px-3 rounded-lg border border-zinc-200 bg-white hover:bg-zinc-50 text-zinc-700 text-xs font-semibold transition-colors cursor-pointer"
                >
                    Cancel
                </AlertDialogCancel>
                <AlertDialogAction
                    @click.prevent="confirmLogout"
                    :disabled="isLoggingOut"
                    class="h-8 px-3.5 rounded-lg bg-[#F8313B] hover:bg-[#e0242e] active:scale-[0.98] text-white text-xs font-bold shadow-sm shadow-[#F8313B]/20 transition-all inline-flex items-center gap-1.5 disabled:opacity-70 cursor-pointer"
                >
                    <Loader2
                        v-if="isLoggingOut"
                        class="w-3.5 h-3.5 animate-spin"
                    />
                    <span>{{
                        isLoggingOut ? "Signing out..." : "Sign out"
                    }}</span>
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>

<script setup>
import { ref, computed } from "vue";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import logoImg from "@/assets/images/imaq-logo.png";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";
import {
    LayoutDashboard,
    Users,
    QrCode,
    CreditCard,
    ShieldCheck,
    Settings,
    LogOut,
    User,
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

const authStore = useAuthStore();

const isLogoutDialogOpen = ref(false);
const isLoggingOut = ref(false);

const handleNavClick = (event, navigate) => {
    navigate(event);
    emit("close");
};

const openLogoutDialog = () => {
    logoutError.value = "";
    isLogoutDialogOpen.value = true;
};

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
        visible: () => authStore.isStaff,
    },
    {
        name: "Attendance",
        routeName: "attendance",
        icon: QrCode,
        visible: () => authStore.isStaff,
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

const fullName = computed(() => {
    const user = authStore.user;
    if (!user) return "Staff User";

    const combined = [user.first_name, user.last_name]
        .filter(Boolean)
        .join(" ")
        .trim();

    return combined || user.name || "Staff User";
});

const formattedRole = computed(() => {
    const rawRole = authStore.user?.role?.slug || authStore.user?.role?.name;
    if (!rawRole) return "Staff";
    return String(rawRole).replace(/[_-]+/g, " ");
});

const confirmLogout = async () => {
    isLoggingOut.value = true;
    await authStore.logout();
    isLogoutDialogOpen.value = false;
    isLoggingOut.value = false;
};
</script>
