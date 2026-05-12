/**
 * Classic Theme - Default AiRestro360 tenant storefront
 * 
 * Warm, traditional restaurant feel with card-based layout.
 * Good for: cafes, diners, family restaurants, traditional cuisine
 */

import { createRouter, createWebHistory } from 'vue-router'
import i18n from '../../js/i18n'
import routes from './router'
import './assets/tokens.css'

/**
 * Initialize the classic theme
 * Called by themeLoader when this theme is active
 */
export async function initTheme(app) {
    const router = createRouter({
        history: createWebHistory(),
        routes
    })

    // Navigation guards
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
    slug: 'classic',
    name: 'Classic',
    description: 'Traditional restaurant design with warm colors and card-based layout',
    preview: '/themes/classic/preview.png',
    tags: ['traditional', 'warm', 'cards', 'family']
}
