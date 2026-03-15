<template>
  <div class="cart-container">
    <h2>Shopping Cart</h2>
    <div v-if="cartItems.length === 0" class="empty-cart">
      Your cart is empty
    </div>
    <div v-else class="cart-items">
      <div v-for="item in cartItems" :key="item.id" class="cart-item">
        <img :src="item.product.image" :alt="item.product.name" class="product-image">
        <div class="product-details">
          <h3>{{ item.product.name }}</h3>
          <p>{{ item.product.description }}</p>
          <div class="quantity-controls">
            <button @click="updateQuantity(item, item.quantity - 1)">-</button>
            <span>{{ item.quantity }}</span>
            <button @click="updateQuantity(item, item.quantity + 1)">+</button>
          </div>
          <p class="price">${{ (item.price * item.quantity).toFixed(2) }}</p>
        </div>
        <button @click="removeFromCart(item)" class="remove-btn">Remove</button>
      </div>
    </div>
    <div v-if="cartItems.length > 0" class="cart-summary">
      <h3>Total: ${{ totalAmount.toFixed(2) }}</h3>
      <button @click="proceedToCheckout" class="checkout-btn">Proceed to Checkout</button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cartItems: [],
      totalAmount: 0
    }
  },
  async created() {
    await this.fetchCartItems();
  },
  methods: {
    async fetchCartItems() {
      try {
        const response = await axios.get('/cart');
        this.cartItems = response.data;
        this.calculateTotal();
      } catch (error) {
        console.error('Error fetching cart items:', error);
      }
    },
    async updateQuantity(item, newQuantity) {
      if (newQuantity < 1) return;
      try {
        await axios.put(`/cart/update/${item.id}`, {
          quantity: newQuantity
        });
        item.quantity = newQuantity;
        this.calculateTotal();
      } catch (error) {
        console.error('Error updating quantity:', error);
      }
    },
    async removeFromCart(item) {
      try {
        await axios.delete(`/cart/remove/${item.id}`);
        this.cartItems = this.cartItems.filter(i => i.id !== item.id);
        this.calculateTotal();
      } catch (error) {
        console.error('Error removing item:', error);
      }
    },
    calculateTotal() {
      this.totalAmount = this.cartItems.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },
    async proceedToCheckout() {
      // Navigate to checkout page
      this.$router.push('/checkout');
    }
  }
}
</script>

<style scoped>
.cart-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.product-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  margin-right: 20px;
}

.product-details {
  flex: 1;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 10px;
  margin: 10px 0;
}

.quantity-controls button {
  padding: 5px 10px;
  border: 1px solid #ddd;
  background: none;
  cursor: pointer;
}

.remove-btn {
  padding: 8px 15px;
  background-color: #ff4444;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.cart-summary {
  margin-top: 20px;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
  text-align: right;
}

.checkout-btn {
  padding: 10px 20px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 10px;
}

.empty-cart {
  text-align: center;
  padding: 40px;
  color: #666;
}
</style> 