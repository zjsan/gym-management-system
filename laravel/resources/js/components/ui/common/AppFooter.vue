<template>
    <footer
        class="w-full border-t border-zinc-200/80 bg-white px-4 sm:px-6 lg:px-8 py-2 text-[11px] text-zinc-500 shrink-0"
    >
        <div
            class="max-w-[1400px] mx-auto flex items-center justify-between gap-4"
        >
            <!-- Left: Brand Identity & Location -->
            <div class="flex items-center gap-2 min-w-0 truncate">
                <span class="font-medium text-zinc-700 truncate">
                    &copy; {{ currentYear }}
                    {{ branding.appName || "IMAQ Gym" }}
                </span>
                <template v-if="branding.location">
                    <span class="text-zinc-300" aria-hidden="true"
                        >&middot;</span
                    >
                    <span class="truncate">{{ branding.location }}</span>
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
                    <span class="text-zinc-600 font-medium">
                        {{ isOnline ? "Online" : "Offline Mode" }}
                    </span>
                </div>

                <span class="hidden sm:inline text-zinc-300" aria-hidden="true"
                    >|</span
                >

                <a
                    href="mailto:zjsantos25@gmail.com"
                    class="text-zinc-600 hover:text-zinc-950 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B] rounded px-0.5"
                >
                    Support
                </a>

                <span
                    v-if="branding.version"
                    class="font-mono tabular-nums px-1.5 py-0.5 rounded bg-zinc-100 text-zinc-600 border border-zinc-200/70"
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
