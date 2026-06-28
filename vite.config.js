import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/crm.css',
                'resources/js/app.js',
                'resources/js/crm.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
