<template>
    <div class="min-h-screen bg-gray-100 flex">
        <!-- Sidebar Navigation Component -->
        <SidebarNav />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Header Component -->
            <AppHeader />

            <!-- Page Content View -->
            <main class="flex-1 overflow-y-auto p-6">
                <router-view />
            </main>
            <AppFooter />
        </div>
    </div>
</template>

<script setup>
import SidebarNav from "@/pages/private/Layout/SidebarNav.vue";
import AppHeader from "@/pages/private/Layout/AppHeader.vue";
import AppFooter from "@/components/ui/common/AppFooter.vue";
</script>
