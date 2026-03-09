import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'; // <--- ESTA LÍNEA ES VITAL

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(), // <--- ESTA OTRA TAMBIÉN
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        cors:true,
        origin: 'http://192.168.43.22:5173',
        hmr: {
            host: '192.168.43.22',
        },
    },
});