import { defineConfig } from 'vite';
import fs from 'fs';
import path from 'path';

const hotFile = path.resolve(import.meta.dirname, 'public/hot');

/**
 * Пишет адрес dev-сервера в public/hot, пока идёт vite dev.
 * Хелпер vite() в php смотрит на этот файл и решает, откуда брать assets.
 */
function hotFilePlugin() {
    return {
        name: 'motor-hot-file',
        apply: 'serve',
        configureServer(server) {
            server.httpServer?.once('listening', () => {
                const address = server.resolvedUrls?.local?.[0]?.replace(/\/$/, '');

                if (address) {
                    fs.writeFileSync(hotFile, address);
                }
            });

            const clean = () => fs.existsSync(hotFile) && fs.unlinkSync(hotFile);

            process.on('exit', clean);
            process.on('SIGINT', () => { clean(); process.exit(); });
            process.on('SIGTERM', () => { clean(); process.exit(); });
        },
    };
}

export default defineConfig({
    base: '/build/',
    // Копировать public/ никуда не нужно, он и так отдаётся веб-сервером
    publicDir: false,
    plugins: [hotFilePlugin()],
    server: {
        // Страницы отдаёт php, vite отдаёт только assets — иначе браузер
        // не увидит горячую замену с другого порта
        cors: true,
    },
    build: {
        outDir: 'public/build',
        emptyOutDir: true,
        manifest: 'manifest.json',
        rollupOptions: {
            input: 'resources/js/app.js',
        },
    },
});
