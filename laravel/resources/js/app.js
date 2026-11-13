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

const authStore = useAuthStore();
authStore.initTabSync();

// If we have a cached user, silently sync fresh permissions in the background 
// without blocking the initial DOM render or throwing permission errors.
if (authStore.isAuthenticated) {
    authStore.fetchUser().catch(() => {
        // Interceptor will handle 401 redirects if session is genuinely dead
    });
}

app.mount("#app");