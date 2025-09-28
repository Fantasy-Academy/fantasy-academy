import { createApp } from 'vue';
import App from './App.vue';
import router from './router';  
import './index.css'
import './styles/tailwind.css';


router.afterEach((to) => {
  if (import.meta.env.PROD && window.gtag) {
    window.gtag('config', import.meta.env.VITE_GA_ID, {
      page_path: to.fullPath,
      page_title: to.name || document.title,
    });
  }
});

const app = createApp(App);
app.use(router);                
app.mount('#app');