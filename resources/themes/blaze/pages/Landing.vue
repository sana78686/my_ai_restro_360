<template>
    <div class="blaze-landing">
        <!-- Hero Slider (Dynamic from banners) -->
        <section class="blaze-hero">
            <div class="blaze-hero__slider">
                <div 
                    class="blaze-hero__slide" 
                    v-for="(slide, index) in slides" 
                    :key="index"
                    :class="{ 'blaze-hero__slide--active': currentSlide === index }"
                    :style="{ backgroundImage: `url(${slide.image_url})` }"
                >
                    <div class="blaze-hero__content">
                        <span v-if="slide.badge" class="blaze-hero__badge">{{ slide.badge }}</span>
                        <h1 class="blaze-hero__title" v-html="slide.title"></h1>
                        <p class="blaze-hero__desc" v-if="slide.subtitle">{{ slide.subtitle }}</p>
                        <div class="blaze-hero__price" v-if="slide.price">
                            <span class="blaze-hero__price-currency">{{ currency }}</span>
                            <span class="blaze-hero__price-amount">{{ slide.price }}</span>
                        </div>
                        <router-link :to="slide.cta_link || '/menu'" class="blaze-btn blaze-btn--primary blaze-hero__cta">
                            {{ slide.cta_text || 'Order Now' }}
                        </router-link>
                    </div>
                </div>
            </div>
            <!-- Slider Navigation -->
            <div class="blaze-hero__dots">
                <button 
                    v-for="(_, index) in slides" 
                    :key="index"
                    class="blaze-hero__dot"
                    :class="{ 'blaze-hero__dot--active': currentSlide === index }"
                    @click="currentSlide = index"
                ></button>
            </div>
        </section>

        <!-- Explore Menu (Categories - Dynamic) -->
        <section class="blaze-section">
            <div class="blaze-container">
                <div class="blaze-section-title blaze-section-title--with-link">
                    <span>Explore Menu</span>
                    <router-link to="/menu" class="blaze-section-link">View All</router-link>
                </div>
                <div class="blaze-categories">
                    <router-link 
                        v-for="category in categories" 
                        :key="category.id"
                        :to="`/menu?category=${category.id}`"
                        class="blaze-category-card"
                    >
                        <div class="blaze-category-card__icon">
                            <img 
                                v-if="category.image" 
                                :src="category.image" 
                                :alt="category.name"
                            />
                            <i v-else class="fas fa-utensils"></i>
                        </div>
                        <span class="blaze-category-card__name">{{ category.name }}</span>
                    </router-link>
                </div>
            </div>
        </section>

        <!-- Best Sellers (Dynamic - Featured Products) -->
        <section class="blaze-section blaze-section--gray">
            <div class="blaze-container">
                <div class="blaze-section-title">Best Sellers</div>
                <div class="blaze-products-grid">
                    <div 
                        v-for="product in bestSellers" 
                        :key="product.id" 
                        class="blaze-product-card blaze-card"
                    >
                        <div class="blaze-product-card__badge" v-if="product.badge">
                            {{ product.badge }}
                        </div>
                        <div class="blaze-product-card__image">
                            <img :src="product.image" :alt="product.name" />
                        </div>
                        <div class="blaze-product-card__body">
                            <h3 class="blaze-product-card__name">{{ product.name }}</h3>
                            <div class="blaze-product-card__price">
                                {{ currency }} {{ product.price }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Top Deals (Static for now) -->
        <section class="blaze-section">
            <div class="blaze-container">
                <div class="blaze-section-title">Top Deals</div>
                <div class="blaze-deals-grid">
                    <div 
                        v-for="deal in topDeals" 
                        :key="deal.id" 
                        class="blaze-deal-card blaze-card"
                    >
                        <button class="blaze-deal-card__wishlist" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="blaze-deal-card__image">
                            <img :src="deal.image" :alt="deal.name" />
                        </div>
                        <div class="blaze-deal-card__body">
                            <h3 class="blaze-deal-card__name">{{ deal.name }}</h3>
                            <p class="blaze-deal-card__desc">{{ deal.description }}</p>
                            <div class="blaze-deal-card__footer">
                                <span class="blaze-deal-card__price">{{ currency }} {{ deal.price }}</span>
                                <button class="blaze-btn blaze-btn--primary blaze-btn--sm">
                                    + Add to Bucket
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Promo Banners (Static for now) -->
        <section class="blaze-section blaze-promos">
            <div class="blaze-container">
                <div class="blaze-promos-grid">
                    <div class="blaze-promo-card blaze-promo-card--dark">
                        <div class="blaze-promo-card__content">
                            <h3 class="blaze-promo-card__title">PICKUP<br/>MADE<br/>EASY</h3>
                            <p class="blaze-promo-card__desc">Order online and pick up at your nearest branch</p>
                        </div>
                        <div class="blaze-promo-card__image">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                    </div>
                    <div class="blaze-promo-card blaze-promo-card--red">
                        <div class="blaze-promo-card__content">
                            <h3 class="blaze-promo-card__title">VALUE<br/>BUCKET</h3>
                            <p class="blaze-promo-card__desc">More food than a regular combo!</p>
                            <div class="blaze-promo-card__price">
                                {{ currency }} 50 <small>onwards</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="blaze-promo-banner">
                    <div class="blaze-promo-banner__content">
                        <h3>Cravings have no limits!</h3>
                        <p>Adding 11 herbs and spices. Explore our menu and add items to your cart.</p>
                        <router-link to="/menu" class="blaze-btn blaze-btn--primary">
                            Explore Menu
                        </router-link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'
import '../assets/tokens.css'

export default {
    name: 'BlazeLanding',
    setup() {
        const currentSlide = ref(0)
        const categories = ref([])
        const bestSellers = ref([])
        const websiteSettings = ref({})
        const banners = ref([])
        const currency = computed(() => window.TENANT_CURRENCY || websiteSettings.value.currency_symbol || 'Rs')
        
        const defaultSlides = [
            {
                title: 'TURN IT UP<br/>& <span style="color: var(--blaze-accent)">SHAKE</span> IT UP',
                subtitle: '10 Hot Shots + 1 Chipotle Sauce + 1 Creamy Ranch Dip',
                price: '550',
                cta_text: 'Order Now',
                cta_link: '/menu',
                image_url: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=1200&h=600&fit=crop'
            },
            {
                title: 'CRISPY<br/><span style="color: var(--blaze-accent)">GOLDEN</span><br/>CHICKEN',
                subtitle: 'Fresh, hot, and incredibly delicious',
                price: '399',
                cta_text: 'Order Now',
                cta_link: '/menu',
                image_url: 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=1200&h=600&fit=crop'
            },
            {
                title: 'MEGA<br/><span style="color: var(--blaze-accent)">DEAL</span>',
                subtitle: 'Family bucket for sharing',
                price: '999',
                cta_text: 'Get Deal',
                cta_link: '/menu',
                image_url: 'https://images.unsplash.com/photo-1562967914-608f82629710?w=1200&h=600&fit=crop'
            }
        ]
        
        const slides = computed(() => {
            if (banners.value.length > 0) {
                return banners.value.map(b => ({
                    title: b.title,
                    subtitle: b.subtitle || b.description,
                    price: b.price,
                    cta_text: b.cta_text || 'Order Now',
                    cta_link: b.cta_link || '/menu',
                    image_url: b.image_url,
                    badge: b.badge
                }))
            }
            return defaultSlides
        })

        const topDeals = ref([
            {
                id: 1,
                name: 'Family Festival 3',
                description: 'An all-in-one meal for the fam. It includes 4 Zinger burgers + 4...',
                price: '2500',
                image: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=300&h=300&fit=crop'
            },
            {
                id: 2,
                name: 'Value Bucket',
                description: 'Enjoy 9 pcs of our Signature Crispy Fried Chicken, hand-breaded in...',
                price: '1800',
                image: 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=300&h=300&fit=crop'
            },
            {
                id: 3,
                name: 'Strips Slice N Rice',
                description: '4 Boneless Strips, Regular Fries, 2 Dips (Spicy & Ranch) and a...',
                price: '750',
                image: 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300&h=300&fit=crop'
            },
            {
                id: 4,
                name: 'Xtreme Box Duo',
                description: 'The irresistible combo of 2 Signature Zingers + 2 pcs Hot &...',
                price: '1560',
                image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=300&fit=crop'
            }
        ])

        let slideInterval = null

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

        const fetchBanners = async () => {
            try {
                const response = await axios.get('/tenant/banners', { 
                    params: { position: 'hero', active_only: true } 
                })
                if (response.data?.success && response.data.data?.length) {
                    banners.value = response.data.data
                }
            } catch (error) {
                console.log('Using default banners')
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

        const fetchCategories = async () => {
            try {
                const response = await axios.get('/tenant/public/categories')
                if (response.data?.data) {
                    categories.value = response.data.data.slice(0, 6).map(c => ({
                        id: c.id,
                        name: c.name,
                        image: c.image_url || c.image
                    }))
                }
            } catch (error) {
                categories.value = [
                    { id: 1, name: 'Promotion', image: null },
                    { id: 2, name: 'Everyday Value', image: null },
                    { id: 3, name: 'Ala Carte & Combos', image: null },
                    { id: 4, name: 'Signature Boxes', image: null },
                    { id: 5, name: 'Sharing', image: null },
                    { id: 6, name: 'Beverages', image: null }
                ]
            }
        }

        const fetchBestSellers = async () => {
            try {
                const response = await axios.get('/tenant/public/products', { params: { featured: 1, limit: 4 } })
                if (response.data?.data?.length) {
                    bestSellers.value = response.data.data.map(p => ({
                        id: p.id,
                        name: p.name,
                        price: p.price,
                        image: p.image_url || p.image || 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300&h=300&fit=crop',
                        badge: p.is_new ? 'NEW' : (p.is_featured ? 'BEST' : null)
                    }))
                } else {
                    throw new Error('No products')
                }
            } catch (error) {
                bestSellers.value = [
                    { id: 1, name: 'Krunch Burger', price: '310', badge: null, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=300&fit=crop' },
                    { id: 2, name: 'Mighty Zinger', price: '770', badge: 'HOT', image: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=300&h=300&fit=crop' },
                    { id: 3, name: 'Chicken & Chips', price: '650', badge: null, image: 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300&h=300&fit=crop' },
                    { id: 4, name: 'Hot Wings Bucket', price: '890', badge: 'BEST', image: 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=300&h=300&fit=crop' }
                ]
            }
        }

        onMounted(() => {
            fetchWebsiteSettings()
            fetchBanners()
            fetchCategories()
            fetchBestSellers()
            slideInterval = setInterval(() => {
                currentSlide.value = (currentSlide.value + 1) % slides.value.length
            }, 5000)
        })

        onUnmounted(() => {
            if (slideInterval) clearInterval(slideInterval)
        })

        return {
            currentSlide,
            slides,
            categories,
            bestSellers,
            topDeals,
            currency,
            websiteSettings
        }
    }
}
</script>

<style scoped>
.blaze-landing {
    background: var(--blaze-bg);
}

/* ===== Hero Slider ===== */
.blaze-hero {
    position: relative;
    height: 500px;
    overflow: hidden;
}

.blaze-hero__slider {
    position: relative;
    height: 100%;
}

.blaze-hero__slide {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 0.8s ease;
}

.blaze-hero__slide::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 50%, transparent 100%);
}

.blaze-hero__slide--active {
    opacity: 1;
}

.blaze-hero__content {
    position: relative;
    z-index: 1;
    max-width: var(--blaze-container-max);
    margin: 0 auto;
    padding: 0 2rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.blaze-hero__badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    background: var(--blaze-accent);
    color: var(--blaze-secondary);
    font-size: var(--blaze-font-size-xs);
    font-weight: var(--blaze-font-weight-bold);
    text-transform: uppercase;
    border-radius: var(--blaze-radius-sm);
    margin-bottom: 0.5rem;
}

.blaze-hero__title {
    font-size: var(--blaze-font-size-4xl);
    font-weight: var(--blaze-font-weight-black);
    color: white;
    line-height: 1.1;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.blaze-hero__desc {
    font-size: var(--blaze-font-size-lg);
    color: rgba(255,255,255,0.9);
    margin-bottom: 1rem;
    max-width: 400px;
}

.blaze-hero__price {
    margin-bottom: 1.5rem;
}

.blaze-hero__price-currency {
    font-size: var(--blaze-font-size-xl);
    color: white;
}

.blaze-hero__price-amount {
    font-size: var(--blaze-font-size-4xl);
    font-weight: var(--blaze-font-weight-black);
    color: var(--blaze-accent);
}

.blaze-hero__cta {
    width: fit-content;
}

.blaze-hero__dots {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.blaze-hero__dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    background: transparent;
    cursor: pointer;
    transition: all var(--blaze-transition-fast);
}

.blaze-hero__dot--active {
    background: var(--blaze-primary);
    border-color: var(--blaze-primary);
}

/* ===== Sections ===== */
.blaze-section {
    padding: var(--blaze-space-2xl) 0;
}

.blaze-section--gray {
    background: var(--blaze-border-light);
}

.blaze-container {
    max-width: var(--blaze-container-max);
    margin: 0 auto;
    padding: 0 1rem;
}

/* ===== Categories ===== */
.blaze-categories {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
}

.blaze-category-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    background: var(--blaze-bg-white);
    border-radius: var(--blaze-radius-lg);
    text-decoration: none;
    transition: all var(--blaze-transition-normal);
}

.blaze-category-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--blaze-shadow-md);
}

.blaze-category-card__icon {
    width: 70px;
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--blaze-primary-light);
    border-radius: 50%;
    margin-bottom: 0.75rem;
    overflow: hidden;
}

.blaze-category-card__icon img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blaze-category-card__icon i {
    font-size: 1.75rem;
    color: var(--blaze-primary);
}

.blaze-category-card__name {
    font-size: var(--blaze-font-size-sm);
    font-weight: var(--blaze-font-weight-medium);
    color: var(--blaze-text);
    text-align: center;
}

/* ===== Products Grid ===== */
.blaze-products-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.blaze-product-card {
    position: relative;
    padding: 1rem;
    text-align: center;
}

.blaze-product-card__badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    padding: 0.25rem 0.75rem;
    background: var(--blaze-primary);
    color: white;
    font-size: var(--blaze-font-size-xs);
    font-weight: var(--blaze-font-weight-bold);
    border-radius: var(--blaze-radius-sm);
}

.blaze-product-card__image {
    width: 150px;
    height: 150px;
    margin: 0 auto 1rem;
    border-radius: 50%;
    overflow: hidden;
    background: var(--blaze-bg);
}

.blaze-product-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blaze-product-card__name {
    font-size: var(--blaze-font-size-base);
    font-weight: var(--blaze-font-weight-semibold);
    color: var(--blaze-secondary);
    margin-bottom: 0.5rem;
}

.blaze-product-card__price {
    font-size: var(--blaze-font-size-lg);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-primary);
}

