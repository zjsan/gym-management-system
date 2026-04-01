import { createWebHashHistory, createRouter } from "vue-router";

import Login from "../pages/public/Login.vue";
import Dashboard from "../pages/protected/Dashboard.vue";

const routes = [
    { path: "/", component: Login, name: "Login" },
    { path: "/dashboard", component: Dashboard, name: "Dashboard" },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});
