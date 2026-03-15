import { createRouter, createWebHistory } from 'vue-router';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import FrontendLayout from '../layouts/FrontendLayout.vue';
import { getAppName, getAppNameSync } from '../utils/appName';

const routes = [
  // Frontend Routes
  {
    path: '/',
    component: FrontendLayout,
    children: [
      {
        path: '',
        name: 'home',
        meta: { title: 'Home' },
        component: () => import('../views/frontend/Home.vue')
      },
      {
        path: 'food-ordering',
        name: 'food-ordering',
        meta: { title: 'FoodOrdering' },
        component: () => import('../views/frontend/FoodOrdering.vue')
      },
      {
        path: 'restaurant-services',
        name: 'restaurant-services',
        meta: { title: 'Restaurant Services' },
        component: () => import('../views/frontend/RestaurantServices.vue')
      },
      {
        path: 'pricing',
        name: 'pricing',
        meta: { title: 'Pricing' },
        component: () => import('../views/frontend/Pricing.vue')
      },
      {
        path: 'about',
        name: 'about',
        meta: { title: 'About' },
        component: () => import('../views/frontend/About.vue')
      },
      {
        path: 'contact',
        name: 'contact',
        meta: { title: 'Contact' },
        component: () => import('../views/frontend/Contact.vue')
      },
      {
        path: 'privacy',
        name: 'privacy',
        meta: { title: 'Privacy' },
        component: () => import('../views/frontend/Privacy.vue')
      },
      {
        path: 'terms',
        name: 'terms',
        meta: { title: 'Terms' },
        component: () => import('../views/frontend/Terms.vue')
      },
      {
        path: 'tenant-register',
        name: 'tenant-register',
        component: () => import('../views/frontend/TenantRegister.vue'),
        meta: { title: 'Register' },
      },
      {
        path: 'auth/sys-access-9xA7',
        name: 'super-login',
        component: () => import('../views/auth/Login.vue'),
        meta: { title: 'Login' },
      }
    ]
  },
  // Dashboard Routes
  {
    path: '/dashboard',
    component: DashboardLayout,
    meta: { title: 'Dashboard' },
    children: [
      {
        path: '',
        name: 'dashboard',
        meta: { title: 'Home' },
        component: () => import('../views/dashboard/Dashboard.vue')
      },
      {
        path: 'users',
        name: 'users',
        meta: { title: 'Users' },
        component: () => import('../views/dashboard/Users.vue')
      },
      {
        path: 'roles',
        name: 'roles',
        meta: { title: 'Roles' },
        component: () => import('../views/dashboard/Roles.vue')
      },
      {
        path: 'tenants',
        name: 'tenants',
        meta: { title: 'Tenants' },
        component: () => import('../views/dashboard/Tenants.vue')
      },
      {
        path: 'mail-logs',
        name: 'mail-logs',
        meta: { title: 'Mail Logs' },
        component: () => import('../views/dashboard/MailLogs.vue')
      },
      {
        path: 'plans',
        name: 'plans',
        component: () => import('../views/dashboard/Plan.vue')
      },
      {
        path: 'payments',
        name: 'payments',
        meta: { title: 'Payments' },
        component: () => import('../views/dashboard/Payments.vue')
      },
      {
        path: 'subscriptions',
        name: 'subscriptions',
        meta: { title: 'Subscriptions' },
        component: () => import('../views/dashboard/Subscriptions.vue')
      },
    ]
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  // Allow public routes
  if (to.meta.public) {
    next();
    return;
  }

  // Check authentication for dashboard routes
  if (to.path.startsWith('/dashboard')) {
    const token = localStorage.getItem('token');
    if (!token) {
      next('/login');
      return;
    }
  }

  next();
});
router.afterEach(async (to) => {
  // Collect titles from all matched routes (parents + child)
  const titleParts = to.matched
    .filter(record => record.meta && record.meta.title)
    .map(record => {
      // If using i18n, translate here:
      // return i18n.global.t(record.meta.title)
      return record.meta.title
    })

  // Join parent + child titles, e.g. "Dashboard Home"
  const fullTitle = titleParts.join(' ') || 'Dashboard'

  // Get dynamic app name
  const appName = await getAppName()

  // Final tab title
  document.title = `${fullTitle} - ${appName}`
})

export default router;
