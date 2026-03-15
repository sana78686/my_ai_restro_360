<template>
  <div class="Orders-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.orders.title') }}</h4>
    </div>
    <!-- Filters + Column Filter Row -->
    <div class="mb-4">
      <div class="filter-body row g-3 align-items-end flex-wrap">
        <div class="col-12 col-md-4 mb-2 mb-md-0">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.orders.search') }}</label>
          <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input"
            :placeholder="$t('tenant_dashboard.orders.searchPlaceholder')">
          </div>
        <div class="col-12 col-md-3 ms-auto d-flex justify-content-md-end justify-content-start align-items-end">
          <!-- Column Filter Dropdown -->
          <div class="dropdown column-filter-dropdown w-100 w-md-auto">
            <button class="btn btn-theme btn-sm dropdown-toggle w-100" type="button" id="orderColumnDropdown"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-columns"></i> {{ $t('tenant_dashboard.orders.columns') }}
            </button>
            <ul class="dropdown-menu theme-dropdown shadow-lg p-2" aria-labelledby="orderColumnDropdown"
              style="min-width:220px;">
              <li v-for="col in allColumns" :key="col.key">
                <label class="dropdown-item mb-0 d-flex align-items-center gap-2">
                  <input type="checkbox" v-model="tempSelectedColumns" :value="col.key" class="form-check-input" />
                  <span>{{ col.label }}</span>
                </label>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li>
                <button class="btn btn-theme w-100" @click="applyColumnSelection">{{ $t('tenant_dashboard.orders.apply') }}</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <!-- Orders Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="orders.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-store fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.orders.noOrders') }}</div>
          <div>{{ $t('tenant_dashboard.orders.noOrdersMessage') }}</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm orders-table">
            <thead>
              <tr>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders" :key="order.id">
                <td v-for="col in visibleColumns" :key="col.key">
                  <template v-if="col.key === 'customer_info'">
                    <div class="fw-bold">{{ order.customer?.name || '-' }}</div>
                    <div class="small text-muted">
                      {{ $t('tenant_dashboard.orders.email') }}: {{ order.customer?.email || '-' }}
                    </div>
                    <div class="small text-muted">
                      {{ $t('tenant_dashboard.orders.phone') }}: {{ order.customer?.phone || '-' }}
                    </div>
                  </template>
                  <template v-else-if="col.key === 'tracking_id'">
                    {{ order.tracking_id || '-' }}
                    <button v-if="order.tracking_id" class="btn btn-link btn-copy ms-1 p-0" @click.stop="copyToClipboard(order.tracking_id, 'Tracking ID')" :title="$t('tenant_dashboard.orders.copyTrackingId')"><i class="fas fa-copy"></i></button>
                  </template>
                  <template v-else-if="col.key === 'total_price'">
                    <span v-if="order.total_amount">
                      {{ order.currency_symbol }}&nbsp;{{ Number(order.total_amount).toFixed(2) }}
                      <span v-if="order.applied_discount && Number(order.applied_discount) > 0"
                        class="text-success small ms-2">{{ Number(order.applied_discount).toFixed(2) }}% {{ $t('tenant_dashboard.orders.off') }}
                      </span>
                    </span>
                    <span v-else>-</span>
                  </template>
                  <template v-else-if="col.key === 'status'">
                    <select class="form-select form-select-sm w-auto d-inline-block" v-model="order.status"
                      @change="updateOrderStatus(order, order.status)">
                      <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                    <!-- Assign Delivery Boy Button - Only show for completed orders -->
                    <!-- <button 
                      v-if="order.status === 'completed' && !order.delivery_boy_id"
                      class="btn btn-success btn-sm ms-2"
                      @click="openAssignDeliveryModal(order)"
                      title="Assign Delivery Boy"
                    >
                      <i class="fas fa-motorcycle"></i> Assign Delivery
                    </button> -->
                    <!-- <span 
                      v-else-if="order.delivery_boy_id" 
                      class="badge bg-info ms-2"
                    >
                      <i class="fas fa-check"></i> Assigned
                    </span> -->
                  </template>
                  <!-- order type is added here  -->
                  <template v-else-if="col.key === 'order_type'">
                    <span>
                      {{
                        order.order_type === 'in_house' ? 'In House' :
                        order.order_type === 'online' ? 'Online' :
                        order.order_type === 'takeaway' ? 'Takeaway' :
                        'Default'
                      }}
                    </span>
                  </template>
                  <template v-else-if="col.key === 'created_at'">
                    {{ new Date(order.created_at).toLocaleDateString() }}
                  </template>
                  <template v-else-if="col.key === 'actions'">
                    <button class="btn btn-sm btn-theme" @click="viewOrderDetails(order)"><i class="fas fa-eye"></i>
                      {{ $t('tenant_dashboard.orders.viewDetails') }}</button>
                    <Button 
                      size="btn btn-sm btn-theme" 
                      variant="outline" 
                      @click="downloadInvoice(order.id)"
                    ><i class="fas fa-download"></i>
                      {{ $t('tenant_dashboard.orders.downloadInvoice') }}
                    </Button>                              
                  </template>
                  
                  <template v-else>
                    {{ order[col.key] }}
                  </template>
                  </td>
                </tr>
            </tbody>
          </table>
          <!-- Pagination Controls -->
          <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
              {{ $t('tenant_dashboard.orders.showingEntries', { from: pagination.from, to: pagination.to, total: pagination.total }) }}
            </div>
            <nav v-if="pagination.last_page > 1">
              <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                  <button class="page-link" @click="goToPage(pagination.current_page - 1)"
                    :disabled="pagination.current_page === 1">&laquo;</button>
                </li>
                <li v-for="page in paginationWindow" :key="page" class="page-item"
                  :class="{ active: page === pagination.current_page }">
                  <button class="page-link" @click="goToPage(page)">{{ page }}</button>
                </li>
                <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                  <button class="page-link" @click="goToPage(pagination.current_page + 1)"
                    :disabled="pagination.current_page === pagination.last_page">&raquo;</button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Order Details Modal -->
  <div v-if="showOrderModal" class="modal fade show d-block order-print-modal" tabindex="-1"
    style="background:rgba(0,0,0,0.25);">
      <div class="modal-dialog modal-lg">
      <div class="modal-content print-area">
        <!-- Enhanced Header with date/time and app name -->
        <div class="p-3 d-flex justify-content-between align-items-center border-bottom pb-2 mb-3 print-header">
          <div class="order-date-time">
            <small class="text-muted">{{ $t('tenant_dashboard.orders.orderDate') }}:</small><br>
            <span class="fw-bold">{{ selectedOrder ? new Date(selectedOrder.created_at).toLocaleString() : '' }}</span>
          </div>
          <div class="app-name fw-bold" style="font-size:1.3rem; letter-spacing:0.5px;">{{ appName }}</div>
          <button type="button" class="btn-close d-print-none ms-2" @click="showOrderModal = false"></button>
 
        </div>
        <div class="d-flex justify-content-between align-items-center mb-2 px-3">
          <h4 class="modal-title flex-grow-1 mb-0">{{ $t('tenant_dashboard.orders.orderDetail') }}</h4>

        </div>
        <div class="modal-body pt-0">
          <div v-if="selectedOrder">
            <!-- Order Info Two-Column Grid -->
            <div class="row mb-3 order-info-grid">
              <div class="col-md-8 mb-2">
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.orderNumber') }}:</span>
                  <span class="value p-2">{{ selectedOrder.order_number }}</span>
                  </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.trackingId') }}:</span>
                  <span class="value p-2">
                    <span class="badge bg-info">{{ selectedOrder.tracking_id }}</span>
                    <button class="btn btn-link btn-copy ms-1 p-0" @click="copyToClipboard(selectedOrder.tracking_id, 'Tracking ID')" :title="$t('tenant_dashboard.orders.copyTrackingId')"><i class="fas fa-copy"></i></button>
                  </span>
                </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.customerName') }}:</span>
                  <span class="value p-2">{{ selectedOrder.customer?.name }}</span>
                  </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.customerEmail') }}:</span>
                  <span class="value p-2">{{ selectedOrder.customer?.email }}
                    <button v-if="selectedOrder.customer?.email" class="btn btn-link btn-copy ms-1 p-0" @click="copyToClipboard(selectedOrder.customer.email, 'Email')" :title="$t('tenant_dashboard.orders.copyEmail')"><i class="fas fa-copy"></i></button>
                  </span>
                  </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.customerPhone') }}:</span>
                  <span class="value p-2">{{ selectedOrder.customer?.phone }}
                    <button v-if="selectedOrder.customer?.phone" class="btn btn-link btn-copy ms-1 p-0" @click="copyToClipboard(selectedOrder.customer.phone, 'Phone')" :title="$t('tenant_dashboard.orders.copyPhone')"><i class="fas fa-copy"></i></button>
                  </span>
                </div>

                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.deliveryAddress') }}:</span>
                  <span class="value p-2">{{ selectedOrder.delivery_address }}</span>
                  </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.paymentStatus') }}:</span>
                  <span class="value p-2">{{ selectedOrder.payment_status }}</span>
                </div>
                 <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.orderType') }}:</span>
                  <span class="value p-2">{{ selectedOrder.order_type==='in_house' ? 'In House' : selectedOrder.order_type==='online' ? 'Online' : selectedOrder.order_type==='takeaway' ? 'Takeaway' : 'Default' }}</span>
                </div>
                <div>
                  <span class="label">{{ $t('tenant_dashboard.orders.createdAt') }}:</span>
                  <span class="value p-2">{{ new Date(selectedOrder.created_at).toLocaleString() }}</span>
                  </div>
              </div>

              <div class="col-md-4">

                <div class="mb-2"><strong>{{ $t('tenant_dashboard.orders.changeStatus') }}:</strong></div>
                <div class="status-area ">
                  <span class="fw-bold">{{ $t('tenant_dashboard.orders.status') }}:</span>
                  <span class="position-relative">
                    <select class="form-select form-select-sm w-auto d-inline-block status-select ms-2"
                      v-model="selectedOrder.status" @change="updateModalOrderStatus(selectedOrder.status)"
                      :title="statusTooltip">
                      <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                    <i class="fas fa-info-circle text-muted ms-1" :title="statusTooltip" style="cursor:help;"></i>
                  </span>

                  <!-- Assign Delivery Boy Button in Modal -->
                  <!-- <div v-if="selectedOrder.status === 'completed' && !selectedOrder.delivery_boy_id" class="mt-2">
                    <button 
                      class="btn btn-success btn-sm w-100"
                      @click="openAssignDeliveryModal(selectedOrder)"
                    >
                      <i class="fas fa-motorcycle"></i> Assign Delivery Boy
                    </button>
                  </div>
                  <div v-else-if="selectedOrder.delivery_boy_id" class="mt-2">
                    <span class="badge bg-info">
                      <i class="fas fa-check"></i> Assigned to Delivery
                    </span>
                  </div> -->
                  
                </div>
              </div>
            </div>
          </div>
          <!-- Order Items Table -->
          <div class="mb-2"><strong>{{ $t('tenant_dashboard.orders.orderItems') }}:</strong></div>
          <table class="table table-bordered table-sm mt-2 order-items-table">
            <thead class="table-light">
              <tr>
                <th>{{ $t('tenant_dashboard.orders.product') }}</th>
                <th>{{ $t('tenant_dashboard.orders.quantity') }}</th>
                <th class="text-end">{{ $t('tenant_dashboard.orders.unitPrice') }}</th>
                <th class="text-end">{{ $t('tenant_dashboard.orders.total') }}</th>
                  </tr>
                </thead>
                <tbody>
              <tr v-for="item in selectedOrder.order_details || []" :key="item.id">
                <td>{{ item.product?.name || '-' }}</td>
                <td>{{ item.quantity }}</td>
                <td class="text-end">{{ item.price ? selectedOrder.currency_symbol + Number(item.price).toFixed(2) : '-' }}
                    </td>
                <td class="text-end">{{ item.price && item.quantity ? selectedOrder.currency_symbol + (Number(item.price) *
                  Number(item.quantity)).toFixed(2) : '-' }}</td>
                  </tr>
                </tbody>
            <tfoot>
              <tr>
                <th colspan="3" class="text-end">{{ $t('tenant_dashboard.orders.subtotal') }}:</th>
                <th class="text-end">{{ selectedOrder.currency_symbol }}&nbsp;{{ orderSubtotal(selectedOrder) }}</th>
              </tr>
              <tr v-if="selectedOrder.applied_discount && Number(selectedOrder.applied_discount) > 0">
                <th colspan="3" class="text-end">{{ $t('tenant_dashboard.orders.discount') }}:</th>
                <th class="text-end">-{{ selectedOrder.currency_symbol }}&nbsp;{{ Number(selectedOrder.applied_discount).toFixed(2)
                  }}</th>
              </tr>
              <tr>
                <th colspan="3" class="text-end">{{ $t('tenant_dashboard.orders.finalTotal') }}:</th>
                <th class="text-end final-total">{{ selectedOrder.currency_symbol }}&nbsp;{{ (Number(orderSubtotal(selectedOrder))
                  -
                  Number(selectedOrder.applied_discount || 0)).toFixed(2) }}</th>
              </tr>
            </tfoot>
              </table>

        </div>
      </div>
    </div>
  </div>

  <!-- Assign Delivery Boy Modal -->
  <div v-if="showAssignDeliveryModal" class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.25);">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Assign Delivery Boy</h5>
          <button type="button" class="btn-close" @click="closeAssignDeliveryModal"></button>
        </div>
        <div class="modal-body">
          <div v-if="deliveryBoysLoading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading delivery boys...</span>
            </div>
            <p class="mt-2">Finding available delivery partners...</p>
          </div>
          <div v-else-if="deliveryBoys.length === 0" class="text-center py-4 text-muted">
            <i class="fas fa-motorcycle fa-3x mb-3"></i>
            <p>No delivery boys available at the moment.</p>
          </div>
          <div v-else>
            <div class="alert alert-info mb-3">
              <i class="fas fa-info-circle"></i> 
              Delivery boys within <strong>1KM range</strong> are marked with a green checkmark and recommended for assignment.
            </div>
            
            <div class="row g-3">
              <div 
                v-for="deliveryBoy in deliveryBoys" 
                :key="deliveryBoy.id" 
                class="col-md-6"
              >
                <div 
                  class="card delivery-boy-card h-100"
                  :class="{ 'border-success': deliveryBoy.within_range, 'recommended': deliveryBoy.within_range }"
                >
                  <div class="card-body">
                    <div class="d-flex align-items-start">
                      <div class="flex-grow-1">
                        <h6 class="card-title mb-1">
                          {{ deliveryBoy.name }}
                          <span v-if="deliveryBoy.within_range" class="badge bg-success ms-1">
                            <i class="fas fa-check"></i> Within 1KM
                          </span>
                        </h6>
                        <!-- delivery boy role -->
                        <p class="card-text mb-1">
                          <small class="text-muted">
                            <i class="fas fa-user-tag"></i> {{( deliveryBoy.role || 'Delivery Boy') }}
                          </small>
                        </p>

                          
                        <p class="card-text mb-1">
                          <small class="text-muted">
                            <i class="fas fa-phone"></i> {{ deliveryBoy.phone || 'N/A' }}
                          </small>
                        </p>
                        <p class="card-text mb-1">
                          <small class="text-muted">
                            <i class="fas fa-map-marker-alt"></i> 
                            {{ deliveryBoy.distance ? `${deliveryBoy.distance.toFixed(2)} km away` : 'Distance calculating...' }}
                          </small>
                        </p>
                        <p class="card-text mb-2">
                          <small class="text-muted">
                            <i class="fas fa-bicycle"></i> 
                            {{ deliveryBoy.vehicle_type || 'Not specified' }}
                          </small>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                          <span 
                            class="badge"
                            :class="deliveryBoy.is_available ? 'bg-success' : 'bg-warning'"
                          >
                            {{ deliveryBoy.is_available ? 'Available' : 'Busy' }}
                          </span>
                          <button
                            class="btn btn-sm"
                            :class="deliveryBoy.within_range ? 'btn-success' : 'btn-outline-primary'"
                            @click="assignDeliveryBoy(deliveryBoy)"
                            :disabled="deliveryBoy.is_available || assigningDelivery"
                          >
                            <span v-if="assigningDelivery">
                              <span class="spinner-border spinner-border-sm me-1"></span>
                              Assigning...
                            </span>
                            <span v-else>
                              Assign
                            </span>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="closeAssignDeliveryModal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const COLUMN_CACHE_KEY = 'orders_selected_columns_v1';

