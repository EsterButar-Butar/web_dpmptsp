import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', 
                'resources/js/app.js',
                'resources/css/home.css',
                'resources/css/navbar.css',
                'resources/css/about.css',
                'resources/css/analysis.css',
                'resources/css/comparison.css',
                'resources/js/navbar.js',
                'resources/js/home.js',
                'resources/js/about.js',
                'resources/js/analysis.js',
                'resources/js/comparison.js'
            ],
            refresh: true,
        }),
    ],
});
