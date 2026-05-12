/**
 * Modern Theme - Bold, contemporary restaurant storefront
 * 
 * Full-bleed images, strong typography, dark accents.
 * Good for: trendy cafes, fusion restaurants, bars, modern bistros
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
    slug: 'modern',
    name: 'Modern',
    description: 'Bold contemporary design with full-bleed imagery and strong typography',
    preview: '/themes/modern/preview.png',
    tags: ['modern', 'bold', 'dark', 'trendy']
}
