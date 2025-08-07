import { defineConfig } from 'vite';
import path from 'path';
import { copyFileSync, mkdirSync, existsSync } from 'fs';
import { glob } from 'glob';

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
                    // Font files are handled by custom copy logic
                    if (assetInfo.name && /\.(woff2?|eot|ttf|otf)$/i.test(assetInfo.name)) {
                        return 'assets/fonts/[name].[ext]';
                    }
                    return 'assets/[name].[ext]';
                }
            }
        }
    },
    plugins: [
        {
            name: 'copy-fonts-to-public',
            writeBundle() {
                // Copy fonts to central public/fonts directory
                const publicFontsDir = path.resolve(__dirname, '../../../public/fonts');
                if (!existsSync(publicFontsDir)) {
                    mkdirSync(publicFontsDir, { recursive: true });
                }

                const fontFiles = glob.sync('fonts/source-sans-pro/*.woff2', { cwd: path.resolve(__dirname, 'src') });
                fontFiles.forEach(fontFile => {
                    const fileName = path.basename(fontFile);
                    const srcPath = path.resolve(__dirname, 'src', fontFile);
                    const destPath = path.resolve(publicFontsDir, fileName);
                    copyFileSync(srcPath, destPath);
                    console.log(`Copied ${fileName} to public / fonts / `);
                });
            }
    }
    ],
    server: {
        port: 3001
    }
}));
