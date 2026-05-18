import { defineStore } from "pinia";
import api from "../api/api";

export const useMemberStore = defineStore("memberStore", {
    state: () => ({
        members: [],
        loading: false,
        errors: null,
    }),
    actions: {
        async fetchMembers() {
            this.loading = true;
            try {
                const res = await api.get("/members");
                this.members = Array.isArray(res.data)
                    ? res.data
                    : res.data.data || [];
            } catch (err) {
                this.errors =
                    err.response?.data?.errors || "fetch error occurred";
                console.log("Fetch error failed: ", err);
                this.members = [];
            } finally {
                this.loading = false;
            }
        },

        async toggleStatus(member) {
            this.loading = true;
            this.error = null; //clear previous error

            try {
                const res = await api.put(
                    `/members/${member.id}/toggle-status`,
                );

                // Update the member's status in the local state
                const index = this.members.findIndex((m) => m.id === member.id);
                if (index !== -1) {
                    this.members[index] = res.data.member;
                }
                console.log("Member status toggled successfully:", res.data);
            } catch (err) {
                this.errors =
                    err.response?.data?.errors ||
                    "Toggle status error occurred";
                console.error("Failed to toggle member status:", err);
            } finally {
                this.loading = false;
            }
        },

        async addMember(memberData) {
            this.loading = true;
            this.error = null; //clear previous error

            try {
                const res = await api.post("/members", memberData);
                this.members.unshift(res.data); //newest member appears at the top of the table
                console.log("Member added successfully:", res.data);
                return { success: true };
            } catch (err) {
                this.errors =
                    err.response?.data?.errors || "Create error occurred";
                console.error("Failed to add member:", err);
                return { success: false };
            } finally {
                this.loading = false;
            }
        },

        // async updateUser(id, userData) {
        //     this.loading = true;
        //     try {
        //         const res = await api.put(`/members/${id}`, userData);

        //         //find the index of the user
        //         const index = this.users.findIndex((user) => user.id === id);

        //         //check if it successully found the user
        //         if (index !== -1) {
        //             this.users[index] = res.data; //swap the old data from the new data
        //         }

        //         return { success: true };
        //     } catch (error) {
        //         this.errors = error.response?.data?.errors;
        //         return { success: false };
        //     } finally {
        //         this.loading = false;
        //     }
        // },

        // async deleteUser(id) {
        //     if (!confirm("Are you sure?")) {
        //         return;
        //     }

        //     try {
        //         await api.delete(`/users/${id}`);

        //         //remove user from local state
        //         this.users = this.users.filter((user) => user.id !== id);
        //     } catch (error) {
        //         console.log(error);
        //         alert("Failed to delete user");
        //     }
        // },
    },
});
