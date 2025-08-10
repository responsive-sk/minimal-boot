import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import { resolve } from 'path'

export default defineConfig({
    plugins: [react()],

    build: {
        outDir: '../../../public/themes/react',
        emptyOutDir: true,
        cssCodeSplit: false,
        rollupOptions: {
            input: {
                main: resolve(__dirname, 'src/main.jsx')
            },
            output: {
                entryFileNames: 'assets/[name].js',
                chunkFileNames: 'assets/[name].js',
                assetFileNames: 'assets/[name].[ext]',
                format: 'iife',
                name: 'ForestCalmApp'
            }
        },
        manifest: true,
        assetsDir: 'assets'
    },

    server: {
        port: 5177,
        host: true
    },

    css: {
        postcss: './postcss.config.js'
    }
})
