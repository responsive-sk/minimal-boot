import { defineConfig } from 'vite';
import path from 'path';
export default defineConfig(({ mode }) => ({
    root: path.resolve(__dirname, 'src'),
    build: {
        outDir: path.resolve(__dirname, '../../../public/themes/main'),
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
                  // Optimize image file names for better caching
                    if (assetInfo.name && /\.(png|jpe?g|gif|svg|webp|avif)$/i.test(assetInfo.name)) {
                        return 'assets/images/[name].[ext]';
                    }
                    return 'assets/[name].[ext]';
                }
            }
        },
      // Image optimization settings
        assetsInlineLimit: 4096, // Inline small assets as base64
        target: 'es2015' // Better browser support
    },
    server: {
        port: 3002
    },
  // Optimize dependencies
    optimizeDeps: {
        include: ['alpinejs']
    }
}));
