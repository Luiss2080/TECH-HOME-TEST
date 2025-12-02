import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                // Auth Modules
                "resources/css/auth/login.css",
                "resources/js/auth/login.js",
                "resources/css/auth/register.css",
                "resources/js/auth/register.js",
                "resources/css/auth/forgot-password.css",
                "resources/js/auth/forgot-password.js",
                "resources/css/auth/reset-password.css",
                "resources/css/auth/otp-verification.css",
                // Home Module
                "resources/css/home/welcome.css",
                "resources/js/home/welcome.js",
                // Layout Styles
                "resources/css/layouts/header.css",
                "resources/css/layouts/sidebar.css",
                "resources/css/layouts/footer.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
