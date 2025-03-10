import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

// This is a special configuration file for deployment
// It uses ES Module syntax for compatibility with newer Vite versions on deployment platforms

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                }
            }
        })
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        }
    },
    build: {
        // Ensure proper build settings for production
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: undefined
            }
        }
    },
    server: {
        watch: {
            ignored: ['**/vendor/**', '**/node_modules/**', '**/.git/**', '**/.heroku/**'],
            followSymlinks: false
        }
    }
});
