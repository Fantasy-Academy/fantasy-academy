import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { sentryVitePlugin } from '@sentry/vite-plugin';

export default defineConfig({
  plugins: [
    vue(),
    // Upload source maps to Sentry on production builds
    // Only runs if SENTRY_AUTH_TOKEN is provided
    process.env.SENTRY_AUTH_TOKEN && sentryVitePlugin({
      org: 'fantasy-academy',
      project: 'frontend',
      authToken: process.env.SENTRY_AUTH_TOKEN,
      release: {
        name: process.env.VITE_APP_VERSION,
      },
    }),
  ].filter(Boolean),
  resolve: {
    alias: {
      '@': '/app/src',
    },
  },
  build: {
    // Generate source maps for production builds
    sourcemap: true,
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:8080',
        changeOrigin: true,
        secure: false
      }
    }
  }
});