/* ===== Deals Grid ===== */
.blaze-deals-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
}

.blaze-deal-card {
    position: relative;
    display: flex;
    flex-direction: column;
}

.blaze-deal-card__wishlist {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
    border: 1px solid var(--blaze-border);
    border-radius: 50%;
    color: var(--blaze-primary);
    cursor: pointer;
    z-index: 2;
    transition: all var(--blaze-transition-fast);
}

.blaze-deal-card__wishlist:hover {
    background: var(--blaze-primary);
    color: white;
}

.blaze-deal-card__image {
    height: 180px;
    overflow: hidden;
}

.blaze-deal-card__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blaze-deal-card__body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blaze-deal-card__name {
    font-size: var(--blaze-font-size-base);
    font-weight: var(--blaze-font-weight-semibold);
    color: var(--blaze-secondary);
    margin-bottom: 0.5rem;
}

.blaze-deal-card__desc {
    font-size: var(--blaze-font-size-sm);
    color: var(--blaze-text-muted);
    margin-bottom: 1rem;
    flex: 1;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.blaze-deal-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.blaze-deal-card__price {
    font-size: var(--blaze-font-size-lg);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-secondary);
}

.blaze-btn--sm {
    padding: 0.5rem 0.75rem;
    font-size: var(--blaze-font-size-xs);
}

