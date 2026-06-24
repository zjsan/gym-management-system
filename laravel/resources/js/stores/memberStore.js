import { defineStore } from "pinia";
import api from "../api/api";

export const useMemberStore = defineStore("memberStore", {
    state: () => ({
        members: [],
        loading: false,
        errors: null, // Holds validation arrays or global strings
        currentPage: 1,
        itemsPerPage: 10,
        lastPage: 1, //for disabling next button when on the last page
        totalItems: 0,
        loading: false,
        errors: null,
        currentAbortController: null, // to manage request cancellation
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

            //clear ongoing request before starting new one
            if (this.currentAbortController) {
                this.currentAbortController.abort();
            }

            //intializing a new abort controller for new request and assigning it to the store's state to track it
            const controller = new AbortController();
            this.currentAbortController = controller;

            try {
                const res = await api.get("/members", {
                    params: { page, per_page: perPage, search },
                    signal: controller.signal, //track the local reference of the abort controller for this specific request
                });
                const payload = res.data; //extract response data from the controller
                console.log("API Response:", payload); // Debugging log

                const data = this.unpackResource(res);
                this.members = Array.isArray(data) ? data : [];
            } catch (err) {
                this.handleError(err, "Failed to load members.");
                this.members = [];
            } finally {
                this.loading = false;
            }
        },

        async addMember(memberData) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.post("/members", memberData);
                const newMember = this.unpackResource(res);

                this.members.push(newMember);
                return {
                    success: true,
                };
            } catch (err) {
                const message = this.handleError(err, "Create error occurred");
                return {
                    success: false,
                    message,
                };
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
                return {
                    success: true,
                };
            } catch (err) {
                const message = this.handleError(err, "Update error occurred");
                return {
                    success: false,
                    message,
                };
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
                return {
                    success: true,
                };
            } catch (err) {
                const message = this.handleError(
                    err,
                    "Toggle status error occurred",
                );
                return {
                    success: false,
                    message,
                };
            } finally {
                this.loading = false;
            }
        },

        // Renamed to match the exact method called in the Vue file
        async renewMember(id) {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.put(`/members/${id}/renew`);
                const updatedMember = this.unpackResource(res);

                this.updateLocalState(id, updatedMember);
                return {
                    success: true,
                };
            } catch (err) {
                // FIXED syntax error here
                const message = this.handleError(err, "Renewal error occurred");
                return {
                    success: false,
                    message,
                };
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
                return {
                    success: true,
                };
            } catch (err) {
                const message = this.handleError(
                    err,
                    "Failed to adjust membership dates.",
                );
                return {
                    success: false,
                    message,
                };
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
