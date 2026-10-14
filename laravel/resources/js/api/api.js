import axios from "axios";
import { useAuthStore } from "../stores/authStore";
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
            const authStore = useAuthStore();
            authStore.saveUserToStorage(null);

            if (router.currentRoute.value.name !== "Login") {
                router.push({ name: "Login" });
            }
        }
        return Promise.reject(error);
    },
);

export default api;
