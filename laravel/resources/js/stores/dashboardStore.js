import { defineStore } from "pinia";
import api from "../api/api";

export const useDashboardStore = defineStore("dashboard", {
    state: () => ({
        loading: false,
        metrics: {
            todayRevenue: 0,
            monthlyRevenue: 0,
            activeMembers: 0,
            expiredMembers: 0,
            todayAttendance: 0,
        },
        revenueChartData: [],
        attendanceTrendData: [],
        hourlyAttendanceData: [],
        visitorRatioData: {
            members: 0,
            walkins: 0,
        },
        expirationWatchlist: {
            expiring_soon: 0,
            recently_expired: 0,
        },
        recentTransactions: [],
        error: null,
    }),

    actions: {
        async fetchOverview() {
            this.loading = true;
            this.error = null;
            try {
                const res = await api.get("/dashboard/overview");
                const data = res.data;

                this.metrics = {
                    todayRevenue: Number(data.metrics?.today_revenue ?? 0),
                    monthlyRevenue: Number(data.metrics?.monthly_revenue ?? 0),
                    activeMembers: Number(data.metrics?.active_members ?? 0),
                    expiredMembers: Number(data.metrics?.expired_members ?? 0),
                    todayAttendance: Number(
                        data.metrics?.today_attendance ?? 0,
                    ),
                };

                this.revenueChartData = data.revenue_chart || [];
                this.attendanceTrendData = data.attendance_trends || [];

                this.hourlyAttendanceData = data.hourly_attendance || [];
                this.visitorRatioData = data.visitor_ratio || {
                    members: 0,
                    walkins: 0,
                };
                this.expirationWatchlist = data.expiration_watchlist || {
                    expiring_soon: 0,
                    recently_expired: 0,
                };

                this.recentTransactions = data.recent_transactions || [];
            } catch (err) {
                this.error =
                    err?.response?.data?.message ||
                    "Failed to load dashboard metrics.";
                console.error("[DashboardStore Error]:", err);
            } finally {
                this.loading = false;
            }
        },
    },
});
