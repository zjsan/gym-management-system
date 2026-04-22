import { defineStore } from "pinia";
import api from "../api/api";

export const useUserStore = defineStore("userStore", {
    state: () => ({
        users: [],
        loading: false,
        errors: null,
    }),
    actions: {
        async fetchUsers() {
            this.loading = true;
            try {
                const res = await axios.get("/api/users");
                this.users = res.data;
            } catch (err) {
                this.errors = err.response.data;
            } finally {
                this.loading = false;
            }
        },

        async addUser(userData) {
            try {
                const res = await axios.post("/api/users", userData);
                this.users.push(res.data); // Update UI instantly
                console.log("User added successfully:", res.data);
                return { success: true };
            } catch (err) {
                this.errors = err.response.data.errors;
                console.error("Failed to add user:", err);
                return { success: false };
            }
        },
    },
});
