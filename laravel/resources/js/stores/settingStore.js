import { defineStore } from "pinia";
import api from "../api/api";

export const useSettingStore = defineStore("setting", {
  state: () => ({
    settings: {
      walkin_daily_fee: 100,
      monthly_membership_fee: 1200,
    },
    loading: false,
    saving: false,
    errors: null,
    successMessage: "",
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
        // Cast returned values to numbers for form inputs
        this.settings = {
          walkin_daily_fee: Number(res.data.walkin_daily_fee ?? 100),
          monthly_membership_fee: Number(res.data.monthly_membership_fee ?? 1200),
        };
        return { success: true };
      } catch (err) {
        console.error("Failed to load gym settings:", err);
        this.errors = err.response?.data?.message || "Failed to load gym settings.";
        return { success: false };
      } finally {
        this.loading = false;
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
        this.successMessage = res.data.message || "Gym settings updated successfully.";
        
        // Optimistically update local store state
        this.settings = { ...this.settings, ...payload };
        return { success: true };
      } catch (err) {
        if (err.response?.status === 422) {
          this.errors = err.response.data.errors;
        } else if (err.response?.status === 403) {
          this.errors = "Unauthorized. Admin privilege required.";
        } else {
          this.errors = err.response?.data?.message || "Failed to update gym settings.";
        }
        return { success: false };
      } finally {
        this.saving = false;
      }
    },
  },
});