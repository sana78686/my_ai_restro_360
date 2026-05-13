/**
 * Blaze Theme Router
 * 
 * Frontend routes use Blaze-specific layout and pages
 * Dashboard routes reuse shared tenant dashboard
 */

const BlazeLayout = () => import('./layouts/TenantFrontendLayout.vue')

const frontendRoutes = [
    {
        path: '/',
        component: BlazeLayout,
        children: [
            {
                path: '',
                name: 'landing',
                component: () => import('./pages/Landing.vue'),
                meta: { title: 'Home' }
            },
            {
                path: 'menu',
                name: 'menu',
                component: () => import('./pages/Menu.vue'),
                meta: { title: 'Menu' }
            },
            {
                path: 'checkout',
                name: 'checkout',
                component: () => import('../../js/views/tenant/Checkout.vue'),
                meta: { title: 'Checkout' }
            },
            {
                path: 'about',
                name: 'about',
                component: () => import('../../js/views/tenant/About.vue'),
                meta: { title: 'About Us' }
            },
            {
                path: 'contact',
                name: 'contact',
                component: () => import('../../js/views/tenant/Contact.vue'),
                meta: { title: 'Contact' }
            },
            {
                path: 'branches',
                name: 'branches',
                component: () => import('../../js/views/tenant/Branches.vue'),
                meta: { title: 'Our Branches' }
            },
            {
                path: 'track',
                name: 'track-order',
                component: () => import('../../js/views/tenant/TrackOrder.vue'),
                meta: { title: 'Track Order' }
            },
            {
                path: 'terms',
                name: 'terms',
                component: () => import('../../js/views/tenant/Terms.vue'),
                meta: { title: 'Terms & Conditions' }
            },
            {
                path: 'privacy',
                name: 'privacy',
                component: () => import('../../js/views/tenant/Privacy.vue'),
                meta: { title: 'Privacy Policy' }
            }
        ]
    },
    {
        path: '/login',
        name: 'tenant-login',
        component: () => import('../../js/views/tenant/Login.vue'),
        meta: { title: 'Login' }
    }
]

const dashboardRoutes = [
    {
        path: '/dashboard',
        component: () => import('../../js/layouts/TenantDashboardLayout.vue'),
        meta: { requiresAuth: true },
        children: [
            {
                path: 'home',
                name: 'dashboard-home',
                component: () => import('../../js/views/tenant_dashboard/Home.vue'),
                meta: { title: 'Dashboard' }
            },
            {
                path: 'orders',
                name: 'dashboard-orders',
                component: () => import('../../js/views/tenant_dashboard/Orders.vue'),
                meta: { title: 'Orders' }
            },
            {
                path: 'menu',
                name: 'dashboard-menu',
                component: () => import('../../js/views/tenant_dashboard/Menu.vue'),
                meta: { title: 'Menu Management' }
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

export default [...frontendRoutes, ...dashboardRoutes]
