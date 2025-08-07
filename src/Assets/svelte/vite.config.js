import { defineConfig } from 'vite'
import { svelte } from '@sveltejs/vite-plugin-svelte'
import { resolve } from 'path'

export default defineConfig({
  plugins: [svelte()],
  
  build: {
    outDir: '../../../public/themes/svelte',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/main.js')
      },
      output: {
        entryFileNames: 'assets/[name].js',
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name].[ext]'
      }
    },
    manifest: true,
    assetsDir: 'assets'
  },
  
  server: {
    port: 5174,
    host: true
  },
  
  css: {
    postcss: './postcss.config.js'
  }
})
