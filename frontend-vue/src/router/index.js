import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    //public
    { path: '/', component: () => import('../views/HomePage.vue') },
    { path: '/login', component: () => import('../views/LoginPage.vue') },
    { path: '/signup', component: () => import('../views/SignupPage.vue') },
    { path: '/challenges', component: () => import('../views/ChallengesPage.vue') },
    { path: '/forgot-password', component: () => import('../views/ForgotPasswordPage.vue') },
    //secured
    { path: '/profile', component: () => import('../views/ProfilePage.vue'), meta: { requiresAuth: true } },
    { path: '/dashboard', component: () => import('../views/DashboardPage.vue'), meta: { requiresAuth: true } },
  ],
});

router.beforeEach((to) => {
  const { isAuthenticated } = useAuth();
  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return { path: '/login', query: { redirect: to.fullPath } };
  }
});

export default router;