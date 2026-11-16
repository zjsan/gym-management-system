import { createWebHistory, createRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";

// Layouts
import AppLayout from "@/pages/private/AppLayout.vue";

// Public Pages
import Login from "@/pages/public/Login.vue";
import ForgotPassword from "@/pages/public/ForgotPassword.vue";
import ResetPassword from "@/pages/private/Force Password Change/ResetPasswordView.vue";

// Protected Pages
import DashboardView from "@/pages/private/Dashboard/DashboardView.vue";
import Adminview from "@/pages/private/Admin/Adminview.vue";
import MemberManagement from "@/pages/private/Staff/Staffview.vue";
import AttendanceDashboard from "@/pages/private/Staff/AttendanceDashboard.vue";
import PaymentHistory from "@/pages/private/Payment/PaymentHistory.vue";
import SettingsView from "@/pages/private/Admin/SettingsView.vue";
import ForcePasswordChange from "@/pages/private/Force Password Change/ForcePasswordChange.vue";
import UserProfileView from "@/pages/private/User Profile/UserProfileView.vue";
import Notfound from "@/pages/views/NotFound.vue";

const routes = [
    // Public / Guest Routes
    {
        path: "/",
        component: Login,
        name: "Login",
        meta: { requiresGuest: true },
    },
    {
        path: "/forgot-password",
        component: ForgotPassword,
        name: "ForgotPassword",
        meta: { requiresGuest: true },
    },
    {
        path: "/reset-password",
        component: ResetPassword,
        name: "ResetPassword",
        meta: { requiresGuest: true },
        beforeEnter: (to) => {
            if (!to.query.token || !to.query.email) {
                return {
                    name: "ForgotPassword",
                    query: { error: "Invalid or missing reset link." },
                };
            }
        },
    },

    // Standalone Protected Route (No Sidebar/Header)
    {
        path: "/force-password-change",
        name: "ForcePasswordChange",
        component: ForcePasswordChange,
        meta: { requiresAuth: true },
    },

    // Protected App Shell Routes (Shared Sidebar & Header)
    {
        path: "/",
        component: AppLayout,
        meta: { requiresAuth: true },
        children: [
            { path: "dashboard", name: "Dashboard", component: DashboardView },
            {
                path: "user-profile",
                name: "UserProfile",
                component: UserProfileView,
            },
            {
                path: "member-management",
                name: "MemberManagement",
                component: MemberManagement,
                meta: { role: "staff" },
            },
            {
                path: "attendance",
                name: "attendance",
                component: AttendanceDashboard,
                meta: { role: "staff" },
            },
            {
                path: "payment-history",
                name: "payment",
                component: PaymentHistory,
            },
            {
                path: "admin-page",
                name: "Adminview",
                component: Adminview,
                meta: { requiresAdmin: true },
            },
            {
                path: "gym-setting",
                name: "SettingsView",
                component: SettingsView,
                meta: { requiresAdmin: true },
            },
        ],
    },

    // Catch-All 404
    {
        path: "/:pathMatch(.*)*",
        name: "NotFound",
        component: Notfound,
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// secure manual navigation
router.beforeEach((to, from) => {
    const authStore = useAuthStore();

    // Prevent authenticated users from visiting guest-only routes
    if (to.meta.requiresGuest && authStore.isAuthenticated) {
        return { name: "Dashboard", replace: true };
    }

    // Enforce Force Password Change if user is logged in with a temporary password flag
    const mustChangePassword = authStore.user?.must_change_password;

    if (authStore.isAuthenticated && mustChangePassword) {
        if (to.name !== "ForcePasswordChange") {
            return { name: "ForcePasswordChange", replace: true };
        }
    } else if (
        to.name === "ForcePasswordChange" &&
        authStore.isAuthenticated &&
        !mustChangePassword
    ) {
        // If user try to hit force-password-change when they already updated it, bounce them back
        return { name: "Dashboard", replace: true };
    }

    // Check if route requires any form of authentication
    const requiresAuth = to.meta.requiresAuth || to.meta.requiresAdmin;
    if (requiresAuth && !authStore.isAuthenticated) {
        return { name: "Login", replace: true };
    }

    // Admin-only route protection
    if (to.meta.requiresAdmin) {
        // Allow pass-through if user exists but role isn't loaded yet
        if (authStore.isAuthenticated && !authStore.user?.role) {
            return true;
        }

        const userRole = authStore.user?.role?.slug;
        if (userRole && userRole !== "admin") {
            return { name: "Dashboard", replace: true };
        }
    }

    //  Specific Role verification staff-only views
    if (to.meta.role) {
        const userRole = authStore.user?.role?.slug;

        if (authStore.isAuthenticated && !userRole) {
            return true;
        }

        if (userRole !== to.meta.role) {
            return { name: "Dashboard", replace: true };
        }
    }

    return true;
});

export default router;
