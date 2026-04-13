import { createWebHistory, createRouter } from "vue-router";

import Login from "../pages/public/Login.vue";
import Dashboard from "../pages/protected/Dashboard.vue";
import Adminview from "../pages/Admin/Adminview.vue";

const routes = [
    { path: "/", component: Login, name: "Login" },
    {
        path: "/dashboard",
        component: Dashboard,
        name: "Dashboard",
        meta: { requiresAdmin: true },
    },
    {
        path: "/admin-page",
        component: Adminview,
        name: "Adminview",
        meta: { requiresAdmin: true, role: "admin" },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
