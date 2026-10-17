import axios from "axios";
import router from "../router/router";

const api = axios.create({
    baseURL: "http://localhost:8000" + "/api",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
    },
    withCredentials: true,
    withXSRFToken: true,
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response && error.response.status === 401) {
            // Avoid logging out if we are explicitly on the login page already
            if (router.currentRoute.value.name !== "Login") {
                import("@/stores/auth").then(({ useAuthStore }) => {
                    const authStore = useAuthStore();
                    
                    // Only clear if authenticated to prevent cross-tab cascading loops
                    if (authStore.isAuthenticated) {
                        authStore.saveUserToStorage(null);
                        router.push({ name: "Login" });
                    }
                });
            }
        }
        return Promise.reject(error);
    },
);

export default api;
