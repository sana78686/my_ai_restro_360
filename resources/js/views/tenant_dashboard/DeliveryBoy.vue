<template>
  <div class="delivery-boy-orders">
    <!-- Availability Toggle -->
    <div class="availability-card">
      <div class="card">
        <div class="card-body">
          <h5>My Availability</h5>
          <div class="d-flex justify-content-between align-items-center">
            <span>I'm available for new orders</span>
            <div class="form-check form-switch">
              <input 
                v-model="isAvailable" 
                @change="updateAvailability"
                class="form-check-input" 
                :checked="isAvailable"
                type="checkbox" 
                style="transform: scale(1.5);"
              >
            </div>
          </div>
          <div class="mt-2">
            <small class="text-muted">
              Current deliveries: {{ currentDeliveryCount }}/3
              <span v-if="currentDeliveryCount >= 3" class="text-danger">(At capacity)</span>
            </small>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders List -->
    <div class="orders-list mt-4">
      <h5>My Orders ({{ orders.length }})</h5>
      
      <div v-if="loading" class="text-center">
        <div class="spinner-border"></div>
      </div>

      <div v-else-if="orders.length === 0" class="text-center text-muted">
        <p>No orders assigned to you</p>
      </div>

      <div v-else>
        <div v-for="order in orders" :key="order.id" class="order-card">
          <div class="order-header">
            <h6>Order #{{ order.order_number }}</h6>
            <span class="badge" :class="getStatusClass(order.status)">
              {{ order.status }}
            </span>
          </div>
          
          <div class="order-details">
            <p><strong>Customer:</strong> {{ order.customer_name }}</p>
            <p><strong>Phone:</strong> {{ order.customer_phone }}</p>
            <p><strong>Address:</strong> {{ order.delivery_address }}</p>
            <p><strong>Total:</strong> ${{ order.total_amount }}</p>
          </div>

          <div class="action-buttons">
            <!-- Simple Status Change Dropdown -->
            <select 
              v-model="order.status" 
              @change="updateStatus(order.id, order.status)"
              class="form-select form-select-sm"
              style="width: auto;"
            >
              <option value="assigned">Assigned</option>
              <option value="accepted">Accepted</option>
              <option value="picked_up">Picked Up</option>
              <option value="out_for_delivery">Out for Delivery</option>
              <option value="delivered">Delivered</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'DeliveryBoyOrders',
  setup() {
    const orders = ref([])
    const loading = ref(false)
    const isAvailable = ref(false)
    const currentDeliveryCount = ref(0)

    const fetchMyData = async () => {
      loading.value = true
      try {
        // Fetch orders
        const ordersResponse = await axios.get('/tenant/delivery-boy/orders')
        if (ordersResponse.data.success) {
          orders.value = ordersResponse.data.data
          currentDeliveryCount.value = orders.value.length
        }
        
        // You might want to fetch current availability status from user profile
        // For now, we'll derive it from order count
        // value from the customer field is_available_for_delivery
        const profileResponse = await axios.get('/tenant/delivery-boy/profile')
        if (profileResponse.data.success) {
          isAvailable.value = profileResponse.data.data.is_available_for_delivery
        }
        // isAvailable.value = currentDeliveryCount.value < 3 
        
      } catch (error) {
        console.error('Error fetching data:', error)
      } finally {
        loading.value = false
      }
    }

    const updateAvailability = async () => {
      try {
        const response = await axios.post('/tenant/delivery-boy/availability', {
          available: isAvailable.value
        })

        if (response.data.success) {
          Swal.fire({
            icon: 'success',
            title: isAvailable.value ? 'Available!' : 'Unavailable',
            text: response.data.message,
            timer: 1500,
            showConfirmButton: false
          })
          currentDeliveryCount.value = response.data.data.current_delivery_count
        }
      } catch (error) {
        // Revert toggle if failed
        isAvailable.value = !isAvailable.value
        Swal.fire('Error!', 'Failed to update availability', 'error')
      }
    }

    const updateStatus = async (orderId, status) => {
      try {
        const response = await axios.post('/tenant/delivery-boy/orders/update-status', {
          order_id: orderId,
          status: status
        })

        if (response.data.success) {
          Swal.fire('Success!', `Order status updated to ${status}`, 'success')
          // Update local counts
          currentDeliveryCount.value = response.data.data.current_delivery_count
          isAvailable.value = response.data.data.can_accept_more
        }
      } catch (error) {
        // Revert the select value if failed
        await fetchMyData() // Refresh to get correct status
        Swal.fire('Error!', error.response?.data?.message || 'Failed to update status', 'error')
      }
    }

    const getStatusClass = (status) => {
      const classes = {
        'assigned': 'bg-warning',
        'accepted': 'bg-info', 
        'picked_up': 'bg-primary',
        'out_for_delivery': 'bg-secondary',
        'delivered': 'bg-success',
        'cancelled': 'bg-danger'
      }
      return classes[status] || 'bg-secondary'
    }

    onMounted(() => {
      fetchMyData()
      // Refresh every 30 seconds
      setInterval(fetchMyData, 30000)
    })

    return {
      orders,
      loading,
      isAvailable,
      currentDeliveryCount,
      updateAvailability,
      updateStatus,
      getStatusClass
    }
  }
}
</script>

<style scoped>
.availability-card {
  margin-bottom: 20px;
}

.order-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 15px;
  background: white;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.action-buttons {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  margin-top: 10px;
}

/* Make the toggle switch larger */
.form-check-input {
  height: 1.5em;
  width: 3em;
}
</style>