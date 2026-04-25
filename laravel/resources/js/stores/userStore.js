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
                const res = await api.get("/users");
                this.users = res.data;
            } catch (err) {
                this.errors = err.response.data;
            } finally {
                this.loading = false;
            }
        },

        async addUser(userData) {
            try {
                const res = await api.post("/users", userData);
                this.users.push(res.data); // Update UI instantly
                console.log("User added successfully:", res.data);
                return { success: true };
            } catch (err) {
                this.errors = err.response.data.errors;
                console.error("Failed to add user:", err);
                return { success: false };
            }
        },

        async updateUser(id, userData){

            this.loading = true;
            try {
                const res = await api.put(`/users/${id}`, userData)

                //find the index of the user
                const index = this.users.findIndex(user => user.id === id);

                //check if it successully found the user
                if(index !== -1){
                    this.user[index] = res.data;//swap the old data from the new data 
                }

                return { success: true };
            } catch (error) {
                this.errors = err.response?.data?.errors;
                return { success: false };
            }
            finally{
                this.loading = false
            }
        }
    },
});
