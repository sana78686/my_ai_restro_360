import { reactive, computed } from 'vue'

const cart = reactive({
  items: []
})

function addToCart(product) {
  const found = cart.items.find(item => item.id === product.id)
  if (found) {
    found.quantity += 1
  } else {
    cart.items.push({ ...product, quantity: 1 })
  }
}

function removeFromCart(productId) {
  const idx = cart.items.findIndex(item => item.id === productId)
  if (idx !== -1) cart.items.splice(idx, 1)
}

function clearCart() {
  cart.items = []
}

const cartCount = computed(() => cart.items.length)
const totalItems = computed(() => cart.items.reduce((sum, item) => sum + item.quantity, 0))

export function useCart() {
  return {
    cart,
    addToCart,
    removeFromCart,
    clearCart,
    cartCount,
    totalItems
  }
} 