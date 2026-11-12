import { ref, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";

export function useIdleTimeout(
    onIdleTimeout,
    idleTimeMs = 15 * 60 * 1000,
    warningTimeMs = 60 * 1000,
) {
    const authStore = useAuthStore();
    const showWarningModal = ref(false);
    const secondsRemaining = ref(60);

    let idleTimer = null;
    let countdownTimer = null;

    const resetTimers = () => {
        // Only track idle if the user is actually logged in
        if (!authStore.isAuthenticated) return;

        clearTimeout(idleTimer);
        clearInterval(countdownTimer);
        showWarningModal.value = false;

        // Start the main 15-minute idle countdown
        idleTimer = setTimeout(() => {
            triggerWarning();
        }, idleTimeMs);
    };

    const triggerWarning = () => {
        if (!authStore.isAuthenticated) return;

        showWarningModal.value = true;
        secondsRemaining.value = warningTimeMs / 1000;

        // Start the 60-second countdown ticker
        countdownTimer = setInterval(() => {
            secondsRemaining.value--;
            if (secondsRemaining.value <= 0) {
                clearInterval(countdownTimer);
                showWarningModal.value = false;
                // Time's up! Execute logout
                onIdleTimeout();
            }
        }, 1000);
    };

    const handleUserActivity = () => {
        // Ignore activity if the warning modal is already showing (force them to click the button)
        if (showWarningModal.value) return;
        resetTimers();
    };

    const extendSession = () => {
        resetTimers();
        // Optional: Ping backend to touch the session cookie lifetime if desired
    };

    const setupListeners = () => {
        const events = [
            "mousemove",
            "keydown",
            "mousedown",
            "touchstart",
            "scroll",
        ];
        events.forEach((event) => {
            window.addEventListener(event, handleUserActivity);
        });
        resetTimers();
    };

    const cleanupListeners = () => {
        const events = [
            "mousemove",
            "keydown",
            "mousedown",
            "touchstart",
            "scroll",
        ];
        events.forEach((event) => {
            window.removeEventListener(event, handleUserActivity);
        });
        clearTimeout(idleTimer);
        clearInterval(countdownTimer);
    };

    return {
        showWarningModal,
        secondsRemaining,
        extendSession,
        setupListeners,
        cleanupListeners,
    };
}
