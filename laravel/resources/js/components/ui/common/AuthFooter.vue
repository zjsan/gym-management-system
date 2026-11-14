<template>
    <div class="mt-8 pt-6 border-t border-slate-800/80 text-center space-y-1">
        <p class="text-xs font-semibold text-slate-600 tracking-wide">
            &copy; {{ currentYear }} {{ branding.appName }}
        </p>
        <p class="text-[11px] text-slate-500 font-medium">
            Secure Operations Portal &bull;
            <span class="text-slate-600">{{ branding.location }}</span>
            <span class="text-[10px] text-slate-400">
                &bull; v{{ branding.version }}</span
            >
        </p>
    </div>
</template>

<script setup>
import { branding } from "@/config/branding";
const currentYear = new Date().getFullYear();
</script>
