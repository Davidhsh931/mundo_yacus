import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js')
        }
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        origin: 'http://192.168.43.22:5173',

        cors: {
            origin: '*',
            methods: ['GET','POST','PUT','DELETE','OPTIONS'],
            allowedHeaders: ['Content-Type','Authorization','X-Requested-With']
        },

        hmr: {
            host: '192.168.43.22'
        }
    },

    optimizeDeps: {
        exclude: ['vue-chartjs', 'chart.js'],
    },
})