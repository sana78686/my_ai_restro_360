<template>
  <div class="orders-list-container">
    <div class="header">
      <h2>Orders Management</h2>
      <div class="filters">
        <select v-model="statusFilter" @change="filterOrders">
          <option value="">All Statuses</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="preparing">Preparing</option>
          <option value="ready">Ready</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <input 
          type="text" 
          v-model="searchQuery" 
          placeholder="Search by tracking ID or customer name"
          @input="filterOrders"
        >
      </div>
    </div>

    <div v-if="loading" class="loading">Loading orders...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="orders-table">
      <table>
        <thead>
          <tr>
            <th>Tracking ID</th>
            <th>Customer</th>
            <th>Total Amount</th>
            <th>Status</th>
            <th>Payment Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="order in filteredOrders" :key="order.id">
            <td>{{ order.tracking_id }}</td>
            <td>{{ order.customer.name }}</td>
            <td>${{ order.total_amount.toFixed(2) }}</td>
            <td>
              <select v-model="order.status" @change="updateOrderStatus(order)">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="preparing">Preparing</option>
                <option value="ready">Ready</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </td>
            <td>
              <span :class="['payment-status', order.payment_status]">
                {{ order.payment_status }}
              </span>
            </td>
            <td>{{ formatDate(order.created_at) }}</td>
            <td>
              <button @click="viewOrderDetails(order)" class="view-btn">View</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedOrder" class="order-details-modal">
      <div class="modal-content">
        <h3>Order Details</h3>
        <div class="order-info">
          <p><strong>Tracking ID:</strong> {{ selectedOrder.tracking_id }}</p>
          <p><strong>Customer:</strong> {{ selectedOrder.customer.name }}</p>
          <p><strong>Phone:</strong> {{ selectedOrder.customer.phone }}</p>
          <p><strong>Address:</strong> {{ selectedOrder.delivery_address }}</p>
        </div>
        <div class="order-items">
          <h4>Order Items</h4>
          <div v-for="item in selectedOrder.order_details" :key="item.id" class="item">
            <span>{{ item.product.name }} x {{ item.quantity }}</span>
            <span>${{ (item.price * item.quantity).toFixed(2) }}</span>
          </div>
        </div>
        <button @click="selectedOrder = null" class="close-btn">Close</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      orders: [],
      loading: true,
      error: null,
      statusFilter: '',
      searchQuery: '',
      selectedOrder: null
    }
  },
  computed: {
    filteredOrders() {
      let filtered = this.orders;
      
      if (this.statusFilter) {
        filtered = filtered.filter(order => order.status === this.statusFilter);
      }
      
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(order => 
          order.tracking_id.toLowerCase().includes(query) ||
          order.customer.name.toLowerCase().includes(query)
        );
      }
      
      return filtered;
    }
  },
  async created() {
    await this.fetchOrders();
  },
  methods: {
    async fetchOrders() {
      try {
        const response = await axios.get('/admin/orders');
        this.orders = response.data;
        this.loading = false;
      } catch (error) {
        this.error = 'Error fetching orders';
        this.loading = false;
        console.error('Error:', error);
      }
    },
    async updateOrderStatus(order) {
      try {
        await axios.put(`/admin/orders/${order.id}/status`, {
          status: order.status
        });
        // Show success message
      } catch (error) {
        console.error('Error updating order status:', error);
        // Show error message
      }
    },
    viewOrderDetails(order) {
      this.selectedOrder = order;
    },
    formatDate(date) {
      return new Date(date).toLocaleDateString();
    }
  }
}
</script>

<style scoped>
.orders-list-container {
  padding: 20px;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 10px;
}

.filters select, .filters input {
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.orders-table {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #eee;
}

th {
  background-color: #f8f9fa;
  font-weight: bold;
}

select {
  padding: 5px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.payment-status {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.9em;
}

.payment-status.paid {
  background-color: #28a745;
  color: white;
}

.payment-status.pending {
  background-color: #ffc107;
  color: black;
}

.payment-status.failed {
  background-color: #dc3545;
  color: white;
}

.view-btn {
  padding: 6px 12px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.order-details-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-content {
  background: white;
  padding: 20px;
  border-radius: 8px;
  max-width: 600px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.close-btn {
  margin-top: 20px;
  padding: 8px 16px;
  background-color: #6c757d;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.loading, .error {
  text-align: center;
  padding: 40px;
  font-size: 18px;
}

.error {
  color: #dc3545;
}
</style> 