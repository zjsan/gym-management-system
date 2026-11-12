<template>
    <router-view />

    <!-- Idle Warning Modal -->
    <IdleWarningModal
        :show="showWarningModal"
        :secondsRemaining="secondsRemaining"
        @extend="extendSession"
    />
</template>
<script setup></script>

<script setup>
import { onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useIdleTimeout } from "@/composables/useIdleTimeout";
import IdleWarningModal from "@/pages/protected/Components/IdleWarningModal.vue";

const authStore = useAuthStore();

//  For testing right now, change 15 * 60 * 1000 to 10000 (10 seconds)
const {
    showWarningModal,
    secondsRemaining,
    extendSession,
    setupListeners,
    cleanupListeners,
} = useIdleTimeout(
    async () => {
        // Callback when timer hits 0
        authStore.error = "Logged out due to inactivity.";
        await authStore.logout();
        authStore.error =
            "Your session has expired due to inactivity. Please log in again.";
    },
    10000, // 10 seconds idle time (for testing)
    60 * 1000, // 60 seconds warning countdown
);

onMounted(() => {
    setupListeners();
});

onUnmounted(() => {
    cleanupListeners();
});
</script>
