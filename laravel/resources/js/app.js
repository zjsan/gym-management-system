import "./bootstrap";
import { createApp } from "vue";
import { createPinia } from "pinia";
import router from "./router/router.js";
import App from "./pages/App.vue";
import { useAuthStore } from "@/stores/auth";

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);

// Initialize cross-tab synchronization listener
const authStore = useAuthStore();
authStore.initTabSync();

// Mount the app first so the router and currentRoute are fully active
app.mount("#app");

// Verify session with backend after mount if user exists locally
if (authStore.user) {
    
    // Give the browser layout and cookies a moment to synchronize completely 
    // across windows before checking the backend state.
    window.requestAnimationFrame(() => {
        setTimeout(() => {
            authStore.fetchUser();
        }, 300);
    });
}