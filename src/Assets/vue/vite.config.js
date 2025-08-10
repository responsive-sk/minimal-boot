import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

export default defineConfig({
    plugins: [vue()],

    build: {
        outDir: '../../../public/themes/vue',
        emptyOutDir: true,
        cssCodeSplit: false,
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'src/main.js')
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]',
                format: 'iife',
                name: 'VueCyberpunkApp'
            }
        },
        manifest: true,
        assetsDir: 'assets'
    },

    server: {
        port: 5175,
        host: true
    },

    css: {
        postcss: './postcss.config.js'
    }
})
