import { defineStore } from "pinia";
import api from "../api/api";

export const useMemberStore = defineStore("memberStore", {
    state: () => ({
        members: [],
        loading: false,
        errors: null, // Holds validation arrays or global strings
        currentPage: 1,
        itemsPerPage: 10,
        lastPage: 1, // For disabling next button when on the last page
        totalItems: 0,
        currentAbortController: null, // To manage request cancellation
    }),

    actions: {
        // Centralized helper to extract a resource uniformly
        unpackResource(response) {
            return (
                response.data?.data || response.data?.member || response.data
            );
        },

        // Centralized error handler to map backend anomalies cleanly
        handleError(err, fallbackMessage) {
            console.error(`Store Error [${fallbackMessage}]:`, err);
            this.errors = err.response?.data?.errors || {
                message: err.response?.data?.message || fallbackMessage,
            };
            return err.response?.data?.message || fallbackMessage;
        },

        async fetchMembers(page = 1, perPage = 10, search = "") {
            this.loading = true;
            this.errors = null;

            // Clear ongoing request before starting a new one
            if (this.currentAbortController) {
                this.currentAbortController.abort();
            }

            // Initializing a new abort controller for the new request
            const controller = new AbortController();
            this.currentAbortController = controller;

            try {
                const res = await api.get("/members", {
                    params: { page, per_page: perPage, search },
                    signal: controller.signal,
                });

                const payload = res.data;
                console.log("API Response:", payload); // Debugging log

                // Update only if the request wasn't aborted
                if (!controller.signal.aborted) {
                    this.members = payload.data || [];

                    // Update pagination info
                    this.currentPage = payload.meta?.current_page || page;
                    this.itemsPerPage = payload.meta?.per_page || perPage;
                    this.lastPage = payload.meta?.last_page || 1;
                    this.totalItems = payload.meta?.total || 0;

                    // Clean up controller reference on successful completion
                    if (this.currentAbortController === controller) {
                        this.currentAbortController = null;
                    }
                }
            } catch (err) {
                // FIXED: Changed 'error' references to 'err' matching catch signature
                if (
                    err.name === "AbortError" ||
                    err.name === "CanceledError" ||
                    api.isCancel?.(err)
                ) {
                    console.log("Members fetch request safely aborted.");
                    return; // Graceful exit
                }
                
                // Handle backend/network errors
                const errMsg = err.response?.data?.message || "Failed to load members.";
                this.errors = errMsg;
                throw err;
            } finally {
                // Only set loading to false if this is the active request tracking execution
                if (
                    this.currentAbortController === controller ||
                    this.currentAbortController === null
                ) {
                    this.loading = false;
                }
            }
        },

        async addMember(memberData) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.post("/members", memberData);
                const newMember = this.unpackResource(res);

                this.members.push(newMember);
                return { success: true };
            } catch (err) {
                const message = this.handleError(err, "Create error occurred");
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },

        async updateMember(id, memberData) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.put(`/members/${id}`, memberData);
                const updatedMember = this.unpackResource(res);

                this.updateLocalState(id, updatedMember);
                return { success: true };
            } catch (err) {
                const message = this.handleError(err, "Update error occurred");
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },

        async toggleStatus(id) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.put(`/members/${id}/toggle-status`);
                const updatedMember = this.unpackResource(res);

                this.updateLocalState(id, updatedMember);
                return { success: true };
            } catch (err) {
                const message = this.handleError(err, "Toggle status error occurred");
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },

        async renewMember(id) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.put(`/members/${id}/renew`);
                const updatedMember = this.unpackResource(res);

                this.updateLocalState(id, updatedMember);
                return { success: true };
            } catch (err) {
                const message = this.handleError(err, "Renewal error occurred");
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },

        async adjustMemberDays(id, daysCount) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.put(`/members/${id}/adjust-days`, {
                    days: daysCount,
                });
                const updatedMember = this.unpackResource(res);

                this.updateLocalState(id, updatedMember);
                return { success: true };
            } catch (err) {
                const message = this.handleError(err, "Failed to adjust membership dates.");
                return { success: false, message };
            } finally {
                this.loading = false;
            }
        },

        updateLocalState(id, updatedMember) {
            const index = this.members.findIndex((m) => m.id === id);
            if (index !== -1) {
                this.members.splice(index, 1, updatedMember);
            }
        },
    },
});