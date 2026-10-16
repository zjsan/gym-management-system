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

        initTabSync() {
            window.addEventListener("storage", (event) => {
                if (event.key === "user") {
                    const newUser = event.newValue ? JSON.parse(event.newValue) : null;
                    this.user = newUser;

                    const currentRoute = router.currentRoute.value;
                    const currentRouteName = currentRoute.name;

                    // Determine if the current route needs any type of authentication/authorization
                    const routeRequiresAuth = currentRoute.meta.requiresAuth || currentRoute.meta.requiresAdmin;

                    if (!newUser && routeRequiresAuth) {
                        // User logged out in another tab, and this tab is on a protected route
                        if (currentRouteName !== "Login") {
                            router.push({ name: "Login" });
                        }
                    } else if (newUser && currentRouteName === "Login") {
                        // User logged in on another tab, kick this tab to dashboard
                        router.replace({ name: "Dashboard" });
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

                this.saveUserToStorage(data.user);
                router.replace({ name: "Dashboard" });
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            if (!this.user) return;

            try {
                const response = await api.get("/user");
                this.saveUserToStorage(response.data);
                this.error = null;
            } catch (error) {
                // Only logout if it's explicitly 401 AND we are certain the session is dead
                if (error.response && error.response.status === 401) {
                    // Prevent false positives on cold tab load race conditions
                    console.warn("Session expired or invalid. Clearing local state.");
                    this.saveUserToStorage(null);
                    if (router.currentRoute.value.name !== "Login") {
                        router.push({ name: "Login" });
                    }
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