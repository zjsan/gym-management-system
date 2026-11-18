<template>
    <div
        class="h-dvh w-full overflow-hidden bg-zinc-50/80 flex text-zinc-950 antialiased selection:bg-[#F8313B]/15 selection:text-[#F8313B]"
    >
        <!-- Persistent / Responsive Sidebar -->
        <SidebarNav :is-open="isSidebarOpen" @close="isSidebarOpen = false" />

        <!-- Main Column -->
        <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
            <!-- Top Navigation Header -->
            <AppHeader
                @toggle-sidebar="isSidebarOpen = !isSidebarOpen"
                @open-command="$emit('open-command')"
            />

            <!-- Main Scrollable Canvas -->
            <main class="flex-1 overflow-y-auto scroll-smooth">
                <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <router-view />
                </div>
            </main>

            <!-- Compact Operational Status Footer -->
            <AppFooter />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import SidebarNav from "@/pages/private/Layout/SidebarNav.vue";
import AppHeader from "@/pages/private/Layout/AppHeader.vue";
import AppFooter from "@/components/ui/common/AppFooter.vue";

defineEmits(["open-command"]);

const isSidebarOpen = ref(false);

const handleKeydown = (e) => {
    if (e.key === "Escape" && isSidebarOpen.value) {
        isSidebarOpen.value = false;
    }
};

onMounted(() => window.addEventListener("keydown", handleKeydown));
onUnmounted(() => window.removeEventListener("keydown", handleKeydown));
</script>
