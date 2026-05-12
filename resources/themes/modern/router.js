/**
 * Modern Theme Router
 */

import TenantFrontendLayout from './layouts/TenantFrontendLayout.vue'
import TenantDashboardLayout from '../../js/layouts/TenantDashboardLayout.vue'

const routes = [
    {
        path: '/',
        component: TenantFrontendLayout,
        children: [
            {
                path: '',
                name: 'tenant-landing',
                meta: { title: 'Home' },
                component: () => import('./pages/Landing.vue')
            },
            {
                path: 'checkout',
                name: 'checkout',
                component: () => import('./pages/Checkout.vue'),
                meta: { title: 'Checkout' }
            },
            {
                path: 'stock-check',
                name: 'stock-check',
                component: () => import('./pages/StockCheck.vue'),
                meta: { title: 'Stock Check' }
            },
            {
                path: 'reservation',
                name: 'reservation',
                component: () => import('./pages/Reservation.vue'),
                meta: { title: 'Reservation' }
            },
            {
                path: 'about',
                name: 'about',
                component: () => import('./pages/About.vue'),
                meta: { title: 'About' }
            },
            {
                path: 'contact',
                name: 'contact',
                component: () => import('./pages/Contact.vue'),
                meta: { title: 'Contact' }
            },
            {
                path: 'privacy',
                name: 'privacy',
                component: () => import('./pages/Privacy.vue'),
                meta: { title: 'Privacy' }
            },
            {
                path: 'page/:slug',
                name: 'cms-page',
                component: () => import('./pages/CmsPage.vue'),
                meta: { title: 'Page' }
            },
            {
                path: 'login',
                name: 'tenant-login',
                component: () => import('./pages/Login.vue'),
                meta: { title: 'Login' }
            },
            {
                path: 'forgot-password',
                name: 'forgot-password',
                component: () => import('./pages/ForgotPassword.vue'),
                meta: { title: 'Forgot Password' }
            },
            {
                path: 'reset-password',
                name: 'reset-password',
                component: () => import('./pages/ResetPassword.vue'),
                meta: { title: 'Reset Password' }
            }
        ]
    },
    {
        path: '/dashboard',
        component: TenantDashboardLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'home',
                name: 'dashboard-home',
                component: () => import('../../js/views/tenant_dashboard/TenantDashboard.vue'),
                meta: { title: 'Dashboard' }
            },
            {
                path: 'products',
                name: 'dashboard-products',
                component: () => import('../../js/views/tenant_dashboard/Products.vue'),
                meta: { title: 'Products' }
            },
            {
                path: 'settings',
                name: 'dashboard-settings',
                component: () => import('../../js/views/tenant_dashboard/Settings.vue'),
                meta: { title: 'Settings' }
            }
        ]
    }
]

export default routes
