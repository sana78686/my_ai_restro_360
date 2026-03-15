<template>
  <div class="checkout-container">
    <h2>Checkout</h2>
    <div class="checkout-form">
      <div class="form-section">
        <h3>Delivery Information</h3>
        <div class="form-group">
          <label for="name">Full Name</label>
          <input type="text" id="name" v-model="formData.name" required>
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" v-model="formData.email" required>
        </div>
        <div class="form-group">
          <label for="phone">Phone Number</label>
          <input type="tel" id="phone" v-model="formData.phone" required>
        </div>
        <div class="form-group">
          <label for="address">Delivery Address</label>
          <textarea id="address" v-model="formData.delivery_address" required></textarea>
        </div>
        <div class="form-group">
          <label for="instructions">Special Instructions</label>
          <textarea id="instructions" v-model="formData.special_instructions"></textarea>
        </div>
      </div>

      <div class="form-section">
        <h3>Payment Method</h3>
        <div class="payment-methods">
          <label v-for="method in paymentMethods" :key="method.value" class="payment-method">
            <input type="radio" v-model="formData.payment_method" :value="method.value">
            {{ method.label }}
          </label>
        </div>
      </div>

      <div class="order-summary">
        <h3>Order Summary</h3>
        <div v-for="item in cartItems" :key="item.id" class="order-item">
          <span>{{ item.product.name }} x {{ item.quantity }}</span>
          <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
        </div>
        <div class="total">
          <span>Total</span>
          <span>${{ totalAmount.toFixed(2) }}</span>
        </div>
      </div>

      <button @click="placeOrder" class="place-order-btn" :disabled="!isFormValid">
        Place Order
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      cartItems: [],
      totalAmount: 0,
      formData: {
        name: '',
        email: '',
        phone: '',
        delivery_address: '',
        special_instructions: '',
        payment_method: 'cash'
      },
      paymentMethods: [
        { value: 'cash', label: 'Cash on Delivery' },
        { value: 'card', label: 'Credit Card' },
        { value: 'online', label: 'Online Payment' }
      ]
    }
  },
  computed: {
    isFormValid() {
      return (
        this.formData.name &&
        this.formData.email &&
        this.formData.phone &&
        this.formData.delivery_address &&
        this.formData.payment_method
      )
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
    calculateTotal() {
      this.totalAmount = this.cartItems.reduce((total, item) => {
        return total + (item.price * item.quantity);
      }, 0);
    },
    async placeOrder() {
      try {
        const response = await axios.post('/orders/place', {
          ...this.formData,
          restaurant_id: this.$route.params.restaurantId
        });
        
        // Clear cart and redirect to order confirmation
        await axios.delete('/cart/clear');
        this.$router.push(`/orders/${response.data.id}`);
      } catch (error) {
        console.error('Error placing order:', error);
      }
    }
  }
}
</script>

<style scoped>
.checkout-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.form-section {
  margin-bottom: 30px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

input, textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

textarea {
  min-height: 100px;
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.payment-method {
  display: flex;
  align-items: center;
  gap: 10px;
}

.order-summary {
  margin: 20px 0;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.order-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.total {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #ddd;
  font-weight: bold;
}

.place-order-btn {
  width: 100%;
  padding: 15px;
  background-color: #4CAF50;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 16px;
}

.place-order-btn:disabled {
  background-color: #cccccc;
  cursor: not-allowed;
}
</style> 