import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

const router = new VueRouter({
    routes: [
        {
            path: '/dashboard',
            component: Dashboard,
            meta: {
                requiresAuth: true // Add meta field to indicate protected route
            }
        },
        {
            path: '/profile',
            component: Dashboard,
            meta: {
                requiresAuth: true // Add meta field to indicate protected route
            }
        }
        // Other routes...
    ]
});

router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth) {
        const token = localStorage.getItem('token');
        if (token) {
            // User is authenticated, proceed to the route
            next();
        } else {
            // User is not authenticated, redirect to login
            next('/login');
        }
    } else {
        // Non-protected route, allow access
        next();
    }
});

export default router;