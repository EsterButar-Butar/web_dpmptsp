import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [

                // Global
                'resources/css/app.css',
                'resources/js/app.js',

                // Landing
                'resources/css/home.css',
                'resources/css/about.css',
                'resources/css/navbar.css',
                'resources/css/footer.css',
                'resources/css/map.css',

                // Dashboard
                'resources/css/analysis.css',
                'resources/css/comparison.css',
                'resources/css/user.css',
                'resources/css/auth.css',

                // Javascript
                'resources/js/navbar.js',
                'resources/js/home.js',
                'resources/js/about.js',
                'resources/js/analysis.js',
                'resources/js/comparison.js',
                'resources/js/map.js',

            ],
            refresh: true,
        }),
    ],
});