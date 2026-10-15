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

// Initialize cross-tab synchronization and session verification
const authStore = useAuthStore();
authStore.initTabSync();

// Verify the session with the backend on initial load if user exists locally
if (authStore.user) {
    authStore.fetchUser();
}

app.mount("#app");
