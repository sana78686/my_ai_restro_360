/**
 * Minimal Theme - Clean, zen-like restaurant storefront
 * 
 * Generous whitespace, monochrome palette, menu-first focus.
 * Good for: coffee shops, bakeries, health food, boutique restaurants
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
    slug: 'minimal',
    name: 'Minimal',
    description: 'Clean, zen-like design with generous whitespace and menu-first focus',
    preview: '/themes/minimal/preview.png',
    tags: ['minimal', 'clean', 'zen', 'simple']
}
