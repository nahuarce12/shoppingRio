import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/frontoffice/main.js',
                'resources/js/frontoffice/register.js',
                'resources/js/frontoffice/perfil-admin.js',
                'resources/js/frontoffice/perfil-dueno.js',
                'resources/js/frontoffice/perfil-cliente.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
