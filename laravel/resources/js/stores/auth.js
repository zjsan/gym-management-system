import { defineStore } from "pinia";
import api from "../api/api";
import router from "@/router";

export const useAuthStore = defineStore("alerts", {
    // other options...
    state: () => {
        ((user = JSON.parse(localStorage.getItem("user")) || null), // stores logged-in user object
            (loading = false));
        error = null;
    },

    /**
     * 
     * If auth.user is null → not authenticated.
       If auth.user has data → authenticated.
     */
    getters: {
        isAuthenticated: (state) => !!state.user,
    },

    actions: {
        // Save user in localStorage
        saveUserToStorage() {
            localStorage.setItem("user", JSON.stringify(this.user));
        },

        //login
        async login(credentials) {
            this.loading = true;
            this.error = null;

            /* API call & set user */
            try {
                //get csrf cookie
                await api.get("/sanctum/csrf-cookie"); //intialize csrf protection

                //make the login call to the backend
                await api.post("/login", credentials);

                //fetch user data after successful login to update store state
                await this.fetchUser();

                //persist data to localstorage if successful fetch
                if (this.user && this.isAuthenticated) {
                    this.saveUserToStorage();
                }

                //if successfully logged in, redirect to dashboard
                if (this.user && this.isAuthenticated) {
                    router.replace({ name: "Dashboard" });
                }
            } catch (error) {
                this.error = err.response?.data?.message || "Login failed";
            } finally {
                this.loading = false;
            }
        },

        async fetchUser() {
            try {
                const response = await api.get("/user");
                this.user = response.data;
                this.isAuthenticated = true;
                this.error = null;
            } catch (error) {
                this.user = null;
                this.isAuthenticated = false;
                console.error("Failed to fetch user:", error);
                this.logout(); // clear invalid session
            }
        },

        async logout() {
            try {
                await api.post("/logout");
                this.user = null;
                this.isAuthenticated = false;
            } catch (error) {
                console.error("Logout failed:", error);
            } finally {
                localStorage.removeItem("user");
                this.user = null;
                this.error = null;
                this.loading = false;
            }
        },
    },
});
