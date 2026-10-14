import { defineStore } from "pinia";
import api from "../api/api";
import router from "../router/router.js";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        loading: false,
        error: null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.user,
        isAdmin: (state) => state.user?.role?.slug === "admin",
        isStaff: (state) => state.user?.role?.slug === "staff",
    },

    actions: {
        saveUserToStorage(user) {
            this.user = user;
            if (user) {
                localStorage.setItem("user", JSON.stringify(user));
            } else {
                localStorage.removeItem("user");
            }
        },

        // Initialize cross-tab synchronization listener
        initTabSync() {
            window.addEventListener("storage", (event) => {
                if (event.key === "user") {
                    this.user = event.newValue
                        ? JSON.parse(event.newValue)
                        : null;
                    if (
                        !this.user &&
                        router.currentRoute.value.meta.requiresAuth
                    ) {
                        router.push({ name: "Login" });
                    }
                }
            });
        },

        async login(credentials) {
            this.loading = true;
            this.error = null;

            try {
                await api.get("/sanctum/csrf-cookie");
                const response = await api.post("/login", credentials);
                const { data } = response;

                // Save user and trigger storage event for Tab B
                this.saveUserToStorage(data.user);

                if (this.user) {
                    router.replace({ name: "Dashboard" });
                } else {
                    await this.fetchUser();
                }
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            try {
                const response = await api.get("/user");
                this.saveUserToStorage(response.data);
                this.error = null;
            } catch (error) {
                console.error("Failed to fetch user session:", error);
                this.saveUserToStorage(null);
                if (router.currentRoute.value.name !== "Login") {
                    router.push({ name: "Login" });
                }
            }
        },

        async logout() {
            try {
                await api.post("/logout");
            } catch (error) {
                console.error("Logout failed:", error);
            } finally {
                this.saveUserToStorage(null);
                this.error = null;
                this.loading = false;

                if (router.currentRoute.value.name !== "Login") {
                    router.push({ name: "Login" });
                }
            }
        },
    },
});
