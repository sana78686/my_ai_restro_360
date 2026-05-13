<template>
    <div class="blaze-menu-page">
        <!-- Category Tabs -->
        <div class="blaze-menu-tabs">
            <div class="blaze-menu-tabs__container">
                <button
                    v-for="cat in categories"
                    :key="cat.id"
                    class="blaze-menu-tabs__tab"
                    :class="{ 
                        'blaze-menu-tabs__tab--active': activeCategory === cat.id,
                        'blaze-menu-tabs__tab--special': cat.special
                    }"
                    @click="selectCategory(cat.id)"
                >
                    {{ cat.name }}
                </button>
            </div>
        </div>

        <div class="blaze-menu-layout">
            <!-- Products Section -->
            <div class="blaze-menu-content">
                <h1 class="blaze-menu-content__title">{{ activeCategoryName }}</h1>
                
                <div class="blaze-menu-products">
                    <div 
                        v-for="product in filteredProducts" 
                        :key="product.id" 
                        class="blaze-menu-product blaze-card"
                    >
                        <button class="blaze-menu-product__wishlist" aria-label="Add to wishlist">
                            <i class="far fa-heart"></i>
                        </button>
                        <div class="blaze-menu-product__image">
                            <img :src="product.image" :alt="product.name" />
                        </div>
                        <div class="blaze-menu-product__body">
                            <h3 class="blaze-menu-product__name">{{ product.name }}</h3>
                            <p class="blaze-menu-product__desc">{{ product.description }}</p>
                            <div class="blaze-menu-product__footer">
                                <span class="blaze-menu-product__price">{{ currency }} {{ product.price }}</span>
                                <button 
                                    class="blaze-btn blaze-btn--primary blaze-btn--sm"
                                    @click="addToCart(product)"
                                >
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Cart Sidebar -->
            <aside class="blaze-sticky-cart">
                <div class="blaze-sticky-cart__inner">
                    <div class="blaze-sticky-cart__header">
                        <h2>Order Details</h2>
                        <div class="blaze-sticky-cart__dots">
                            <span></span><span></span><span></span>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="!cartItems.length" class="blaze-sticky-cart__empty">
                        <div class="blaze-sticky-cart__empty-icon">
                            <i class="fas fa-shopping-basket"></i>
                        </div>
                        <p>You haven't added any items in bucket yet</p>
                    </div>

                    <!-- Cart Items -->
                    <div v-else class="blaze-sticky-cart__items">
                        <div 
                            v-for="item in cartItems" 
                            :key="item.id" 
                            class="blaze-cart-item"
                        >
                            <div class="blaze-cart-item__image">
                                <img :src="item.image" :alt="item.name" />
                            </div>
                            <div class="blaze-cart-item__info">
                                <h4>{{ item.name }}</h4>
                                <span class="blaze-cart-item__price">{{ currency }} {{ item.price }}</span>
                            </div>
                            <div class="blaze-cart-item__qty">
                                <button @click="updateQty(item.id, -1)">-</button>
                                <span>{{ item.quantity }}</span>
                                <button @click="updateQty(item.id, 1)">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Footer -->
                    <div class="blaze-sticky-cart__footer">
                        <div class="blaze-sticky-cart__summary">
                            <span>{{ totalItems }} Item{{ totalItems !== 1 ? 's' : '' }}</span>
                            <span class="blaze-sticky-cart__total">{{ currency }} {{ cartTotal }}</span>
                        </div>
                        <router-link 
                            to="/checkout" 
                            class="blaze-btn blaze-btn--primary blaze-sticky-cart__btn"
                            :class="{ 'blaze-btn--disabled': !cartItems.length }"
                        >
                            View Bucket <i class="fas fa-chevron-right"></i>
                        </router-link>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</template>

<script>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import '../assets/tokens.css'

