<template>
    <div class="blaze-theme">
        <!-- Header -->
        <header class="blaze-header" :class="{ 'blaze-header--scrolled': isScrolled }">
            <div class="blaze-header__container">
                <!-- Mobile Menu Toggle -->
                <button 
                    class="blaze-header__menu-btn"
                    @click="isMobileNavOpen = true"
                    aria-label="Open menu"
                >
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Logo -->
                <router-link to="/" class="blaze-header__logo">
                    <img 
                        v-if="tenantLogo" 
                        :src="tenantLogo" 
                        :alt="tenantName"
                        class="blaze-header__logo-img"
                    />
                    <span v-else class="blaze-header__logo-text">{{ tenantName }}</span>
                </router-link>

                <!-- Order Type Tabs (Desktop) -->
                <div class="blaze-header__tabs">
                    <button 
                        v-for="tab in orderTabs" 
                        :key="tab.id"
                        class="blaze-header__tab"
                        :class="{ 'blaze-header__tab--active': activeTab === tab.id }"
                        @click="activeTab = tab.id"
                    >
                        <i :class="tab.icon"></i>
                        <span>{{ tab.label }}</span>
                    </button>
                </div>

                <!-- Right Actions -->
                <div class="blaze-header__actions">
                    <button class="blaze-header__icon-btn" aria-label="Search">
                        <i class="fas fa-search"></i>
                    </button>
                    
                    <router-link to="/checkout" class="blaze-header__cart">
                        <i class="fas fa-shopping-basket"></i>
                        <span v-if="cartCount" class="blaze-header__cart-badge">{{ cartCount }}</span>
                    </router-link>
                    
                    <router-link 
                        :to="isLoggedIn ? '/dashboard/home' : '/login'" 
                        class="blaze-btn blaze-btn--primary blaze-header__login"
                    >
                        {{ isLoggedIn ? 'Dashboard' : 'Login' }}
                    </router-link>
                </div>
            </div>
        </header>

        <!-- Mobile Navigation Drawer -->
        <div 
            class="blaze-drawer-overlay" 
            :class="{ 'blaze-drawer-overlay--open': isMobileNavOpen }"
            @click="isMobileNavOpen = false"
        ></div>
        <nav class="blaze-drawer" :class="{ 'blaze-drawer--open': isMobileNavOpen }">
            <div class="blaze-drawer__header">
                <router-link 
                    :to="isLoggedIn ? '/dashboard/home' : '/login'" 
                    class="blaze-btn blaze-btn--primary blaze-drawer__login"
                    @click="isMobileNavOpen = false"
                >
                    {{ isLoggedIn ? 'Dashboard' : 'Login' }}
                </router-link>
                <button class="blaze-drawer__close" @click="isMobileNavOpen = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Day/Night Toggle (Static UI) -->
            <div class="blaze-drawer__toggle">
                <button 
                    class="blaze-drawer__toggle-btn"
                    :class="{ 'blaze-drawer__toggle-btn--active': !isDarkMode }"
                    @click="isDarkMode = false"
                >
                    Day
                </button>
                <button 
                    class="blaze-drawer__toggle-btn"
                    :class="{ 'blaze-drawer__toggle-btn--active': isDarkMode }"
                    @click="isDarkMode = true"
                >
                    Night
                </button>
            </div>

            <ul class="blaze-drawer__nav">
                <li>
                    <router-link to="/branches" @click="isMobileNavOpen = false">
                        <i class="fas fa-map-marker-alt"></i>
                        Store Locator
                    </router-link>
                </li>
                <li>
                    <router-link to="/track" @click="isMobileNavOpen = false">
                        <i class="fas fa-truck"></i>
                        Track Order
                    </router-link>
                </li>
                <li>
                    <router-link to="/menu" @click="isMobileNavOpen = false">
                        <i class="fas fa-utensils"></i>
                        Explore Menu
                    </router-link>
                </li>
            </ul>

            <hr class="blaze-drawer__divider" />

            <ul class="blaze-drawer__links">
                <li><router-link to="/about" @click="isMobileNavOpen = false">About Us</router-link></li>
                <li><router-link to="/terms" @click="isMobileNavOpen = false">Terms & Conditions</router-link></li>
                <li><router-link to="/privacy" @click="isMobileNavOpen = false">Privacy Policy</router-link></li>
                <li><router-link to="/contact" @click="isMobileNavOpen = false">Contact Us</router-link></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="blaze-main">
            <router-view />
        </main>

        <!-- Footer -->
        <footer class="blaze-footer">
            <div class="blaze-footer__container">
                <div class="blaze-footer__top">
                    <!-- Logo -->
                    <div class="blaze-footer__brand">
                        <img 
                            v-if="tenantLogo" 
                            :src="tenantLogo" 
                            :alt="tenantName"
                            class="blaze-footer__logo"
                        />
                        <span v-else class="blaze-footer__logo-text">{{ tenantName }}</span>
                    </div>

                    <!-- Social Links -->
                    <div class="blaze-footer__social">
                        <a v-if="socialLinks.youtube" :href="socialLinks.youtube" target="_blank" class="blaze-footer__social-link" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a v-if="socialLinks.instagram" :href="socialLinks.instagram" target="_blank" class="blaze-footer__social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a v-if="socialLinks.facebook" :href="socialLinks.facebook" target="_blank" class="blaze-footer__social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a v-if="socialLinks.twitter" :href="socialLinks.twitter" target="_blank" class="blaze-footer__social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a v-if="socialLinks.tiktok" :href="socialLinks.tiktok" target="_blank" class="blaze-footer__social-link" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <div class="blaze-footer__links">
                    <div class="blaze-footer__col">
                        <router-link to="/about">About Us</router-link>
                        <router-link to="/contact">Contact Us</router-link>
                        <router-link to="/branches">Store Locator</router-link>
                    </div>
                    <div class="blaze-footer__col">
                        <router-link to="/terms">Terms & Conditions</router-link>
                        <router-link to="/track">Track Order</router-link>
                        <router-link to="/privacy">Privacy Policy</router-link>
                    </div>
                </div>

                <div class="blaze-footer__bottom">
                    <p>&copy; {{ new Date().getFullYear() }} {{ tenantName }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import '../assets/tokens.css'

export default {
    name: 'BlazeTenantFrontendLayout',
    setup() {
        const isMobileNavOpen = ref(false)
        const isScrolled = ref(false)
        const isDarkMode = ref(false)
        const activeTab = ref('delivery')
        const websiteSettings = ref({})
        
        const orderTabs = [
            { id: 'delivery', label: 'Delivery', icon: 'fas fa-motorcycle' },
            { id: 'pickup', label: 'Pickup', icon: 'fas fa-store' },
            { id: 'dinein', label: 'Dine-in', icon: 'fas fa-chair' }
        ]

        const tenantName = computed(() => 
            websiteSettings.value.business_name || window.TENANT_NAME || 'Restaurant'
        )
        const tenantLogo = computed(() => 
            websiteSettings.value.logo_url || window.TENANT_LOGO || null
        )
        const socialLinks = computed(() => ({
            facebook: websiteSettings.value.facebook_link,
            instagram: websiteSettings.value.instagram_link,
            youtube: websiteSettings.value.youtube_link,
            twitter: websiteSettings.value.twitter_link,
            tiktok: websiteSettings.value.tiktok_link
        }))
        const isLoggedIn = computed(() => !!localStorage.getItem('token'))
        const cartCount = computed(() => {
            try {
                const cart = JSON.parse(localStorage.getItem('cart') || '[]')
                return cart.reduce((sum, item) => sum + (item.quantity || 1), 0)
            } catch {
                return 0
            }
        })

        const handleScroll = () => {
            isScrolled.value = window.scrollY > 20
        }

        const fetchWebsiteSettings = async () => {
            try {
                const response = await axios.get('/tenant/website-settings')
                if (response.data?.success) {
                    websiteSettings.value = response.data.data
                    applyColors(response.data.data)
                }
            } catch (error) {
                console.log('Using default website settings')
            }
        }

        const applyColors = (settings) => {
            if (settings.primary_color) {
                document.documentElement.style.setProperty('--blaze-primary', settings.primary_color)
            }
            if (settings.secondary_color) {
                document.documentElement.style.setProperty('--blaze-secondary', settings.secondary_color)
            }
            if (settings.accent_color) {
                document.documentElement.style.setProperty('--blaze-accent', settings.accent_color)
            }
        }

        onMounted(() => {
            window.addEventListener('scroll', handleScroll)
            fetchWebsiteSettings()
        })

        onUnmounted(() => {
            window.removeEventListener('scroll', handleScroll)
        })

        return {
            isMobileNavOpen,
            isScrolled,
            isDarkMode,
            activeTab,
            orderTabs,
            tenantName,
            tenantLogo,
            socialLinks,
            isLoggedIn,
            cartCount
        }
    }
}
</script>

<style scoped>
/* ===== Header ===== */
.blaze-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--blaze-header-height, 70px);
    background: var(--blaze-bg-white);
    box-shadow: var(--blaze-shadow-sm);
    z-index: 1000;
    transition: all var(--blaze-transition-normal);
}