/* ===== Promo Section ===== */
.blaze-promos {
    background: var(--blaze-bg-white);
}

.blaze-promos-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.blaze-promo-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 2rem;
    border-radius: var(--blaze-radius-xl);
    min-height: 200px;
}

.blaze-promo-card--dark {
    background: var(--blaze-secondary);
    color: white;
}

.blaze-promo-card--red {
    background: var(--blaze-primary);
    color: white;
}

.blaze-promo-card__title {
    font-size: var(--blaze-font-size-2xl);
    font-weight: var(--blaze-font-weight-black);
    text-transform: uppercase;
    line-height: 1.1;
    margin-bottom: 0.5rem;
}

.blaze-promo-card__desc {
    font-size: var(--blaze-font-size-sm);
    opacity: 0.9;
    margin-bottom: 0.5rem;
}

.blaze-promo-card__price {
    font-size: var(--blaze-font-size-xl);
    font-weight: var(--blaze-font-weight-bold);
}

.blaze-promo-card__price small {
    font-size: var(--blaze-font-size-sm);
    font-weight: normal;
}

.blaze-promo-card__image i {
    font-size: 4rem;
    opacity: 0.3;
}

.blaze-promo-banner {
    background: linear-gradient(135deg, var(--blaze-primary-light) 0%, #fff 100%);
    border-radius: var(--blaze-radius-xl);
    padding: 2rem;
    text-align: center;
}

.blaze-promo-banner h3 {
    font-size: var(--blaze-font-size-2xl);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-secondary);
    margin-bottom: 0.5rem;
}