export default {
    name: 'BlazeMenu',
    setup() {
        const route = useRoute()
        const categories = ref([])
        const products = ref([])
        const cartItems = ref([])
        const activeCategory = ref(null)
        const currency = computed(() => window.TENANT_CURRENCY || 'Rs')

        const activeCategoryName = computed(() => {
            const cat = categories.value.find(c => c.id === activeCategory.value)
            return cat?.name || 'All Menu'
        })

        const filteredProducts = computed(() => {
            if (!activeCategory.value) return products.value
            return products.value.filter(p => p.category_id === activeCategory.value)
        })

        const totalItems = computed(() => 
            cartItems.value.reduce((sum, item) => sum + item.quantity, 0)
        )

        const cartTotal = computed(() => 
            cartItems.value.reduce((sum, item) => sum + (item.price * item.quantity), 0)
        )

        const selectCategory = (catId) => {
            activeCategory.value = catId
        }

        const addToCart = (product) => {
            const existing = cartItems.value.find(i => i.id === product.id)
            if (existing) {
                existing.quantity++
            } else {
                cartItems.value.push({ ...product, quantity: 1 })
            }
            saveCart()
        }

        const updateQty = (productId, delta) => {
            const item = cartItems.value.find(i => i.id === productId)
            if (!item) return
            item.quantity += delta
            if (item.quantity <= 0) {
                cartItems.value = cartItems.value.filter(i => i.id !== productId)
            }
            saveCart()
        }

        const saveCart = () => {
            localStorage.setItem('cart', JSON.stringify(cartItems.value))
        }

        const loadCart = () => {
            try {
                cartItems.value = JSON.parse(localStorage.getItem('cart') || '[]')
            } catch {
                cartItems.value = []
            }
        }

        const fetchCategories = async () => {
            try {
                const response = await axios.get('/api/categories')
                if (response.data?.data) {
                    categories.value = [
                        { id: 'midnight', name: 'Midnight (Start at 12 AM)', special: true },
                        ...response.data.data.map(c => ({ id: c.id, name: c.name }))
                    ]
                    if (route.query.category) {
                        activeCategory.value = parseInt(route.query.category) || null
                    } else if (categories.value.length > 1) {
                        activeCategory.value = categories.value[1].id
                    }
                }
            } catch (error) {
                categories.value = [
                    { id: 'midnight', name: 'Midnight (Start at 12 AM)', special: true },
                    { id: 1, name: 'Promotion' },
                    { id: 2, name: 'Everyday Value' },
                    { id: 3, name: 'Ala-Carte-&-Combos' },
                    { id: 4, name: 'Signature-Boxes' },
                    { id: 5, name: 'Sharing' },
                    { id: 6, name: 'Snacks-&-Beverages' },
                    { id: 7, name: 'Condiments' }
                ]
                activeCategory.value = 1
            }
        }

        const fetchProducts = async () => {
            try {
                const response = await axios.get('/api/products')
                if (response.data?.data) {
                    products.value = response.data.data.map(p => ({
                        id: p.id,
                        category_id: p.category_id,
                        name: p.name,
                        description: p.description || '',
                        price: parseFloat(p.price) || 0,
                        image: p.image_url || p.image || 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=300&fit=crop'
                    }))
                } else {
                    throw new Error('No products')
                }
            } catch (error) {
                products.value = [
                    { id: 1, category_id: 1, name: 'Midnight Deal 1', description: '1 Zinger burger + 1 regular drink', price: 520, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=300&fit=crop' },
                    { id: 2, category_id: 1, name: 'Midnight Deal 2', description: '2 Krunch burgers + 2 regular drinks', price: 610, image: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=300&h=300&fit=crop' },
                    { id: 3, category_id: 1, name: 'Midnight Deal 3', description: 'Mighty Zinger + Regular drink', price: 710, image: 'https://images.unsplash.com/photo-1606755962773-d324e0a13086?w=300&h=300&fit=crop' },
                    { id: 4, category_id: 2, name: 'Value Burger', description: 'Classic chicken burger at a great price', price: 250, image: 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&h=300&fit=crop' },
                    { id: 5, category_id: 2, name: 'Snack Box', description: '2 pcs chicken + fries + drink', price: 450, image: 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=300&h=300&fit=crop' },
                    { id: 6, category_id: 3, name: 'Zinger Burger', description: 'Spicy chicken fillet in a fresh bun', price: 550, image: 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=300&h=300&fit=crop' }
                ]
            }
        }

        watch(() => route.query.category, (newVal) => {
            if (newVal) {
                activeCategory.value = parseInt(newVal) || null
            }
        })

        onMounted(() => {
            loadCart()
            fetchCategories()
            fetchProducts()
        })

        return {
            categories,
            products,
            cartItems,
            activeCategory,
            activeCategoryName,
            filteredProducts,
            totalItems,
            cartTotal,
            currency,
            selectCategory,
            addToCart,
            updateQty
        }
    }
}
</script>

<style scoped>
.blaze-menu-page {
    background: var(--blaze-bg);
    min-height: calc(100vh - var(--blaze-header-height));
}

/* ===== Category Tabs ===== */
.blaze-menu-tabs {
    background: var(--blaze-bg-white);
    border-bottom: 1px solid var(--blaze-border);
    position: sticky;
    top: var(--blaze-header-height);
    z-index: 100;
}

.blaze-menu-tabs__container {
    max-width: calc(var(--blaze-container-max) - var(--blaze-cart-width) - 2rem);
    margin: 0 auto;
    padding: 0 1rem;
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    scrollbar-width: none;
}

.blaze-menu-tabs__container::-webkit-scrollbar {
    display: none;
}

.blaze-menu-tabs__tab {
    flex-shrink: 0;
    padding: 1rem 1.25rem;
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: var(--blaze-font-size-sm);
    font-weight: var(--blaze-font-weight-medium);
    color: var(--blaze-text-muted);
    cursor: pointer;
    white-space: nowrap;
    transition: all var(--blaze-transition-fast);
}

.blaze-menu-tabs__tab:hover {
    color: var(--blaze-text);
}

.blaze-menu-tabs__tab--active {
    color: var(--blaze-text);
    border-bottom-color: var(--blaze-primary);
}

.blaze-menu-tabs__tab--special {
    background: var(--blaze-primary);
    color: white;
    border-radius: var(--blaze-radius-md);
    margin: 0.5rem 0;
}

.blaze-menu-tabs__tab--special:hover,
.blaze-menu-tabs__tab--special.blaze-menu-tabs__tab--active {
    background: var(--blaze-primary-dark);
    color: white;
    border-bottom-color: transparent;
}

/* ===== Layout ===== */
.blaze-menu-layout {
    display: flex;
    max-width: var(--blaze-container-max);
    margin: 0 auto;
    gap: 2rem;
    padding: 2rem 1rem;
}

.blaze-menu-content {
    flex: 1;
    min-width: 0;
}

.blaze-menu-content__title {
    font-size: var(--blaze-font-size-2xl);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-secondary);
    text-transform: uppercase;
    margin-bottom: 1.5rem;
}

/* ===== Products Grid ===== */
.blaze-menu-products {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}

.blaze-menu-product {
    position: relative;
    display: flex;
    flex-direction: column;
}

.blaze-menu-product__wishlist {
    position: absolute;
    top: 1rem;
    right: 1rem;
    width: 32px;
    height: 32px;
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

.blaze-menu-product__wishlist:hover {
    background: var(--blaze-primary);
    color: white;
}

.blaze-menu-product__image {
    height: 180px;
    overflow: hidden;
    border-radius: var(--blaze-radius-lg) var(--blaze-radius-lg) 0 0;
}

.blaze-menu-product__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blaze-menu-product__body {
    padding: 1rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.blaze-menu-product__name {
    font-size: var(--blaze-font-size-base);
    font-weight: var(--blaze-font-weight-semibold);
    color: var(--blaze-secondary);
    margin-bottom: 0.25rem;
}

.blaze-menu-product__desc {
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

.blaze-menu-product__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.blaze-menu-product__price {
    font-size: var(--blaze-font-size-lg);
    font-weight: var(--blaze-font-weight-bold);
    color: var(--blaze-secondary);
}

/* ===== Sticky Cart ===== */
.blaze-sticky-cart {
    width: var(--blaze-cart-width);
    flex-shrink: 0;
}

.blaze-sticky-cart__inner {
    position: sticky;
    top: calc(var(--blaze-header-height) + 4rem);
    background: var(--blaze-bg-white);
    border-radius: var(--blaze-radius-xl);
    box-shadow: var(--blaze-shadow-md);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    max-height: calc(100vh - var(--blaze-header-height) - 6rem);
}

.blaze-sticky-cart__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--blaze-border-light);
}

.blaze-sticky-cart__header h2 {
    font-size: var(--blaze-font-size-lg);
    font-weight: var(--blaze-font-weight-semibold);
    color: var(--blaze-secondary);
    margin: 0;
}

.blaze-sticky-cart__dots {
    display: flex;
    gap: 4px;
}

.blaze-sticky-cart__dots span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--blaze-primary);
}

/* ===== Empty State ===== */
.blaze-sticky-cart__empty {
    padding: 3rem 1.5rem;
    text-align: center;
}

.blaze-sticky-cart__empty-icon {
    width: 100px;
    height: 100px;
    margin: 0 auto 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--blaze-bg);
    border-radius: 50%;
}

.blaze-sticky-cart__empty-icon i {
    font-size: 2.5rem;
    color: var(--blaze-text-light);
}

.blaze-sticky-cart__empty p {
    color: var(--blaze-text-muted);
    font-size: var(--blaze-font-size-sm);
    margin: 0;
}

/* ===== Cart Items ===== */
.blaze-sticky-cart__items {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.blaze-cart-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--blaze-border-light);
}

.blaze-cart-item:last-child {
    border-bottom: none;
}

.blaze-cart-item__image {
    width: 50px;
    height: 50px;
    border-radius: var(--blaze-radius-md);
    overflow: hidden;
    flex-shrink: 0;
}

.blaze-cart-item__image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.blaze-cart-item__info {
    flex: 1;
    min-width: 0;
}

.blaze-cart-item__info h4 {
    font-size: var(--blaze-font-size-sm);
    font-weight: var(--blaze-font-weight-medium);
    color: var(--blaze-secondary);
    margin: 0 0 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.blaze-cart-item__price {
    font-size: var(--blaze-font-size-sm);
    color: var(--blaze-text-muted);
}

.blaze-cart-item__qty {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.blaze-cart-item__qty button {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--blaze-bg);
    border: 1px solid var(--blaze-border);
    border-radius: var(--blaze-radius-sm);
    color: var(--blaze-text);
    cursor: pointer;
    font-weight: bold;
}

.blaze-cart-item__qty span {
    min-width: 20px;
    text-align: center;
    font-weight: var(--blaze-font-weight-medium);
}

/* ===== Cart Footer ===== */
.blaze-sticky-cart__footer {
    padding: 1rem 1.25rem;
    background: var(--blaze-secondary);
    border-radius: 0 0 var(--blaze-radius-xl) var(--blaze-radius-xl);
}

.blaze-sticky-cart__summary {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
    color: rgba(255,255,255,0.8);
    font-size: var(--blaze-font-size-sm);
}

.blaze-sticky-cart__total {
    font-weight: var(--blaze-font-weight-bold);
    color: white;
}

.blaze-sticky-cart__btn {
    width: 100%;
    justify-content: center;
}

.blaze-btn--disabled {
    opacity: 0.5;
    pointer-events: none;
}

/* ===== Responsive ===== */
@media (max-width: 1200px) {
    .blaze-menu-products {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .blaze-menu-layout {
        flex-direction: column;
    }
    
    .blaze-sticky-cart {
        width: 100%;
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 100;
    }
    
    .blaze-sticky-cart__inner {
        position: static;
        max-height: none;
        border-radius: var(--blaze-radius-xl) var(--blaze-radius-xl) 0 0;
    }
    
    .blaze-sticky-cart__header,
    .blaze-sticky-cart__empty,
    .blaze-sticky-cart__items {
        display: none;
    }
    
    .blaze-sticky-cart__footer {
        border-radius: var(--blaze-radius-xl) var(--blaze-radius-xl) 0 0;
    }
    
    .blaze-menu-content {
        padding-bottom: 100px;
    }
    
    .blaze-menu-tabs__container {
        max-width: 100%;
    }
}

@media (max-width: 768px) {
    .blaze-menu-products {
        grid-template-columns: 1fr;
    }
}
</style>
