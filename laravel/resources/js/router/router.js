import { createWebHistory, createRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

import Login from "../pages/public/Login.vue";
import Dashboard from "../pages/protected/Main.vue";
import Adminview from "../pages/protected/Admin/Adminview.vue";
import MemberManagement from "../pages/protected/Staff/Staffview.vue";
import AttendanceDashboard from "../pages/protected/Staff/AttendanceDashboard.vue";
import PaymentHistory from "../pages/protected/Payment/PaymentHistory.vue";
import SettingsView from "../pages/protected/Admin/SettingsView.vue";
import DashboardView from "../pages/protected/Dashboard/DashboardView.vue";
import ForcePasswordChange from "../pages/protected/Force Password Change/ForcePasswordChange.vue";
import UserProfileView from "../pages/protected/User Profile/UserProfileView.vue";
import ResetPassword from "../pages/protected/Force Password Change/ResetPasswordView.vue";
import ForgotPassword from "../pages/public/ForgotPassword.vue";

const routes = [
    { path: "/", component: Login, name: "Login", requiresAuth: false },

    {
        path: "/force-password-change",
        name: "ForcePasswordChange",
        component: ForcePasswordChange,
        meta: { requiresAuth: true },
    },

    {
        path: "/forgot-password",
        name: "ForgotPassword",
        component: ForgotPassword,
        meta: { requiresGuest: true, requiresAuth: false },
    },

    {
        path: "/reset-password",
        name: "ResetPassword",
        component: ResetPassword,
        meta: { requiresGuest: true, requiresAuth: false },
    },

    {
        path: "/dashboard",
        component: Dashboard,
        name: "Dashboard",
        meta: { requiresAuth: true },
    },

    {
        path: "/user-profile",
        component: UserProfileView,
        name: "UserProfile",
        meta: { requiresAuth: true },
    },

    {
        path: "/dashboard-view",
        component: DashboardView,
        name: "DashboardView",
        meta: { requiresAuth: true },
    },

    {
        path: "/admin-page",
        component: Adminview,
        name: "Adminview",
        meta: { requiresAdmin: true }, // Admin only
    },

    {
        path: "/gym-setting",
        component: SettingsView,
        name: "SettingsView",
        meta: { requiresAdmin: true }, // Admin only
    },

    {
        path: "/member-management",
        component: MemberManagement,
        name: "MemberManagement",
        meta: { requiresAuth: true, role: "staff" }, // Staff access
    },

    {
        path: "/attendance",
        component: AttendanceDashboard,
        name: "attendance",
        meta: { requiresAuth: true, role: "staff" }, // Staff access
    },

    {
        path: "/payment-history",
        name: "payment",
        component: PaymentHistory,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// secure manual navigation
router.beforeEach((to, from) => {
    const authStore = useAuthStore();

    // 1. Check if route requires any form of authentication
    const requiresAuth = to.meta.requiresAuth || to.meta.requiresAdmin;
    if (requiresAuth && !authStore.isAuthenticated) {
        return { name: "Login", replace: true };
    }

    // 2. Prevent logged-in users from hitting the login page
    if (to.name === "Login" && authStore.isAuthenticated) {
        return { name: "Dashboard", replace: true };
    }

    // 3. Admin-only route protection
    if (to.meta.requiresAdmin) {
        const userRole = authStore.user?.role?.slug;
        if (userRole !== "admin") {
            return { name: "Dashboard", replace: true };
        }
    }

    // 4. Specific Role verification (e.g., staff-only views)
    if (to.meta.role) {
        const userRole = authStore.user?.role?.slug;

        // Allow temporary pass if user object is loading
        if (authStore.user && !userRole) {
            return true;
        }

        if (userRole !== to.meta.role && userRole !== "admin") {
            // Note: Allowing 'admin' to also view staff tools is usually ideal in gyms,
            // but change to strict equality (userRole !== to.meta.role) if admins shouldn't see it.
            return { name: "Dashboard", replace: true };
        }
    }

    return true;
});

export default router;