.blaze-header--scrolled {
    box-shadow: var(--blaze-shadow-md);
}

.blaze-header__container {
    max-width: var(--blaze-container-max);
    margin: 0 auto;
    height: 100%;
    display: flex;
    align-items: center;
    padding: 0 1rem;
    gap: 1rem;
}

.blaze-header__menu-btn {
    display: none;
    background: none;
    border: none;
    font-size: 1.25rem;
    color: var(--blaze-secondary);
    cursor: pointer;
    padding: 0.5rem;
}

.blaze-header__logo {
    text-decoration: none;
}

.blaze-header__logo-img {
    height: 45px;
    width: auto;
    object-fit: contain;
}

.blaze-header__logo-text {
    font-size: var(--blaze-font-size-xl);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-primary);
}

.blaze-header__tabs {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: 2rem;
}

.blaze-header__tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--blaze-bg);
    border: 2px solid transparent;
    border-radius: var(--blaze-radius-full);
    font-size: var(--blaze-font-size-sm);
    font-weight: var(--blaze-font-weight-medium);
    color: var(--blaze-text);
    cursor: pointer;
    transition: all var(--blaze-transition-fast);
}

.blaze-header__tab--active {
    background: var(--blaze-bg-white);
    border-color: var(--blaze-secondary);
}

