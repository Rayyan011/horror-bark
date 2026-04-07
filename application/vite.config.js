import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/panels/theme.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        // Keep production asset names stable and preserve older build artifacts
        // so stale workers can continue to resolve their last manifest during
        // a deploy rollover instead of pointing at deleted files.
        emptyOutDir: false,
        rollupOptions: {
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name][extname]',
            },
        },
    },
});