.blaze-promo-banner p {
    color: var(--blaze-text-muted);
    margin-bottom: 1rem;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* ===== Responsive ===== */
@media (max-width: 1200px) {
    .blaze-products-grid,
    .blaze-deals-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 992px) {
    .blaze-hero {
        height: 400px;
    }
    
    .blaze-hero__title {
        font-size: var(--blaze-font-size-3xl);
    }
    
    .blaze-categories {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .blaze-products-grid,
    .blaze-deals-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .blaze-promos-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .blaze-hero {
        height: 350px;
    }
    
    .blaze-hero__title {
        font-size: var(--blaze-font-size-2xl);
    }
    
    .blaze-hero__desc {
        font-size: var(--blaze-font-size-base);
    }
    
    .blaze-categories {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .blaze-section-title {
        font-size: var(--blaze-font-size-xl);
    }
}

@media (max-width: 576px) {
    .blaze-hero__content {
        padding: 0 1rem;
    }
    
    .blaze-products-grid,
    .blaze-deals-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .blaze-product-card__image {
        width: 120px;
        height: 120px;
    }
    
    .blaze-promo-card {
        flex-direction: column;
        text-align: center;
        padding: 1.5rem;
    }
    
    .blaze-promo-card__image {
        display: none;
    }
}
</style>
