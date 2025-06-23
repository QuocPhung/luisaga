import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',       // Quan trọng: cho phép truy cập từ ngoài
        port: 5173,            // Có thể đổi nếu bị chiếm
        strictPort: true,      // Báo lỗi nếu port bị chiếm
    },
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
        ]),
    ],
});
