<template>
  <div class="order-details-container">
    <div v-if="loading" class="loading">Loading...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="order-info">
      <h2>Order #{{ order.tracking_id }}</h2>
      
      <div class="order-status">
        <h3>Order Status</h3>
        <div class="status-badge" :class="order.status">
          {{ order.status }}
        </div>
      </div>

      <div class="order-items">
        <h3>Order Items</h3>
        <div v-for="item in order.order_details" :key="item.id" class="order-item">
          <img :src="item.product.image" :alt="item.product.name" class="product-image">
          <div class="item-details">
            <h4>{{ item.product.name }}</h4>
            <p>Quantity: {{ item.quantity }}</p>
            <p>Price: ${{ item.price }}</p>
          </div>
          <div class="item-total">
            ${{ (item.price * item.quantity).toFixed(2) }}
          </div>
        </div>
      </div>

      <div class="order-summary">
        <h3>Order Summary</h3>
        <div class="summary-item">
          <span>Subtotal</span>
          <span>${{ order.total_amount.toFixed(2) }}</span>
        </div>
        <div class="summary-item">
          <span>Delivery Fee</span>
          <span>${{ deliveryFee.toFixed(2) }}</span>
        </div>
        <div class="summary-item total">
          <span>Total</span>
          <span>${{ (order.total_amount + deliveryFee).toFixed(2) }}</span>
        </div>
      </div>

      <div class="delivery-info">
        <h3>Delivery Information</h3>
        <p><strong>Name:</strong> {{ order.customer.name }}</p>
        <p><strong>Phone:</strong> {{ order.customer.phone }}</p>
        <p><strong>Address:</strong> {{ order.delivery_address }}</p>
        <p v-if="order.special_instructions">
          <strong>Special Instructions:</strong> {{ order.special_instructions }}
        </p>
      </div>

      <div class="payment-info">
        <h3>Payment Information</h3>
        <p><strong>Payment Method:</strong> {{ order.payment_method }}</p>
        <p><strong>Payment Status:</strong> {{ order.payment_status }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      order: null,
      loading: true,
      error: null,
      deliveryFee: 5.00 // This could be dynamic based on restaurant settings
    }
  },
  async created() {
    await this.fetchOrderDetails();
  },
  methods: {
    async fetchOrderDetails() {
      try {
        const response = await axios.get(`/orders/${this.$route.params.id}`);
        this.order = response.data;
        this.loading = false;
      } catch (error) {
        this.error = 'Error fetching order details';
        this.loading = false;
        console.error('Error:', error);
      }
    }
  }
}
</script>

<style scoped>
.order-details-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
}

.error {
  color: #ff4444;
}

.order-info {
  background-color: white;
  border-radius: 8px;
  padding: 20px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-status {
  margin: 20px 0;
  padding: 15px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.status-badge {
  display: inline-block;
  padding: 5px 10px;
  border-radius: 4px;
  color: white;
  font-weight: bold;
}

.status-badge.pending {
  background-color: #ffc107;
}

.status-badge.confirmed {
  background-color: #17a2b8;
}

.status-badge.preparing {
  background-color: #007bff;
}

.status-badge.ready {
  background-color: #28a745;
}

.status-badge.delivered {
  background-color: #6c757d;
}

.status-badge.cancelled {
  background-color: #dc3545;
}

.order-item {
  display: flex;
  align-items: center;
  padding: 15px;
  border-bottom: 1px solid #eee;
}

.product-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 4px;
  margin-right: 15px;
}

.item-details {
  flex: 1;
}

.item-total {
  font-weight: bold;
}

.order-summary {
  margin: 20px 0;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.summary-item.total {
  font-weight: bold;
  font-size: 1.2em;
  border-top: 1px solid #ddd;
  padding-top: 10px;
  margin-top: 10px;
}

.delivery-info, .payment-info {
  margin: 20px 0;
  padding: 20px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

h3 {
  margin-bottom: 15px;
  color: #333;
}
</style> 