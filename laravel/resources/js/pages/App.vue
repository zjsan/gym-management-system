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
import api from "../api/api";
import IdleWarningModal from "@/pages/protected/Components/IdleWarningModal.vue";

const authStore = useAuthStore();

//  For testing right now, change 15 * 60 * 1000 to 10000 (10 seconds)
// adjust time out for 30-1hr in production

// ==========================================
// PRODUCTION SESSION TIMEOUT CONFIGURATION
// ==========================================
// - Idle Threshold: 1 hour (60 * 60 * 1000 ms)
//   Allows staff to handle longer tasks on the gym floor
//   without constant interruptions, while still securing
//   the terminal if they leave for lunch or go home.
// - Warning Countdown: 2 minutes (2 * 60 * 1000 ms)
//   Provides a generous window for staff to notice the
//   modal and return to their desk to extend the session.
// ==========================================
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
    15 * 60 * 1000, // 15 minutes idle time (for testing)
    60 * 1000, // 60 seconds warning countdown
);

// Handle tab wakeup / visibility change
const handleVisibilityChange = async () => {
    // Only check if the tab became visible AND the user is supposed to be logged in
    if (document.visibilityState === "visible" && authStore.user) {
        try {
            // Silent ping to verify session validity with Laravel
            await api.get("/user");
        } catch (error) {
            // If the session expired overnight, the 401 interceptor
            // will automatically handle logout & redirection!
            console.warn("Session invalidated while tab was inactive.");
        }
    }
};

const addVisibilityChange = () =>
    document.addEventListener("visibilitychange", handleVisibilityChange);
const removeVisibilityChange = () =>
    document.removeEventListener("visibilitychange", handleVisibilityChange);

onMounted(() => {
    setupListeners();
    addVisilibilityChange();
});

onUnmounted(() => {
    cleanupListeners();
    removeVisibilityChange();
});
</script>