const appName = import.meta.env.VITE_APP_NAME || 'Restro-Manage';

export default {
  name: 'Orders',
  setup() {
    const orders = ref([])
    const loading = ref(false)
    const filters = ref({ search: '' })
    const statusOptions = [
      { value: 'preparing', label: 'preparing' },
      { value: 'preparation-done', label: 'preparation-done' },
      { value: 'pending', label: 'Pending' },
      { value: 'processing', label: 'Processing' },
      { value: 'completed', label: 'Completed' },
      { value: 'cancelled', label: 'Cancelled' }
    ]
    // Pagination
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    })
    // Column filter logic
    const allColumns = [
      { key: 'customer_info', label: 'Customer Info' },
      { key: 'tracking_id', label: 'Tracking ID' },
      { key: 'total_price', label: 'Total Price' },
      { key: 'status', label: 'Status' },
       { key: 'order_type', label: 'Order Type' },  // 👈 added this
      { key: 'created_at', label: 'Created At' },
      { key: 'actions', label: 'Actions' },
    ];
    const defaultColumns = ['customer_info', 'tracking_id', 'total_price', 'status', 'actions'];
    const selectedColumns = ref([]);
    const tempSelectedColumns = ref([]);
    const visibleColumns = ref([]);
    // Modal for order details
    const showOrderModal = ref(false)
    const selectedOrder = ref(null)

    // Delivery assignment variables
    const showAssignDeliveryModal = ref(false)
    const deliveryBoys = ref([])
    const deliveryBoysLoading = ref(false)
    const assigningDelivery = ref(false)
    const currentOrderForAssignment = ref(null)

    // Load columns from localStorage or use default
    onMounted(() => {
      const saved = localStorage.getItem(COLUMN_CACHE_KEY);
      if (saved) {
        selectedColumns.value = JSON.parse(saved);
      } else {
        selectedColumns.value = [...defaultColumns];
      }
      tempSelectedColumns.value = [...selectedColumns.value];
      updateVisibleColumns();
      fetchOrders(1);
    });

    const updateVisibleColumns = () => {
      visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key));
    };

    const applyColumnSelection = () => {
      selectedColumns.value = [...tempSelectedColumns.value];
      localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(selectedColumns.value));
      updateVisibleColumns();
    };

    // Fetch orders with pagination
    const fetchOrders = async (page = 1) => {
      loading.value = true
      try {
        const params = {
          page,
          per_page: pagination.value.per_page
        }
        if (filters.value.search) params.search = filters.value.search
        const response = await axios.get('/tenant/orders', { params })
        if (response.data.success) {
          const { data, current_page, last_page, per_page, total, from, to } = response.data.data
          orders.value = data
          pagination.value = { current_page, last_page, per_page, total, from, to }
        } else {
          orders.value = []
          pagination.value = { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
        }
      } catch (error) {
        orders.value = []
        pagination.value = { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
      } finally {
        loading.value = false
      }
    }

    const goToPage = (page) => {
      if (page < 1 || page > pagination.value.last_page) return
      fetchOrders(page)
    }

    // Pagination window logic
    const paginationWindow = computed(() => {
      const total = pagination.value.last_page
      const current = pagination.value.current_page
      const windowSize = 5
      let start = Math.max(1, current - 2)
      let end = Math.min(total, current + 2)
      if (end - start < windowSize - 1) {
        if (start === 1) {
          end = Math.min(total, start + windowSize - 1)
        } else if (end === total) {
          start = Math.max(1, end - windowSize + 1)
        }
      }
      const pages = []
      for (let i = start; i <= end; i++) pages.push(i)
      return pages
    })

    const applyFilters = () => {
      fetchOrders(1)
    }

    // Change status inline
    const updateOrderStatus = async (order, newStatus) => {
      const oldStatus = order.status
      order.status = newStatus
      try {
        await axios.get(`/tenant/orders/${order.id}/status-change`, { params: { status: newStatus } })
        Swal.fire({ icon: 'success', title: 'Status updated', toast: true, position: 'top-end', showConfirmButton: false, timer: 1200 })
      } catch (e) {
        order.status = oldStatus
        Swal.fire({ icon: 'error', title: 'Failed to update status', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
      }
    }

    // View order details
    const viewOrderDetails = (order) => {
      selectedOrder.value = order
      showOrderModal.value = true
    }

    // Change status in modal
    const updateModalOrderStatus = async (newStatus) => {
      if (!selectedOrder.value) return
      const oldStatus = selectedOrder.value.status
      selectedOrder.value.status = newStatus
      try {
        await axios.get(`/tenant/orders/${selectedOrder.value.id}/status-change`, { params: { status: newStatus } })
        Swal.fire({ icon: 'success', title: 'Status updated', toast: true, position: 'top-end', showConfirmButton: false, timer: 1200 })
        fetchOrders(pagination.value.current_page)
      } catch (e) {
        selectedOrder.value.status = oldStatus
        Swal.fire({ icon: 'error', title: 'Failed to update status', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 })
      }
    }

    // Open assign delivery modal
    const openAssignDeliveryModal = async (order) => {
      currentOrderForAssignment.value = order
      showAssignDeliveryModal.value = true
      await fetchDeliveryBoys(order)
    }

    // Close assign delivery modal
    const closeAssignDeliveryModal = () => {
      showAssignDeliveryModal.value = false
      currentOrderForAssignment.value = null
      deliveryBoys.value = []
    }

    // Fetch available delivery boys
    // const fetchDeliveryBoys = async (order) => {
    //   deliveryBoysLoading.value = true
    //   try {
    //     const response = await axios.get('/tenant/delivery-boys/available', {
    //       params: {
    //         order_id: order.id,
    //         latitude: order.delivery_latitude, // Assuming these fields exist
    //         longitude: order.delivery_longitude,
    //         range_km: 1 // 1KM range
    //       }
    //     })
        
    //     if (response.data.success) {
    //       deliveryBoys.value = response.data.data
    //     } else {
    //       deliveryBoys.value = []
    //     }
    //   } catch (error) {
    //     console.error('Error fetching delivery boys:', error)
    //     deliveryBoys.value = []
    //     Swal.fire({
    //       icon: 'error',
    //       title: 'Error',
    //       text: 'Failed to load delivery boys. Please try again.'
    //     })
    //   } finally {
    //     deliveryBoysLoading.value = false
    //   }
    // }
// Fetch available delivery boys
const fetchDeliveryBoys = async (order) => {
  deliveryBoysLoading.value = true;
  try {
    // Get restaurant location from tenant settings or use a default location
    const restaurantLocation = await getRestaurantLocation();
    
    const response = await axios.get('/tenant/delivery-boys/available', {
      params: {
        order_id: order.id,
        latitude: restaurantLocation.latitude,
        longitude: restaurantLocation.longitude,
        range_km: 1 // 1KM range
      }
    });
    
    if (response.data.success) {
      deliveryBoys.value = response.data.data;
    } else {
      deliveryBoys.value = [];
    }
  } catch (error) {
    console.error('Error fetching delivery boys:', error);
    deliveryBoys.value = [];
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Failed to load delivery boys. Please try again.'
    });
  } finally {
    deliveryBoysLoading.value = false;
  }
};

// Helper function to get restaurant location
const getRestaurantLocation = async () => {
  try {
    // Try to get from tenant settings
    const response = await axios.get('/tenant/get-tenant-details');
    if (response.data.success && response.data.data) {
      const tenant = response.data.data;
      console.log(tenant)
      if (tenant.latitude && tenant.longitude) {
        return {
          latitude: tenant.latitude,
          longitude: tenant.longitude
        };
      }
    }
  } catch (error) {
    console.error('Error fetching tenant details:', error);
  }
  
  // Fallback to default location (your restaurant's coordinates)
  return {
    latitude: 28.6129, // Example: Delhi coordinates
    longitude: 77.2295
  };
};
    // Assign delivery boy to order
    const assignDeliveryBoy = async (deliveryBoy) => {
      assigningDelivery.value = true
      try {
        const response = await axios.post('/tenant/orders/assign-delivery', {
          order_id: currentOrderForAssignment.value.id,
          delivery_boy_id: deliveryBoy.id
        })

        if (response.data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: `Order assigned to ${deliveryBoy.name} successfully.`
          })
          
          // Update the order in the list
          const orderIndex = orders.value.findIndex(o => o.id === currentOrderForAssignment.value.id)
          if (orderIndex !== -1) {
            orders.value[orderIndex].delivery_boy_id = deliveryBoy.id
          }
          
          // Update selected order if it's the same
          if (selectedOrder.value && selectedOrder.value.id === currentOrderForAssignment.value.id) {
            selectedOrder.value.delivery_boy_id = deliveryBoy.id
          }
          
          closeAssignDeliveryModal()
        } else {
          throw new Error(response.data.message || 'Failed to assign delivery boy')
        }
      } catch (error) {
        console.error('Error assigning delivery boy:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to assign delivery boy. Please try again.'
        })
      } finally {
        assigningDelivery.value = false
      }
    }

    // Tooltip for status
    const statusTooltip = 'This is your order\'s current status. If you have questions, please contact support.';
    
    // Subtotal calculation
    const orderSubtotal = (order) => {
      if (!order || !order.order_details) return '0.00';
      return order.order_details.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0).toFixed(2);
    };

    // Print handler
    const printOrder = () => {
      window.print();
    };

    // Copy to clipboard handler
    const copyToClipboard = (value, label) => {
      if (!value) return;
      navigator.clipboard.writeText(value).then(() => {
        Swal.fire({
          icon: 'success',
          title: `Successfully copied ${label.toLowerCase()}:`,
          text: value,
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 1800
        });
      });
    };

    const downloadInvoice = async (orderId) => {
      try {
        const response = await axios.get(`/tenant/orders/${orderId}/invoice`, {
          responseType: 'blob' // 👈 Important for binary files
        })

        // Create a link and trigger download
        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url
        link.setAttribute('download', `invoice-${orderId}.pdf`)
        document.body.appendChild(link)
        link.click()
        link.remove()
      } catch (error) {
        console.error('Error downloading invoice:', error)
      }
    }

    return {
      orders,
      loading,
      filters,
      pagination,
      goToPage,
      paginationWindow,
      allColumns,
      selectedColumns,
      tempSelectedColumns,
      visibleColumns,
      applyColumnSelection,
      applyFilters,
      statusOptions,
      updateOrderStatus,
      viewOrderDetails,
      showOrderModal,
      selectedOrder,
      updateModalOrderStatus,
      appName,
      statusTooltip,
      orderSubtotal,
      printOrder,
      copyToClipboard,
      downloadInvoice,
      // Delivery assignment
      showAssignDeliveryModal,
      deliveryBoys,
      deliveryBoysLoading,
      assigningDelivery,
      openAssignDeliveryModal,
      closeAssignDeliveryModal,
      assignDeliveryBoy
    }
  }
}
</script>

