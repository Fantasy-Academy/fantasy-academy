import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import { useProfile } from '../composables/useProfile';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    //public
    { path: '/', component: () => import('../views/HomePage.vue') },
    { path: '/login', component: () => import('../views/LoginPage.vue') },
    { path: '/signup', component: () => import('../views/SignupPage.vue') },
    { path: '/challenges', component: () => import('../views/ChallengesPage.vue') },
    { path: '/forgot-password', component: () => import('../views/ForgotPasswordPage.vue') },
    { path: '/reset-password', component: () => import('../views/ResetPasswordPage.vue') },
    { path: '/leaderboard', component: () => import('../views/LeaderboardPage.vue') },
    { path: '/player/:id', component: () => import('../views/PlayerPage.vue') },
    //secured
    { path: '/profile', component: () => import('../views/ProfilePage.vue'), meta: { requiresAuth: true } },
    { path: '/dashboard', component: () => import('../views/DashboardPage.vue'), meta: { requiresAuth: true } },
    { path: '/profile/edit', component: () => import('@/views/EditProfilePage.vue'), meta: { requiresAuth: true } },
    // ── Subscription ──────────────────────────────────────────────────────
    {
      path: '/subscription',
      component: () => import('../views/SubscriptionPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/subscription/success',
      component: () => import('../views/SubscriptionSuccessPage.vue'),
      meta: { requiresAuth: true },
    },
    {
      path: '/subscription/cancel',
      component: () => import('../views/SubscriptionCancelPage.vue'),
      meta: { requiresAuth: true },
    },
  ],
});

router.beforeEach(async (to) => {
  const { isAuthenticated } = useAuth();
  const { me } = useProfile();

  if (to.path === '/reset-password') return true;

  if (to.meta.requiresAuth && !isAuthenticated.value) {
    return { path: '/login', query: { redirect: to.fullPath } };
  }

  if (to.meta.requiresMembership) {
    const isMember = me.value?.isMember ?? false;
    if (!isMember) {
      return { path: '/subscription' };
    }
  }
});

// Handle chunk load errors (stale assets after deployment)
router.onError((error, to) => {
  if (
    error.message.includes('Failed to fetch dynamically imported module') ||
    error.message.includes('Importing a module script failed') ||
    error.message.includes('error loading dynamically imported module')
  ) {
    console.warn('Detected stale chunk error, reloading page...', error);
    // Reload the page to fetch fresh assets
    // Use location.href instead of router to ensure full page reload
    window.location.href = to.fullPath;
  }
});

export default router;