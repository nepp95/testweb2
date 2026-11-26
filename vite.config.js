import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { google } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/site.css', 'resources/js/site.js', 'resources/css/admin.css', 'resources/js/admin.js'],
            refresh: true,
            fonts: [
                google('Atkinson Hyperlegible', {
                    alias: 'body',
                    weights: [400, 700],
                    styles: ['normal', 'italic'],
                    preload: [{ weight: 400 }, { weight: 700 }],
                    fallbacks: ['system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    optimizedFallbacks: false,
                }),
                google('Fraunces', {
                    alias: 'heading',
                    weights: [600, 700],
                    preload: [{ weight: 700 }],
                    fallbacks: ['Georgia', 'Times New Roman', 'serif'],
                    optimizedFallbacks: false,
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
