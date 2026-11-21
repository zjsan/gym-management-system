<template>
    <footer
        class="w-full h-14 border-t border-zinc-200/80 bg-white px-4 sm:px-6 lg:px-8 text-xs text-zinc-500 flex items-center shrink-0"
    >
        <div
            class="w-full max-w-[1400px] mx-auto flex items-center justify-between gap-4"
        >
            <!-- Left: Brand Identity & Operational Location -->
            <div class="flex items-center gap-2 min-w-0">
                <span class="font-semibold text-[#0F1115] shrink-0">
                    &copy; {{ currentYear }}
                    {{ branding.appName || "IMAQ Gym" }}
                </span>
                <template v-if="branding.location">
                    <span
                        class="hidden sm:inline text-zinc-300 shrink-0"
                        aria-hidden="true"
                        >&middot;</span
                    >
                    <span
                        class="hidden sm:inline font-medium text-zinc-500 truncate min-w-0"
                    >
                        {{ branding.location }}
                    </span>
                </template>
            </div>
            <!-- Right: Live Network Status, Support & Version -->
            <div class="flex items-center gap-3 shrink-0">
                <div
                    class="hidden sm:flex items-center gap-1.5"
                    :title="
                        isOnline
                            ? 'Connected to server'
                            : 'Offline — check local internet'
                    "
                >
                    <span
                        :class="[
                            'w-1.5 h-1.5 rounded-full',
                            isOnline ? 'bg-emerald-500' : 'bg-amber-500',
                        ]"
                    />
                    <span class="text-zinc-600 font-medium text-[11px]">
                        {{ isOnline ? "Online" : "Offline Mode" }}
                    </span>
                </div>

                <span class="hidden sm:inline text-zinc-300" aria-hidden="true"
                    >|</span
                >

                <a
                    :href="CONTACT_MAILTO"
                    class="text-zinc-600 hover:text-[#F8313B] font-medium text-[11px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B] rounded px-1"
                >
                    Support
                </a>

                <span
                    v-if="branding.version"
                    class="font-mono text-[11px] tabular-nums px-2 py-0.5 rounded-md bg-zinc-100 text-zinc-600 border border-zinc-200/80"
                >
                    v{{ branding.version }}
                </span>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { branding } from "@/config/branding";
import { CONTACT_MAILTO } from "@/config/contact";

const currentYear = new Date().getFullYear();
const isOnline = ref(
    typeof navigator !== "undefined" ? navigator.onLine : true,
);

const updateOnlineStatus = () => {
    isOnline.value = navigator.onLine;
};

onMounted(() => {
    window.addEventListener("online", updateOnlineStatus);
    window.addEventListener("offline", updateOnlineStatus);
});

onUnmounted(() => {
    window.removeEventListener("online", updateOnlineStatus);
    window.removeEventListener("offline", updateOnlineStatus);
});
</script>
