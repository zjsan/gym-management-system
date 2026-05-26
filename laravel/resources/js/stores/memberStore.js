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

        async toggleStatus(id) {
            this.loading = true;
            this.error = null; //clear previous error

            try {
                const res = await api.put(`/members/${id}/toggle-status`);

                // Update the member's status in the local state
                const index = this.members.findIndex((m) => m.id === id);
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

        async fetchMemberAge(id) {
            this.loading = true;
            this.error = null; //clear previous error

            try {
                const res = await api.get(`/members/${id}/age`);
                console.log("Member age fetched successfully:", res.data);
                return res.data.age; // Return the age to the caller
            } catch (err) {
                this.errors =
                    err.response?.data?.errors || "Fetch age error occurred";
                console.error("Failed to fetch member age:", err);
                return null; // Return null if there's an error
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

        async updateMember(id, memberData) {
            this.loading = true;
            this.error = null; //clear previous error

            try {
                const res = await api.put(`/members/${id}`, memberData);

                const index = this.members.findIndex((m) => m.id === id);
                if (index !== -1) {
                    this.members[index] = res.data; //swap the old data from the new data
                }
                console.log("Member updated successfully:", res.data);
                return { success: true };
            } catch (err) {
                this.errors =
                    err.response?.data?.errors || "Update error occurred";
                console.error("Failed to update member:", err);
                return { success: false };
            } finally {
                this.loading = false;
            }
        },
    },
});
