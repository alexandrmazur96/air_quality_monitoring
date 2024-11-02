import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import dotenv from "dotenv";
import path from 'path';

dotenv.config();

export default defineConfig({
    mode: process.env.NODE_ENV,
    plugins: [
        vue({
            isProduction: process.env.NODE_ENV === 'production',
        }),
        laravel({
            input: ['resources/scss/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            '@/': path.resolve(__dirname, 'resources/js'),
            'images': path.resolve(__dirname, 'resources/images'),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
            },
        },
    },
});
