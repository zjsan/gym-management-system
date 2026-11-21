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

        <!-- Right: Dynamic Command Palette Trigger & Primary Role CTA -->
        <div class="flex items-center gap-2 sm:gap-2.5 shrink-0">
            <!-- Role-Aware Quick Command Search Trigger -->
            <button
                type="button"
                @click="isCommandOpen = true"
                class="inline-flex items-center gap-2 h-9 px-2.5 sm:px-3 rounded-lg bg-zinc-50/70 hover:bg-zinc-100/80 text-zinc-500 border border-zinc-200/90 text-xs transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B]/15 focus-visible:border-[#F8313B] cursor-pointer"
            >
                <Search class="w-3.5 h-3.5 text-zinc-500 shrink-0" />
                <span class="hidden md:inline font-medium">
                    {{ commandPlaceholder }}
                </span>
                <kbd
                    class="hidden sm:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-semibold text-zinc-500 bg-white rounded border border-zinc-200/90 shadow-2xs"
                >
                    {{ shortcutLabel }}
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

    <!-- Functional Shadcn Command Palette (Role-Filtered) -->
    <CommandDialog v-model:open="isCommandOpen">
        <CommandInput :placeholder="commandPlaceholder" />
        <CommandList class="max-h-[320px]">
            <CommandEmpty class="py-6 text-center text-xs text-zinc-500">
                No matching actions or pages found.
            </CommandEmpty>

            <CommandGroup heading="Quick Navigation">
                <CommandItem
                    v-for="item in roleCommandItems"
                    :key="item.routeName"
                    :value="item.keywords"
                    @select="runCommand(item.routeName)"
                    class="flex items-center justify-between gap-2 px-3 py-2 text-xs cursor-pointer"
                >
                    <div class="flex items-center gap-2.5">
                        <component
                            :is="item.icon"
                            class="w-4 h-4 text-zinc-500 shrink-0"
                        />
                        <span class="font-medium text-[#0F1115]">{{
                            item.label
                        }}</span>
                    </div>
                    <span class="text-[11px] text-zinc-400">
                        {{ item.hint }}
                    </span>
                </CommandItem>
            </CommandGroup>
        </CommandList>
    </CommandDialog>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { branding } from "@/config/branding";
import {
    CommandDialog,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command";
import {
    Menu,
    ChevronRight,
    Search,
    QrCode,
    CreditCard,
    LayoutDashboard,
    Users,
    ShieldCheck,
    Settings,
    User,
} from "lucide-vue-next";

defineEmits(["toggle-sidebar"]);

const route = useRoute();
const router = useRouter();
const authStore = useAuthStore();

const isCommandOpen = ref(false);

// Detect Mac vs Windows/Linux for accurate shortcut badge
const shortcutLabel = computed(() => {
    if (
        typeof navigator !== "undefined" &&
        /Mac|iPhone|iPad/.test(navigator.platform)
    ) {
        return "⌘K";
    }
    return "Ctrl K";
});

const ROUTE_TITLES = {
    Dashboard: "Dashboard Overview",
    MemberManagement: "Members",
    attendance: "Attendance & QR Check-in",
    payment: "Payments & Collections",
    Adminview: "Admin Control",
    SettingsView: "Gym Settings",
    UserProfile: "Account Settings",
};

const currentRouteTitle = computed(() => {
    return route.meta?.title || ROUTE_TITLES[route.name] || "Overview";
});

// Role-specific placeholder text
const commandPlaceholder = computed(() => {
    if (authStore.isStaff) {
        return "Quick jump to members, check-in, or payments...";
    }
    if (authStore.isAdmin) {
        return "Quick jump to payments, users, or settings...";
    }
    return "Quick jump...";
});

// Role-specific top-right primary button
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

    return null;
});

// Role-filtered commands inside the ⌘K dialog
const roleCommandItems = computed(() => {
    const items = [
        {
            label: "Dashboard Overview",
            keywords: "dashboard home overview metrics",
            hint: "Go to Dashboard",
            routeName: "Dashboard",
            icon: LayoutDashboard,
            visible: true,
        },
        {
            label: "Member Directory",
            keywords: "members search lookup active expired",
            hint: "Front Desk",
            routeName: "MemberManagement",
            icon: Users,
            visible: authStore.isStaff,
        },
        {
            label: "QR Check-in & Attendance",
            keywords: "attendance qr scan check in walk-in",
            hint: "Front Desk",
            routeName: "attendance",
            icon: QrCode,
            visible: authStore.isStaff,
        },
        {
            label: "Payments & Collections",
            keywords: "payments revenue transactions billing history",
            hint: "Finance",
            routeName: "payment",
            icon: CreditCard,
            visible: true,
        },
        {
            label: "Admin Control & Staff Accounts",
            keywords: "admin users staff roles permissions management",
            hint: "Administration",
            routeName: "Adminview",
            icon: ShieldCheck,
            visible: authStore.isAdmin,
        },
        {
            label: "Gym Settings & Rates",
            keywords: "settings configuration gym rates membership plans",
            hint: "Administration",
            routeName: "SettingsView",
            icon: Settings,
            visible: authStore.isAdmin,
        },
        {
            label: "Account Settings",
            keywords: "profile password account shift",
            hint: "Account",
            routeName: "UserProfile",
            icon: User,
            visible: true,
        },
    ];

    return items.filter((item) => item.visible);
});

const runCommand = (routeName) => {
    isCommandOpen.value = false;
    router.push({ name: routeName });
};

// Global Ctrl+K / Cmd+K listener
const handleKeydown = (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") {
        e.preventDefault();
        isCommandOpen.value = !isCommandOpen.value;
    }
};

onMounted(() => window.addEventListener("keydown", handleKeydown));
onUnmounted(() => window.removeEventListener("keydown", handleKeydown));
</script>
