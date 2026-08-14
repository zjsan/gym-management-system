import { defineStore } from "pinia";
import api from "../api/api";

export const useAttendanceStore = defineStore("attendance", {
    state: () => ({
        liveFeed: [],
        searchResults: [],
        isLoading: false,
        searchLoading: false,
        errorMessage: null,
        successData: null, // Holds details of the last successful check-in (e.g., photo, name)
        lookupMemberData: null, // NEW: Holds single member profile for verification modal
        lookupLoading: false, // NEW: Dedicated loading state for single member lookup
        qrCodeLoading: false,
    }),

    actions: {
        // 1. Fetch today's live feed
        async fetchLiveFeed() {
            this.isLoading = true;
            try {
                const response = await api.get("/attendance");
                this.liveFeed = response.data;
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.error || "Failed to fetch live feed.";
            } finally {
                this.isLoading = false;
            }
        },


        async searchMembers(query) {
            const term = typeof query === 'string' ? query.trim() : '';
            if (!term) {
                this.searchResults = [];
                return;
            }
            this.searchLoading = true;
            try {
                const response = await api.get(`/attendance/search`, {
                    params: { query: term } // Best practice: use params object instead of manual string interpolation
                });

                // Handle both Laravel API Resource wrapping ({ data: [...] }) and raw arrays
                const responseData = response.data;
                this.searchResults = Array.isArray(responseData) 
                    ? responseData 
                    : (responseData?.data ?? []);
                    
            } catch (error) {
                console.error("Member search failed:", error);
            } finally {
                this.searchLoading = false;
            }
        },

        async lookupMember(query) {
            const term = typeof query === 'string' ? query.trim() : '';
            if (!term) return { success: false };

            this.lookupLoading = true;
            this.errorMessage = null;
            this.lookupMemberData = null;

            try {
                const response = await api.get(`/members/lookup`, {
                    params: { query: term }
                });
                this.lookupMemberData = response.data.data;
                return { success: true, data: response.data.data };
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.message ||
                    error.response?.data?.error ||
                    "Member not found.";
                return { success: false };
            } finally {
                this.lookupLoading = false;
            }
        },

        // 3. Submit a Check-In (QR or Manual)
        async submitCheckIn(payload) {
            this.isLoading = true;
            this.errorMessage = null;
            this.successData = null;

            try {
                const response = await api.post("/attendance", payload);

                // Push the newly created log to the top of the live feed array instantly
                this.liveFeed.unshift(response.data.log);

                // Save success context to display a quick greeting/profile photo in the UI
                this.successData = {
                    name: response.data.name,
                    type: response.data.type,
                    photo_path: response.data.photo_path || null,
                    message: response.data.message,
                };

                return { success: true };
            } catch (error) {
                this.errorMessage =
                    error.response?.data?.error ||
                    "An error occurred during check-in.";

                // If it returns specific data (like an expired member name), keep it for the layout banner
                if (error.response?.data?.member_name) {
                    this.successData = {
                        name: error.response.data.member_name,
                    };
                }

                return { success: false };
            } finally {
                this.isLoading = false;
            }
        },

        // Clear single member verification data
        clearLookup() {
            this.lookupMemberData = null;
            this.errorMessage = null;
        },

        // Clear alert windows / success alerts
        clearStatus() {
            this.errorMessage = null;
            this.successData = null;
        },
    },
});
