import { createWebHistory, createRouter } from "vue-router";

import Login from "../pages/public/Login.vue";
import Dashboard from "../pages/protected/Main.vue";
import Adminview from "../pages/protected/Admin/Adminview.vue";
import MemberManagement from "../pages/protected/Staff/Staffview.vue";
import AttendanceDashboard from "../pages/protected/Staff/AttendanceDashboard.vue";

const routes = [
    { path: "/", component: Login, name: "Login", requiresAuth: false },
    {
        path: "/dashboard",
        component: Dashboard,
        name: "Dashboard",
        meta: { requiresAuth: true },
    },
    {
        path: "/admin-page",
        component: Adminview,
        name: "Adminview",
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
        name: "attendance.dashboard",
        component: AttendanceDashboard,
        meta: { requiresAdmin: true, role: "staff" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
