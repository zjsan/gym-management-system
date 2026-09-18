import { defineStore } from "pinia";
import api from "../api/api"; // Import Axios instance

export const usePaymentStore = defineStore("payment", {
    state: () => ({
        payments: [],
        summaryData: {
            today_revenue: 0,
            week_revenue: 0,
            month_revenue: 0,
            month_breakdown: [],
        },

        // Sales Summary Printable Report State
        salesReportData: null,
        salesReportLoading: false,
        
        loading: false,
        summaryLoading: false,
        errors: null,

        // Pagination & Filtering State
        currentPage: 1,
        itemsPerPage: 15,
        lastPage: 1,
        totalItems: 0,
        filters: {
            search: "",
            category: "",
            start_date: "",
            end_date: "",
        },
    }),

    actions: {
        /**
         * Fetch paginated payments with search and filter parameters
         */
        async fetchPayments(page = 1) {
            this.loading = true;
            this.errors = null;
            this.currentPage = page;

            try {
                const params = {
                    page: this.currentPage,
                    per_page: this.itemsPerPage,
                    ...this.filters,
                };

                const res = await api.get("/payments", { params });
                const paginatedData = res.data.payments;

                this.payments = paginatedData.data;
                this.currentPage = paginatedData.current_page;
                this.lastPage = paginatedData.last_page;
                this.totalItems = paginatedData.total;

                return { success: true };
            } catch (err) {
                this.errors =
                    err.response?.data?.message ||
                    "Failed to load payments history.";
                return { success: false, message: this.errors };
            } finally {
                this.loading = false;
            }
        },

        /**
         * Fetch financial metrics for dashboard summary cards
         */
        async fetchSummary() {
            this.summaryLoading = true;
            try {
                const res = await api.get("/payments/summary");
                if (res.data.success) {
                    this.summaryData = res.data.data;
                }
                return { success: true };
            } catch (err) {
                console.error("Failed to load payment summary metrics:", err);
                return { success: false };
            } finally {
                this.summaryLoading = false;
            }
        },

        /**
         * Trigger the export of payment ledger data to CSV based on current filters
         *  and initiate download in browser
         */
        async exportPayments() {
            this.loading = true;
            try {
                const params = new URLSearchParams(this.filters).toString();
                const response = await api.get(`/payments/export?${params}`, {
                    responseType: "blob", // Crucial for file downloads
                });

                // Create a download link in browser
                const url = window.URL.createObjectURL(
                    new Blob([response.data]),
                );
                const link = document.createElement("a");
                link.href = url;
                link.setAttribute(
                    "download",
                    `payment_ledger_${new Date().toISOString().slice(0, 10)}.csv`,
                );
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);
            } catch (err) {
                this.errors =
                    err.response?.data?.message ||
                    "Failed to export payment ledger.";
                console.error("Failed to export payment ledger:", err);
            } finally {
                this.loading = false;
            }
        },

        /**
         * Reset active filters back to initial state
         */
        resetFilters() {
            this.filters = {
                search: "",
                category: "",
                start_date: "",
                end_date: "",
            };
            this.fetchPayments(1);
        },
    },
});
