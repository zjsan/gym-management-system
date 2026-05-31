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
            this.errors = null; //clear previous error

            try {
                const res = await api.put(`/members/${id}/toggle-status`);

                const updatedMember = res.data.member || res.data.data || res.data;

                // Update the member's status in the local state
                const index = this.members.findIndex((m) => m.id === id);
                if (index !== -1) {
                    this.members.splice(index, 1, updatedMember);
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

        async renewMembership(id){
            this.loading = true;
            this.errors = false;

            try{
                const res = await api.put(`/members/${id}/renew`);
                const updatedMember = res.data.member || res.data; 

                this.updateLocalState(id, updatedMember);
                return { success: true };
            }
            catch{
                const msg = err.response?.data?.error || "Renewal error occurred";
                return { success: false, message: msg };
            }
            finally{
                this.loading = false;
            }
        },

        async adjustMemberDays(id, daysCount) {
            this.loading = true;
            this.errors = false;

            try {
                const res = await api.put(`/members/${id}/adjust-days`, { days: daysCount });
                const updatedMember = res.data.member || res.data;
                
                this.updateLocalState(id, updatedMember);
                return { success: true };
            } catch (err) {
                return { success: false, message: "Failed to adjust membership dates." };
            } finally {
                this.loading = false;
            }
        },

        async addMember(memberData) {
            this.loading = true;
            this.errors = null; //clear previous error

            try {
                const res = await api.post("/members", memberData);

                //Fallback chain to catch Laravel's response data structure
                const newMember = res.data.member || res.data.data || res.data;

                this.members.unshift(newMember);
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
            this.errors = null; //clear previous error

            try {
                const res = await api.put(`/members/${id}`, memberData);

                // Safely unpack the updated member resource
                const updatedMember = res.data.member || res.data.data || res.data;

                const index = this.members.findIndex((m) => m.id === id);
                if (index !== -1) {
                    this.members.splice(index, 1, updatedMember); //swap the old data from the new data
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
