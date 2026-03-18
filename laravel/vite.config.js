import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        vue(),
        tailwindcss(),
    ],
    server: {
        host: "0.0.0.0", // Allow connections from outside the container
        port: 5173, // Default Vite port
        strictPort: true, // Fail if the port is already in use
        hmr: {
            host: "localhost",
        },
    },
});
