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

const routes = [

    { path: "/", component: Login, name: "Login", requiresAuth: false },

    {
        path: '/force-password-change',
        name: 'ForcePasswordChange',
        component: ForcePasswordChange,
        meta: { requiresAuth: true }
    },

    {
        path: "/dashboard",
        component: Dashboard,
        name: "Dashboard",
        meta: { requiresAuth: true },
    },

    {
        path:"/user-profile",
        component: UserProfileView,
        name: "UserProfile",
        meta: { requiresAuth: true}
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
        meta: { requiresAdmin: true, role: "admin" },
    },

    {
        path: "/gym-setting",
        component: SettingsView,
        name: "SettingsView",
        meta: { requiresAdmin: true, role: "admin" },
    },

    {
        path: "/member-management",
        component: MemberManagement,
        name: "MemberManagement",
        meta: { requiresAdmin: true, role: "staff" },
    },
    
    {
        path: "/attendance",
        name: "attendance",
        component: AttendanceDashboard,
        meta: { requiresAdmin: true, role: "staff" },
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

// Add this guard block to secure manual navigation
router.beforeEach((to, from) => {
    const authStore = useAuthStore();
    
    const requiresAuth = to.meta.requiresAuth || to.meta.requiresAdmin;
    const requiredRole = to.meta.role;

    // 1. Not logged in, trying to access protected page
    if (requiresAuth && !authStore.isAuthenticated) {
        return { name: "Login", replace: true };
    }

    // 2. Logged in, trying to access Login page
    if (to.name === "Login" && authStore.isAuthenticated) {
        return { name: "Dashboard", replace:true };
    }

    // 3. Role verification (Admin / Staff checks)
    if (to.meta.requiresAdmin && requiredRole) {
        const userRole = authStore.user?.role?.slug;
        
        // If user object exists but permissions aren't loaded yet, don't boot them out immediately.
        // Let the route load so components can show a loading state while fetchUser resolves.
        if (authStore.user && !userRole) {
            return true; // Allow navigation to continue, component will wait for state
        }

        if (userRole !== requiredRole) {
            return { name: "Dashboard", replace: true };
        }
    }

    return true; 

});

export default router;

