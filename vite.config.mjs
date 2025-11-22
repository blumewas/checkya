import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/index.ts', 'resources/css/main.css'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
