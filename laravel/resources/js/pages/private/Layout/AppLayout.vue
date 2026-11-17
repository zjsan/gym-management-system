<template>
    <div class="min-h-screen bg-[#F8FAFC] flex text-[#0F1115]">
        <!-- Persistent / Responsive Sidebar -->
        <SidebarNav :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

        <!-- Main Column -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navigation Header -->
            <AppHeader @toggle-sidebar="isSidebarOpen = !isSidebarOpen" />

            <!-- Main Scrollable Canvas -->
            <main class="flex-1 overflow-y-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="max-w-7xl mx-auto">
                    <router-view />
                </div>
            </main>

            <!-- Your Existing Branded Footer -->
            <AppFooter />
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import SidebarNav from "@/pages/private/Layout/SidebarNav.vue";
import AppHeader from "@/pages/private/Layout/AppHeader.vue";
import AppFooter from "@/components/ui/common/AppFooter.vue";

const isSidebarOpen = ref(false);
</script>
