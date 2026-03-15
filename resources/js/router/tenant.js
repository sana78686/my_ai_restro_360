import { createRouter, createWebHistory } from 'vue-router';
import TenantFrontendLayout from '../layouts/TenantFrontendLayout.vue';
import TenantDashboardLayout from '../layouts/TenantDashboardLayout.vue';
import TenantLanding from '../views/tenant/landing.vue';
import axios from 'axios';
import { getAppName, getAppNameSync } from '../utils/appName';

const routes = [

  // Tenant Frontend routes
  {
    path: '/',

    component: TenantFrontendLayout,
    children: [
      {
        path: '',
        name: 'tenant-landing',
        meta: { title: 'Home' },
        component: TenantLanding,
        beforeEnter: (to, from, next) => {
          const host = window.location.host;
          const mainDomain = window.MAIN_DOMAIN;
          // Example: 4v57qif4mzpa3wap.localhost:8000
          if (host && host !== mainDomain) {
            // if (host && host !== 'localhost:8000' && host !== '127.0.0.1:8000') {
            // It's a tenant subdomain, show tenant landing
            next();
          } else {
            // It's the main domain, redirect to main home page
            next({ name: 'home' });
          }
        }
      },
      {
        path: 'checkout',
        name: 'checkout',
        component: () => import('../views/tenant/Checkout.vue'),
        meta: { title: 'Checkout' }
      },
      {
        path: 'test-checkout',
        name: 'test-checkout',
        component: () => import('../views/tenant/CheckoutTest.vue')
      },
      {
        path: 'stock-check',
        name: 'stock-check',
        component: () => import('../views/tenant/StockCheck.vue'),
        meta: { title: 'StockCheck' }
      },
      {
        path: 'reservation',
        name: 'reservation',
        component: () => import('../views/tenant/Reservation.vue'),
        meta: { title: 'Reservation' }
      },
      {
        path: 'about',
        name: 'about',
        component: () => import('../views/tenant/About.vue'),
        meta: { title: 'About' }
      },
      {
        path: 'contact',
        name: 'contact',
        component: () => import('../views/tenant/Contact.vue'),
        meta: { title: 'Contact' }
      },
      {
        path: 'privacy',
        name: 'privacy',
        component: () => import('../views/tenant/Privacy.vue'),
        meta: { title: 'Privacy' }

      },
      {
        path: 'page/:slug',
        name: 'cms-page',
        component: () => import('../views/tenant/CmsPage.vue'),
        meta: { title: 'CmsPage' }
      },
      {
        path: '/login',
        name: 'tenant-login',
        component: () => import('../views/tenant/Login.vue'),
         meta: { title: 'Login' }
      },
      {
        path: '/forgot-password',
        name: 'forgot-password',
        component: () => import('../views/tenant/ForgotPassword.vue'),
         meta: { title: 'Forgot Password' }
      },
      {
        path: '/reset-password',
        name: 'reset-password',
        component: () => import('../views/tenant/ResetPass.vue'),
         meta: { title: 'Reset Password' }
      },
      {
        path: '/verify-otp',
        name: 'verify-otp',
        component: () => import('../views/tenant/VerifyOtp.vue'),
        meta: { title: 'Verify OTP' }
      },

    ]
  },


  // Tenant Dashboard routes
  {
    path: '/dashboard/home',
    meta: { title: 'Dashboard' },
    component: TenantDashboardLayout,
    children: [
      {
        path: '',
        name: 'tenant-dashboard',
        component: () => import('../views/tenant_dashboard/TenantDashboard.vue'),
        meta: { title: 'Home' }
      },
      {
        path: '/dashboard/roles',
        name: 'tenant-roles',
        component: () => import('../views/tenant_dashboard/Roles.vue'),
        meta: { title: 'Roles' }
      },
      {
        path: '/dashboard/users',
        name: 'tenant-users',
        component: () => import('../views/tenant_dashboard/Users.vue'),
        meta: { title: 'Users' }
      },
      {
        path: '/dashboard/categories',
        name: 'tenant-categories',
        meta: { requiresAuth: true },
        component: () => import('../views/tenant_dashboard/Categories.vue'),
        meta: { title: 'Users' }
      },
      {
        path: '/dashboard/products',
        name: 'tenant-products',
        meta: { title: 'Users' },
        component: () => import('../views/tenant_dashboard/Products.vue')
      },
      {
        path: '/dashboard/settings',
        name: 'tenant-settings',
       meta: { title: 'Users' },
        component: () => import('../views/tenant_dashboard/Settings.vue')
      },
      {
        path: '/dashboard/cms',
        name: 'tenant-cms',
        meta: { title: 'CMS ' },
        component: () => import('../views/tenant_dashboard/CMS.vue')
      },
      {
        path: '/dashboard/email-settings',
        name: 'tenant-email-settings',
        meta: { title: 'Email Settings' },
        component: () => import('../views/tenant_dashboard/EmailSettings.vue')
      },
      {
        path: '/dashboard/orders',
        name: 'tenant-orders',
        meta: { title: 'Orders' },
        component: () => import('../views/tenant_dashboard/Orders.vue')
      },
      {
        path: '/dashboard/stock-check-requests',
        name: 'tenant-stock-check-requests',
        meta: { title: 'Stock Check' },
        component: () => import('../views/tenant_dashboard/StockCheckRequests.vue')
      },
      {
        path: '/dashboard/quote-requests',
        name: 'tenant-quote-requests',
       meta: { title: 'Quote Requests' },
        component: () => import('../views/tenant_dashboard/QuoteRequests.vue')
      },
      {
        path: '/dashboard/reservations',
        name: 'tenant-reservations',
        meta: { title: 'Reservations' },
        component: () => import('../views/tenant_dashboard/Reservations.vue')
      },
      {
        path: '/dashboard/contact-reqs',
        name: 'tenant-contact-reqs',
       meta: { title: 'Contact Request' },
        component: () => import('../views/tenant_dashboard/Customers.vue')
      },
      {
        path: '/dashboard/subscribers',
        name: 'tenant-subscribers',
        meta: { title: 'Subscribers' },
        component: () => import('../views/tenant_dashboard/Customers.vue')
      },
      {
        path: '/dashboard/customers',
        name: 'tenant-customers',
        meta: { title: 'Customers' },
        component: () => import('../views/tenant_dashboard/Customers.vue')
      },
      {
        path: '/dashboard/bulletin',
        name: 'tenant-bulletin',
        meta: { title: 'Bulletin' },
        component: () => import('../views/tenant_dashboard/Bulletin.vue')
      },
      // Add more tenant child routes here
      {
        path: '/dashboard/delivery-boy',
        name: 'tenant-delivery-boy',
        meta: { title: 'Delivery Boy' },
        component: () => import('../views/tenant_dashboard/DeliveryBoy.vue')
      },
      {
        path: '/dashboard/maillogs',
        name: 'tenant-maillogs',
        meta: { title: 'MailLogs' },
        component: () => import('../views/tenant_dashboard/MailLogs.vue')
      },
      {
        path: '/dashboard/notifications',
        name: 'tenant-notifications',
        meta: { title: 'Notifications' },
        component: () => import('../views/tenant_dashboard/Notifications.vue')
      },
      {
        path: '/dashboard/reset-pass',
        name: 'tenant-reset-pass',
        meta: { title: 'Reset Password' },
        component: () => import('../views/tenant_dashboard/ResetPassword.vue')
      },
      {
        path: '/dashboard/plans',
        name: 'tenant-plans',
        meta: { requiresAuth: true },
        component: () => import('../views/tenant_dashboard/Plans.vue')
      },

      {
        path: '/dashboard/personal-settings',
        name: 'tenant-personal-settings',
        meta: { title: 'Personal Settings' },
        component: () => import('../views/tenant_dashboard/PersonalSettings.vue')
      },
      // In your router file - it's already there!
      {
        path: '/dashboard/payment-gateways',
        name: 'PaymentGateways',
        component: () => import('../views/tenant_dashboard/PaymentGateways.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/dashboard/pricing',
        name: 'pricing',
        component: () => import('../views/tenant_dashboard/Pricing.vue'),
        meta: { requiresAuth: true }
      },
      {
        path: '/dashboard/stripesuccess',
        name: 'stripesuccess',
        component: () => import('../views/tenant_dashboard/StripeSuccess.vue'),
        meta: { requiresAuth: true }
      }
    ]
  }
];



const router = createRouter({
  history: createWebHistory(),
  routes
});
router.beforeEach((to, from, next) => {

  const token = localStorage.getItem('token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard/home') // prevent showing login again if already logged in
  } else {
    next()
  }
})
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
  const fullTitle = titleParts.join(' ') || 'Home'

  // Get dynamic app name (will be tenant business name if in tenant context)
  const appName = await getAppName()

  // Final tab title
  document.title = `${fullTitle} - ${appName}`
})

export default router;