<style scoped>
.Orders-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
}

.filter-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(60, 60, 60, 0.08);
  border: 1px solid #e0e0e0;
  padding: 1.5rem 2rem 1rem 2rem;
}

.filter-body {
  padding: 0;
}

.filter-input {
  border-radius: 8px;
  font-size: 15px;
  border: 1px solid #e0e0e0;
  background: #fafbfc;
}

.btn-add-Category {
  background: #1565c0;
  color: #fff;
  font-weight: 500;
  font-size: 15px;
  border-radius: 8px;
  padding: 0.6rem 1.6rem;
  box-shadow: 0 2px 8px rgba(21, 101, 192, 0.08);
  border: none;
  transition: background 0.15s, box-shadow 0.15s;
}

.btn-add-Category:hover,
.btn-add-Category:focus {
  background: #0d47a1;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21, 101, 192, 0.13);
}

.Orders-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}

.Orders-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}

.Orders-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}

.Orders-table tbody tr:hover {
  background: #fff3f3;
}

.btn-group .btn-action {
  padding: 0.25rem 0.5rem;
  font-size: 0.95rem;
  border-radius: 6px;
  border: none;
  transition: background 0.13s;
}

.btn-view {
  background: #e3f2fd;
  color: #1565c0;
}

.btn-view:hover {
  background: #bbdefb;
  color: #0d47a1;
}

