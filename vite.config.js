import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { resolve } from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Archivos principales
                "resources/css/app.css",
                "resources/js/app.js",
                // Archivos compartidos
                "resources/css/shared/globals.css",
                "resources/css/shared/variables.css", 
                "resources/css/shared/utilities.css"
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
            '@css': resolve(__dirname, 'resources/css'),
            '@js': resolve(__dirname, 'resources/js'),
        }
    }
});