.blaze-header__tab:hover:not(.blaze-header__tab--active) {
    background: var(--blaze-border-light);
}

.blaze-header__actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-left: auto;
}

.blaze-header__icon-btn {
    background: none;
    border: none;
    font-size: 1.125rem;
    color: var(--blaze-secondary);
    cursor: pointer;
    padding: 0.5rem;
}

.blaze-header__cart {
    position: relative;
    font-size: 1.25rem;
    color: var(--blaze-secondary);
    text-decoration: none;
}

.blaze-header__cart-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    min-width: 20px;
    height: 20px;
    background: var(--blaze-primary);
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.blaze-header__login {
    padding: 0.5rem 1.25rem;
    font-size: var(--blaze-font-size-sm);
}

/* ===== Mobile Drawer ===== */
.blaze-drawer-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    z-index: 1100;
    transition: all var(--blaze-transition-normal);
}

.blaze-drawer-overlay--open {
    opacity: 1;
    visibility: visible;
}

.blaze-drawer {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: var(--blaze-mobile-nav-width);
    background: var(--blaze-bg-white);
    z-index: 1200;
    transform: translateX(-100%);
    transition: transform var(--blaze-transition-normal);
    overflow-y: auto;
}

.blaze-drawer--open {
    transform: translateX(0);
}