.btn-edit {
  background: #e8f5e9;
  color: #388e3c;
}

.btn-edit:hover {
  background: #c8e6c9;
  color: #1b5e20;
}

.btn-delete {
  background: #ffebee;
  color: #c62828;
}

.btn-delete:hover {
  background: #ffcdd2;
  color: #b71c1c;
}

.btn-restore {
  background: #fffde7;
  color: #fbc02d;
}

.btn-restore:hover {
  background: #fff9c4;
  color: #f9a825;
}

.btn-hard-delete {
  background: #fbe9e7;
  color: #d84315;
}

.btn-hard-delete:hover {
  background: #ffccbc;
  color: #bf360c;
}

.Category-details p {
  margin-bottom: 0.5rem;
}

.table td {
  vertical-align: middle;
}

.Category-modal-content {
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
  background: #fff;
  border-radius: 0 0 12px 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 8px 32px rgba(60, 60, 60, 0.13);
  max-width: 650px;
  margin: auto;
  padding: 0;
}

.Category-modal-header {
  background: #c62828;
  color: #fff;
  border-radius: 0;
  border: none;
  padding: 1.2rem 2rem;
  position: relative;
}

.Category-modal-title {
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 0.01em;
}

.Category-modal-header .btn-close {
  filter: invert(1) grayscale(1) brightness(2);
  opacity: 1;
}

