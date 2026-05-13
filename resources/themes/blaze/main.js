/**
 * Blaze Theme - Bold fast-food inspired storefront
 * 
 * Energetic red/black/white palette, category-focused navigation,
 * sticky cart sidebar, promotional banners.
 * Good for: fast food, fried chicken, burgers, pizza, QSR
 */

import { createRouter, createWebHistory } from 'vue-router'
import i18n from '../../js/i18n'
import routes from './router'
import './assets/tokens.css'

export async function initTheme(app) {
    const router = createRouter({
        history: createWebHistory(),
        routes
    })

    router.beforeEach((to, from, next) => {
        const token = localStorage.getItem('token')
        if (to.meta.requiresAuth && !token) {
            next('/login')
        } else if (to.path === '/login' && token) {
            next('/dashboard/home')
        } else {
            next()
        }
    })

    app.use(router)
    app.use(i18n)

    return { router }
}

export const themeMeta = {
    slug: 'blaze',
    name: 'Blaze',
    description: 'Bold fast-food style with sticky cart, category tabs, and promotional banners',
    preview: '/themes/blaze/preview.png',
    tags: ['fast-food', 'bold', 'red', 'energetic', 'qsr']
}
