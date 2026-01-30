import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './index.css';
import './assets/hint.css';
import './styles/tailwind.css';
import * as Sentry from '@sentry/vue';

const app = createApp(App);

// Initialize Sentry if DSN is provided
if (import.meta.env.VITE_SENTRY_DSN) {
  Sentry.init({
    app,
    dsn: import.meta.env.VITE_SENTRY_DSN,
    release: import.meta.env.VITE_APP_VERSION,
    integrations: [
      Sentry.browserTracingIntegration({ router }),
      Sentry.replayIntegration(),
      Sentry.vueIntegration({
        tracingOptions: {
            trackComponents: true,
        },
      }),
    ],
    tracesSampleRate: 0.0,
    replaysSessionSampleRate: 0.0,
    replaysOnErrorSampleRate: 1.0,
    sendDefaultPii: true,
    environment: import.meta.env.MODE,
    // Filter out chunk load errors from Sentry since we handle them with reload
    beforeSend(event, hint) {
      const error = hint.originalException;
      if (error && typeof error === 'object' && 'message' in error) {
        const message = String(error.message);
        if (
          message.includes('Failed to fetch dynamically imported module') ||
          message.includes('Importing a module script failed') ||
          message.includes('error loading dynamically imported module')
        ) {
          // Don't send chunk load errors to Sentry
          return null;
        }
      }
      return event;
    },
  });
}

// Global error handler for uncaught chunk load errors
window.addEventListener('error', (event) => {
  if (
    event.message.includes('Failed to fetch dynamically imported module') ||
    event.message.includes('Importing a module script failed') ||
    event.message.includes('error loading dynamically imported module')
  ) {
    console.warn('Global chunk load error detected, reloading...', event.error);
    event.preventDefault();
    window.location.reload();
  }
});

router.afterEach((to) => {
  if (import.meta.env.PROD && window.gtag) {
    window.gtag('config', import.meta.env.VITE_GA_ID, {
      page_path: to.fullPath,
      page_title: to.name || document.title,
    });
  }
});

app.use(router);
app.mount('#app');