.Category-modal-body {
  background: #fafbfc;
  padding: 2rem 2rem 1.5rem 2rem;
}

.Category-modal-footer {
  border-top: 1px solid #ececec;
  padding: 1.2rem 2rem;
  background: #fafbfc;
  border-radius: 0 0 12px 12px;
  display: flex;
  justify-content: center;
}

.input-group-text {
  background: #f5f5f5;
  border: 1px solid #e0e0e0;
  color: #b71c1c;
  font-size: 1.1rem;
}

.btn-submit {
  background: #c62828;
  color: #fff;
  text-transform: uppercase;
  font-weight: 400;
  font-size: 15px;
  border: none;
  border-radius: 6px;
  box-shadow: 0 2px 6px rgba(198, 40, 40, 0.08);
  padding: 0.7rem 2.2rem;
  letter-spacing: 1px;
  transition: background 0.15s, box-shadow 0.15s;
}

.btn-submit:hover,
.btn-submit:focus {
  background: #b71c1c;
  color: #fff;
  box-shadow: 0 4px 12px rgba(198, 40, 40, 0.13);
}

.drag-handle {
  vertical-align: middle;
}

/* Delivery Boy Card Styles */
.delivery-boy-card {
  transition: all 0.3s ease;
  border: 2px solid transparent;
}

