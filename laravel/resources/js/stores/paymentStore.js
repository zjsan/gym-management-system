import { defineStore } from 'pinia';
import api from "../api/api"; // Import your Axios instance

export const usePaymentStore = defineStore('payment', {
    state: () => ({
        payments: [],
        summaryData: {
            today_revenue: 0,
            week_revenue: 0,
            month_revenue: 0,
            month_breakdown: [],
        },
        loading: false,
        summaryLoading: false,
        errors: null,
        
        // Pagination & Filtering State
        currentPage: 1,
        itemsPerPage: 15,
        lastPage: 1,
        totalItems: 0,
        filters: {
            search: '',
            category: '',
            start_date: '',
            end_date: '',
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

                const res = await api.get('/payments', { params });
                const paginatedData = res.data.payments;

                this.payments = paginatedData.data;
                this.currentPage = paginatedData.current_page;
                this.lastPage = paginatedData.last_page;
                this.totalItems = paginatedData.total;

                return { success: true };
            } catch (err) {
                this.errors = err.response?.data?.message || 'Failed to load payments history.';
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
                const res = await api.get('/payments/summary');
                if (res.data.success) {
                    this.summaryData = res.data.data;
                }
                return { success: true };
            } catch (err) {
                console.error('Failed to load payment summary metrics:', err);
                return { success: false };
            } finally {
                this.summaryLoading = false;
            }
        },

        /**
         * Reset active filters back to initial state
         */
        resetFilters() {
            this.filters = {
                search: '',
                category: '',
                start_date: '',
                end_date: '',
            };
            this.fetchPayments(1);
        },
    },
});