<template>
    <aside
        class="w-64 bg-slate-900 text-slate-300 flex flex-col border-r border-slate-800 shrink-0"
    >
        <!-- Brand / Logo Area -->
        <div class="h-16 flex items-center px-6 border-b border-slate-800">
            <span class="text-lg font-extrabold text-white tracking-wide"
                >IMAQ GYM</span
            >
            <span
                class="ml-2 px-2 py-0.5 text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 rounded"
                >SYSTEM</span
            >
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
            <router-link
                :to="{ name: 'Dashboard' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                    />
                </svg>
                Dashboard
            </router-link>

            <!-- Staff / General Links -->
            <router-link
                v-if="authStore.isStaff || authStore.isAdmin"
                :to="{ name: 'MemberManagement' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
                Members
            </router-link>

            <router-link
                v-if="authStore.isStaff || authStore.isAdmin"
                :to="{ name: 'attendance' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"
                    />
                </svg>
                Attendance Check-in
            </router-link>

            <router-link
                :to="{ name: 'payment' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                Payments & Ledger
            </router-link>

            <!-- Admin Section Divider -->
            <div v-if="authStore.isAdmin" class="pt-4 pb-2">
                <p
                    class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500"
                >
                    Administration
                </p>
            </div>

            <router-link
                v-if="authStore.isAdmin"
                :to="{ name: 'Adminview' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                    />
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                    />
                </svg>
                Admin Control
            </router-link>

            <router-link
                v-if="authStore.isAdmin"
                :to="{ name: 'SettingsView' }"
                class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition hover:bg-slate-800 hover:text-white"
                active-class="bg-emerald-600 text-white shadow-sm"
            >
                <svg
                    class="w-5 h-5 opacity-80"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"
                    />
                </svg>
                Gym Settings
            </router-link>
        </nav>

        <!-- User Quick Profile Footer inside Sidebar -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-full bg-emerald-600/30 text-emerald-400 font-bold flex items-center justify-center text-sm border border-emerald-500/30"
                >
                    {{ userInitials }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">
                        {{ authStore.user?.name || "Staff User" }}
                    </p>
                    <p class="text-[11px] text-slate-400 capitalize truncate">
                        {{ authStore.user?.role?.slug || "Active Shift" }}
                    </p>
                </div>
            </div>
        </div>
    </aside>
</template>

<script setup>
import { computed } from "vue";
import { useAuthStore } from "@/stores/auth";

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
</script>
