import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { resolve } from "path";

// Función para obtener dinámicamente todos los archivos CSS y JS de módulos
function getModuleEntries() {
    const entries = [
        // Archivos principales
        "resources/css/app.css",
        "resources/js/app.js",
    ];

    // Archivos globales y compartidos
    const globalFiles = [
        "resources/css/shared/globals.css",
        "resources/css/shared/variables.css",
        "resources/css/shared/utilities.css",
        "resources/css/components/buttons.css",
        "resources/css/components/forms.css",
        "resources/css/components/tables.css",
        "resources/css/components/modals.css",
        "resources/css/components/cards.css",
        "resources/css/layouts/header.css",
        "resources/css/layouts/sidebar.css",
        "resources/css/layouts/footer.css",
        "resources/js/shared/config.js",
        "resources/js/utils/helpers.js",
        "resources/js/utils/api.js",
        "resources/js/utils/validators.js",
        "resources/js/components/modal.js",
        "resources/js/components/form-validator.js",
        "resources/js/components/datatable.js",
    ];

    entries.push(...globalFiles);

    // Módulos específicos - cada módulo tendrá archivos por vista
    const modules = [
        'admin', 'auth', 'categorias', 'configurations', 'cursos', 
        'docente', 'enrollments', 'estudiantes', 'home', 'laboratorios', 
        'libros', 'materiales', 'security'
    ];

    modules.forEach(module => {
        // CSS por módulo
        entries.push(`resources/css/${module}/index.css`);
        entries.push(`resources/css/${module}/dashboard.css`);
        entries.push(`resources/css/${module}/crear.css`);
        entries.push(`resources/css/${module}/editar.css`);
        entries.push(`resources/css/${module}/ver.css`);
        
        // JS por módulo
        entries.push(`resources/js/${module}/index.js`);
        entries.push(`resources/js/${module}/dashboard.js`);
        entries.push(`resources/js/${module}/crear.js`);
        entries.push(`resources/js/${module}/editar.js`);
        entries.push(`resources/js/${module}/ver.js`);
    });

    // Filtrar solo archivos que existen
    return entries.filter(entry => {
        try {
            const fs = require('fs');
            return fs.existsSync(entry);
        } catch {
            return false;
        }
    });
}

export default defineConfig({
    plugins: [
        laravel({
            input: getModuleEntries(),
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': resolve(__dirname, 'resources'),
            '@css': resolve(__dirname, 'resources/css'),
            '@js': resolve(__dirname, 'resources/js'),
            '@components': resolve(__dirname, 'resources/js/components'),
            '@utils': resolve(__dirname, 'resources/js/utils'),
            '@shared': resolve(__dirname, 'resources/js/shared'),
        }
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    // Agrupar dependencias comunes
                    'vendor': ['lodash', 'axios'],
                    'auth': [
                        'resources/js/auth/login.js',
                        'resources/js/auth/register.js',
                        'resources/js/auth/forgot-password.js'
                    ],
                    'shared': [
                        'resources/js/shared/config.js',
                        'resources/js/utils/helpers.js',
                        'resources/js/utils/api.js'
                    ],
                    'components': [
                        'resources/js/components/modal.js',
                        'resources/js/components/form-validator.js',
                        'resources/js/components/datatable.js'
                    ]
                }
            }
        },
        outDir: 'public/build',
        emptyOutDir: true,
        sourcemap: process.env.APP_ENV === 'local',
    },
    server: {
        hmr: {
            host: 'localhost',
        },
        watch: {
            usePolling: true,
        }
    }
});
