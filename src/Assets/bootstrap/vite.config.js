import { defineConfig } from 'vite';
import path from 'path';

export default defineConfig(({ mode }) => ({
    root: path.resolve(__dirname, 'src'),
    build: {
        outDir: path.resolve(__dirname, '../../../public/themes/bootstrap'),
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: {
                main: path.resolve(__dirname, 'src', 'main.js'),
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: (assetInfo) => {
                    // Organize assets by type
                    if (assetInfo.name && /\.(png|jpe?g|gif|svg|webp|avif)$/i.test(assetInfo.name)) {
                        return 'assets/images/[name].[ext]';
                    }
                    // Font files go to fonts directory
                    if (assetInfo.name && /\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return 'assets/fonts/[name].[ext]';
                    }
                    return 'assets/[name].[ext]';
                }
            }
        }
    },
    server: {
        port: 3001
    }
}));
