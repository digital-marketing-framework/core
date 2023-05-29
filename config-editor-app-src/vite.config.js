import { fileURLToPath, URL } from 'node:url';

import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  },
  build: {
    outDir: '../assets',
    emptyOutDir: false,
    rollupOptions: {
      output: {
        entryFileNames: `scripts/[name].js`,
        chunkFileNames: `scripts/[name].js`,
        assetFileNames: `styles/[name].[ext]`,
        sourcemap: true,
        sourcemapFile: `scripts/[name].js.map`
      }
    }
  }
});
