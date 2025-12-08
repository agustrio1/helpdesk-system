import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  plugins: [tailwindcss()],
  build: {
    outDir: 'public/assets',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: './resources/js/app.js'  // ← HANYA JS, CSS akan otomatis ter-bundle
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name.endsWith('.css')) {
            return 'css/app[extname]';
          }
          return 'assets/[name]-[hash][extname]';
        }
      }
    }
  },
  publicDir: false,  // ← TAMBAHKAN INI untuk hilangkan warning
  server: {
    port: 5173,
    proxy: {
      '/api': 'http://localhost'
    }
  }
});