.delivery-boy-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.delivery-boy-card.recommended {
  border-color: #28a745;
  background: linear-gradient(135deg, #f8fff9 0%, #e8f5e9 100%);
}

.delivery-boy-card .card-title {
  font-size: 1.1rem;
  font-weight: 600;
}

/* Print-friendly styles for order modal */
.order-print-modal .modal-content.print-area {
  background: #fff;
  color: #222;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
  border-radius: 8px;
  box-shadow: none;
  padding: 0 0 1.5rem 0;
}

.order-print-modal .modal-header,
.order-print-modal .modal-footer {
  background: none;
  border: none;
}

.order-print-modal .modal-title {
  font-size: 1.5rem;
  font-weight: 600;
}

.order-print-modal .order-date-time {
  font-size: 0.95rem;
}

.order-print-modal .app-name {
  color: #b71c1c;
  font-weight: 700;
}

.order-print-modal .status-select {
  min-width: 120px;
}

.order-print-modal .order-items-table th,
.order-print-modal .order-items-table td {
  font-size: 15px;
  padding: 0.5rem 0.75rem;
}

.order-print-modal .order-items-table tfoot th {
  font-size: 1.1rem;
  font-weight: 600;
  background: #fafbfc;
}

.order-print-modal .print-header {
  background: none;
  border-bottom: 1px solid #ececec;
  margin-bottom: 1.2rem;
  padding-bottom: 0.7rem;
}

.order-print-modal .app-name {
  color: #b71c1c;
  font-weight: 700;
  font-size: 1.3rem;
}

.order-print-modal .status-area {
  padding: 0.3rem 0.8rem;
}

.order-print-modal .status-badge {
  font-size: 0.98rem;
  padding: 0.25em 0.8em;
  border-radius: 12px;
  margin-right: 0.2em;
  vertical-align: middle;
  font-weight: 600;
  letter-spacing: 0.02em;
}

.order-print-modal .status-badge.pending {
  background: #fff8e1;
  color: #bfa100;
  border: 1px solid #ffe082;
}

.order-print-modal .status-badge.processing {
  background: #e3f2fd;
  color: #1565c0;
  border: 1px solid #90caf9;
}

.order-print-modal .status-badge.completed {
  background: #e8f5e9;
  color: #388e3c;
  border: 1px solid #a5d6a7;
}

.order-print-modal .status-badge.cancelled {
  background: #ffebee;
  color: #c62828;
  border: 1px solid #ffcdd2;
}

.order-print-modal .order-info-grid .label {
  font-weight: 600;
  color: #b71c1c;
  min-width: 120px;
  display: inline-block;
}

.order-print-modal .order-info-grid .value {
  font-weight: 400;
  color: #222;
}

.order-print-modal .order-items-table th,
.order-print-modal .order-items-table td {
  font-size: 15px;
  padding: 0.5rem 0.75rem;
}

.order-print-modal .order-items-table thead th {
  background: #fafbfc;
  font-weight: 600;
}

.order-print-modal .order-items-table tfoot th {
  font-size: 1.1rem;
  font-weight: 600;
  background: #fafbfc;
  border-top: 2px solid #e0e0e0;
}

.order-print-modal .order-items-table tfoot .final-total {
  font-size: 1.2rem;
  color: #b71c1c;
  font-weight: 700;
  background: #fff3f3;
}

.btn-copy {
  color: #b71c1c;
  font-size: 1.1em;
  vertical-align: middle;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.15s;
}
.btn-copy:hover {
  color: #c62828;
}

@media print {
  body * {
    visibility: hidden !important;
  }

  .order-print-modal,
  .order-print-modal * {
    visibility: visible !important;
  }

  .order-print-modal {
    position: static !important;
    background: none !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  .order-print-modal .modal-content.print-area {
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    width: 100% !important;
    min-width: 0 !important;
    max-width: 100% !important;
  }

  .order-print-modal .modal-footer,
  .order-print-modal .btn-close,
  .order-print-modal .d-print-none {
    display: none !important;
  }

  .order-print-modal .modal-header {
    border: none !important;
    padding-bottom: 0 !important;
  }
}
</style>