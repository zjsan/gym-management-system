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
        
        // Convert to string safely to compare with what is already on disk
        const currentStored = localStorage.getItem("user");
        const stringifiedUser = user ? JSON.stringify(user) : null;

        // ONLY write to localStorage if the values are actually different.
        // This stops tabs from triggering destructive 'storage' events on each other during race conditions.
        if (currentStored !== stringifiedUser) {
            if (stringifiedUser) {
                localStorage.setItem("user", stringifiedUser);
            } else {
                localStorage.removeItem("user");
            }
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

                // Intercept if the user is required to change their temporary password
                if (data.requires_password_change) {
                    router.replace({ name: "ForcePasswordChange" });
                    return { success: true, requiresPasswordChange: true };
                }

                router.replace({ name: "Dashboard" });
            } catch (error) {
                this.error = error.response?.data?.message || "Login failed";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updatePassword(passwordData) {
            this.loading = true;
            this.error = null;

            try {
                const response = await api.put("/user/update-password", passwordData);
                
                // Update local user data if needed
                if (this.user) {
                    this.user.must_change_password = false;
                    this.saveUserToStorage(this.user);
                }

                return { success: true, message: response.data.message };
            } catch (error) {
                this.error = error.response?.data?.errors || error.response?.data?.message || "Failed to update password.";
                return { success: false, message: this.error };
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