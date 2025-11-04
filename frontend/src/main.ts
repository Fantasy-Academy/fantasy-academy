import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import './index.css'
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
  });
}

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
