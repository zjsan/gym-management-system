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
        </div>
    </div>
</template>

<script setup>
import SidebarNav from "@/components/navigation/SidebarNav.vue"; // Adjust path to your actual sidebar component
import AppHeader from "@/components/navigation/AppHeader.vue"; // Adjust path to your actual header component
</script>
