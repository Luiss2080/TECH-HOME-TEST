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
                "resources/css/modulos/auth/login.css",
                "resources/js/modulos/auth/login.js",
                "resources/css/modulos/auth/register.css",
                "resources/js/modulos/auth/register.js",
                "resources/css/modulos/auth/forgot-password.css",
                "resources/js/modulos/auth/forgot-password.js",
                "resources/css/modulos/auth/reset-password.css",
                "resources/js/modulos/auth/reset-password.js",
                "resources/css/modulos/auth/otp-verification.css",
                "resources/js/modulos/auth/otp-verification.js",
                // Home Module
                "resources/css/modulos/home/welcome.css",
                // Layout Styles
                "resources/css/layout/header.css",
                "resources/css/layout/sidebar.css",
                "resources/css/layout/footer.css",
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
