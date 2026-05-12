import { createRouter, createWebHistory } from 'vue-router';
import DashboardLayout from '../layouts/DashboardLayout.vue';
import FrontendLayout from '../layouts/FrontendLayout.vue';
import { getAppName, getAppNameSync } from '../utils/appName';
import i18n from '../i18n';

const routes = [
  // Frontend Routes
  {
    path: '/',
    component: FrontendLayout,
    children: [
      {
        path: '',
        name: 'home',
        meta: { title: 'Get started', i18nDescription: 'home.landing.seoDescription' },
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
        path: 'login/2',
        name: 'login-password',
        component: () => import('../views/auth/Login.vue'),
        meta: { title: 'Login' },
      },
      {
        path: 'login',
        name: 'login',
        component: () => import('../views/auth/Login.vue'),
        meta: { title: 'Login' },
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
        meta: { i18nTitle: 'tenants.pageTitle' },
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

/** Central-auth login screens — if a session token exists, skip and open the dashboard. */
const LOGIN_ROUTE_NAMES = new Set(['login', 'login-password', 'super-login']);

// Navigation guard
router.beforeEach((to, from, next) => {
  // Allow public routes
  if (to.meta.public) {
    next();
    return;
  }

  const token = localStorage.getItem('token');

  if (token && LOGIN_ROUTE_NAMES.has(to.name)) {
    next({ path: '/dashboard', replace: true });
    return;
  }

  // Check authentication for dashboard routes
  if (to.path.startsWith('/dashboard')) {
    if (!token) {
      next('/login');
      return;
    }
  }

  next();
});
router.afterEach(async (to) => {
  const loc = i18n.global.locale?.value || i18n.global.locale
  if (loc) {
    document.documentElement.lang = String(loc)
    document.documentElement.setAttribute('dir', String(loc) === 'ar' ? 'rtl' : 'ltr')
  }

  // Collect titles from all matched routes (parents + child)
  const titleParts = to.matched
    .filter(record => record.meta && (record.meta.i18nTitle || record.meta.title))
    .map(record => {
      if (record.meta.i18nTitle) {
        return i18n.global.t(record.meta.i18nTitle)
      }
      return record.meta.title
    })

  // Join parent + child titles, e.g. "Dashboard Home"
  const fullTitle = titleParts.join(' ') || 'Dashboard'

  // Get dynamic app name
  const appName = await getAppName()

  // Final tab title
  document.title = `${fullTitle} - ${appName}`

  const metaDesc = document.querySelector('meta[name="description"]')
  if (metaDesc) {
    const withDesc = [...to.matched]
      .reverse()
      .find((r) => r.meta && (r.meta.i18nDescription || r.meta.description))
    if (withDesc?.meta?.i18nDescription) {
      metaDesc.setAttribute('content', i18n.global.t(withDesc.meta.i18nDescription))
    } else if (withDesc?.meta?.description) {
      metaDesc.setAttribute('content', withDesc.meta.description)
    } else {
      metaDesc.setAttribute(
        'content',
        'Register your restaurant on AiRestro 360. Online orders, operations, and guest service in one place.'
      )
    }
  }

  if (typeof window !== 'undefined') {
    let can = document.querySelector('link[rel="canonical"]')
    if (!can) {
      can = document.createElement('link')
      can.setAttribute('rel', 'canonical')
      document.head.appendChild(can)
    }
    const path = to.path || '/'
    can.setAttribute('href', window.location.origin + (path.startsWith('/') ? path : `/${path}`))
  }
})

export default router;