.blaze-drawer__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid var(--blaze-border-light);
}

.blaze-drawer__login {
    flex: 1;
    margin-right: 1rem;
    text-align: center;
}

.blaze-drawer__close {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: var(--blaze-text-muted);
    cursor: pointer;
}

.blaze-drawer__toggle {
    display: flex;
    margin: 1rem;
    background: var(--blaze-bg);
    border-radius: var(--blaze-radius-full);
    padding: 4px;
}

.blaze-drawer__toggle-btn {
    flex: 1;
    padding: 0.5rem 1rem;
    background: transparent;
    border: none;
    border-radius: var(--blaze-radius-full);
    font-weight: var(--blaze-font-weight-medium);
    color: var(--blaze-text-muted);
    cursor: pointer;
    transition: all var(--blaze-transition-fast);
}

.blaze-drawer__toggle-btn--active {
    background: var(--blaze-primary);
    color: white;
}

.blaze-drawer__nav,
.blaze-drawer__links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.blaze-drawer__nav li a {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
    color: var(--blaze-text);
    text-decoration: none;
    font-weight: var(--blaze-font-weight-medium);
    transition: background var(--blaze-transition-fast);
}

.blaze-drawer__nav li a i {
    width: 24px;
    text-align: center;
    color: var(--blaze-primary);
}

.blaze-drawer__nav li a:hover {
    background: var(--blaze-bg);
}

.blaze-drawer__divider {
    border: none;
    border-top: 1px solid var(--blaze-border-light);
    margin: 1rem 0;
}

.blaze-drawer__links li a {
    display: block;
    padding: 0.75rem 1.5rem;
    color: var(--blaze-text-muted);
    text-decoration: none;
    font-size: var(--blaze-font-size-sm);
}

.blaze-drawer__links li a:hover {
    color: var(--blaze-primary);
}

/* ===== Main Content ===== */
.blaze-main {
    padding-top: var(--blaze-header-height);
    min-height: 100vh;
}

/* ===== Footer ===== */
.blaze-footer {
    background: var(--blaze-bg-dark);
    color: var(--blaze-text-inverse);
    padding: 3rem 1rem 1.5rem;
}

.blaze-footer__container {
    max-width: var(--blaze-container-max);
    margin: 0 auto;
}

.blaze-footer__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.blaze-footer__logo {
    height: 50px;
    width: auto;
}

.blaze-footer__logo-text {
    font-size: var(--blaze-font-size-xl);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-text-inverse);
}

.blaze-footer__social {
    display: flex;
    gap: 1rem;
}

.blaze-footer__social-link {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    color: var(--blaze-text-inverse);
    font-size: 1.125rem;
    text-decoration: none;
    transition: background var(--blaze-transition-fast);
}

.blaze-footer__social-link:hover {
    background: var(--blaze-primary);
}

.blaze-footer__links {
    display: flex;
    gap: 4rem;
    padding: 2rem 0;
}

.blaze-footer__col {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.blaze-footer__col a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: var(--blaze-font-size-sm);
    transition: color var(--blaze-transition-fast);
}

.blaze-footer__col a:hover {
    color: var(--blaze-text-inverse);
}

.blaze-footer__bottom {
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.blaze-footer__bottom p {
    color: rgba(255, 255, 255, 0.5);
    font-size: var(--blaze-font-size-sm);
    margin: 0;
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
    .blaze-header__tabs {
        display: none;
    }
    
    .blaze-header__menu-btn {
        display: block;
    }
}

@media (max-width: 768px) {
    .blaze-header__login {
        display: none;
    }
    
    .blaze-footer__links {
        flex-direction: column;
        gap: 2rem;
    }
    
    .blaze-footer__top {
        flex-direction: column;
        gap: 1.5rem;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .blaze-header__icon-btn {
        display: none;
    }
}
</style>
