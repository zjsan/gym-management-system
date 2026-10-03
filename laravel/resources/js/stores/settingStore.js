import { defineStore } from "pinia";
import api from "../api/api";

export const useSettingStore = defineStore("setting", {
    state: () => ({
        settings: {
            walkin_daily_fee: 100,
            monthly_membership_fee: 1200,
        },
        loading: false,
        // --- ADDED / UPDATED STATE ---
        loadingHistory: false,
        saving: false,
        errors: null,
        successMessage: "",
        history: [],
        pagination: {
            current_page: 1,
            last_page: 1,
            total: 0,
        },
        // ------------------------------
    }),

    actions: {
        /**
         * Fetch all gym configuration settings
         */
        async fetchSettings() {
            this.loading = true;
            this.errors = null;
            try {
                const res = await api.get("/gym-settings");
                
                // Handle both direct JSON and wrapped res.data / res.data.data responses
                const data = res.data.data || res.data;

                this.settings = {
                    walkin_daily_fee: Number(data.walkin_daily_fee ?? 100),
                    monthly_membership_fee: Number(data.monthly_membership_fee ?? 1200),
                };

                return { success: true, data: this.settings };
            } catch (err) {
                console.error("Failed to load gym settings:", err);
                this.errors =
                    err.response?.data?.message ||
                    "Failed to load gym settings.";
                return { success: false };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Fetch all gym configuration settings change history
         */
        async fetchAuditHistory(page = 1) {
            this.loadingHistory = true;
            try {
                const res = await api.get(`/gym-settings/history?page=${page}`);

                const responseData = res.data;
                const records = responseData.data?.data || responseData.data || [];
                const meta = responseData.meta || responseData;

                // Assign record list
                this.history = records;

                // Populate root-level properties required by usePagination
                this.currentPage = Number(meta.current_page ?? page);
                this.lastPage = Number(meta.last_page ?? 1);
                this.itemsPerPage = Number(meta.per_page ?? 10); // Fixes the NaN issue
                this.totalItems = Number(meta.total ?? records.length);

            } catch (err) {
                console.error("Failed to load settings history:", err);
            } finally {
                this.loadingHistory = false;
            }
        },

        /**
         * Update gym fees and rates (Admin only)
         */
        async updateSettings(payload) {
            this.saving = true;
            this.errors = null;
            this.successMessage = "";
            try {
                const res = await api.put("/gym-settings", payload);
                this.successMessage =
                    res.data.message || "Gym settings updated successfully.";

                // Optimistically update local store state
                this.settings = { ...this.settings, ...payload };

                // --- ADDED: Auto refresh audit logs after successful rate update ---
                await this.fetchAuditHistory(1);
                // ------------------------------------------------------------------

                return { success: true };
            } catch (err) {
                if (err.response?.status === 422) {
                    this.errors = err.response.data.errors;
                } else if (err.response?.status === 403) {
                    this.errors = "Unauthorized. Admin privilege required.";
                } else {
                    this.errors =
                        err.response?.data?.message ||
                        "Failed to update gym settings.";
                }
                return { success: false };
            } finally {
                this.saving = false;
            }
        },
    },
});
