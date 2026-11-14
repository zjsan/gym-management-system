<template>
    <footer
        class="mt-auto w-full border-t border-slate-200/90 bg-white px-6 py-3.5 text-xs text-slate-500 transition-colors"
    >
        <div
            class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5"
        >
            <!-- Left: Brand Identity & Operational Location -->
            <div
                class="flex flex-wrap items-center justify-center sm:justify-start gap-2"
            >
                <span class="font-semibold text-[#0F1115] tracking-tight">
                    &copy; {{ currentYear }} {{ branding.appName }}
                </span>
                <span
                    v-if="branding.location"
                    class="text-slate-300"
                    aria-hidden="true"
                    >&bull;</span
                >
                <span
                    v-if="branding.location"
                    class="font-medium text-slate-500"
                >
                    {{ branding.location }}
                </span>
            </div>

            <!-- Right: System Status, IT Support & Build Version -->
            <div class="flex items-center gap-3 font-medium">
                <div class="hidden md:flex items-center gap-1.5 text-slate-600">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"
                        ></span>
                        <span
                            class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"
                        ></span>
                    </span>
                    <span class="text-[11px]">System Online</span>
                </div>

                <span class="hidden md:inline text-slate-300" aria-hidden="true"
                    >|</span
                >

                <a
                    href="mailto:zjsantos25@gmail.com"
                    class="text-slate-600 hover:text-[#F8313B] transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#F8313B] rounded px-1"
                >
                    IT Support
                </a>

                <span class="text-slate-300" aria-hidden="true">&bull;</span>

                <span
                    class="font-mono text-[11px] tabular-nums px-2 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200/70"
                >
                    v{{ branding.version }}
                </span>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { branding } from "@/config/branding";

const currentYear = new Date().getFullYear();
</script>
