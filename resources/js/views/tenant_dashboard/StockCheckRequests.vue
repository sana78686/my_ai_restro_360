<template>
  <div class="StockCheckRequests-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.stock_check_requests.title') }}</h4>
      <button class="btn btn-primary" @click="openCreateModal">
        <i class="fas fa-plus me-2"></i>
        {{ $t('tenant_dashboard.stock_check_requests.newRequest') }}
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.stock_check_requests.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" 
                   :placeholder="$t('tenant_dashboard.stock_check_requests.searchPlaceholder')">
          </div>
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.stock_check_requests.status') }}</label>
            <select v-model="filters.status" @change="applyFilters" class="form-control filter-input">
              <option value="">{{ $t('tenant_dashboard.stock_check_requests.allStatus') }}</option>
              <option value="pending">{{ $t('tenant_dashboard.stock_check_requests.pending') }}</option>
              <!-- <option value="in_progress">{{ $t('tenant_dashboard.stock_check_requests.in_progress') }}</option> -->
              <option value="available">{{ $t('tenant_dashboard.stock_check_requests.available') }}</option>
              <option value="out_of_stock">{{ $t('tenant_dashboard.stock_check_requests.out_of_stock') }}</option>
              <option value="completed">{{ $t('tenant_dashboard.stock_check_requests.completed') }}</option>
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-outline-secondary w-100" @click="resetFilters">
              <i class="fas fa-refresh me-1"></i>
              {{ $t('tenant_dashboard.stock_check_requests.reset') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Requests Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span>
          </div>
        </div>
        
        <div v-else-if="requests.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-box-open fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.stock_check_requests.noRequests') }}</div>
          <p>{{ $t('tenant_dashboard.stock_check_requests.noRequestsMessage') }}</p>
        </div>
        
        <div v-else class="table-responsive-custom">
          <table class="table table-hover table-sm StockCheckRequests-table">
            <thead>
              <tr>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.requestNo') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.name') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.email') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.phone') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.product') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.quantity') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.status') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.createdAt') }}</th>
                <th>{{ $t('tenant_dashboard.stock_check_requests.tableHeaders.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="req in requests" :key="req.id">
                <td>
                  <span class="text-muted small">#{{ req.id || 'N/A' }}</span>
                </td>
                <td>{{ req.user_name  }}</td>
                <td>{{ req.email  }}</td>
                <td>{{ req.phone  }}</td>
                <td>{{ getProductName(req.product) }}</td>
                <td>{{ req.quantity || 0 }}</td>
                <td>
                  <span :class="`badge bg-${getStatusBadge(req.status)}`">
                    {{ getStatusText(req.status) }}
                  </span>
                </td>
                <td>{{ formatDate(req.created_at) }}</td>
               <td>
  <div class="btn-group">
    <button class="btn btn-outline-primary btn-sm me-1" @click="showDetails(req)" title="View Details">
      <i class="fas fa-eye"></i>
    </button>
    
    <!-- Simplified dropdown without custom positioning -->
    <div class="dropdown">
      <button class="btn btn-outline-secondary btn-sm dropdown-toggle" 
              type="button" 
              data-bs-toggle="dropdown" 
              aria-expanded="false"
              title="More Actions">
        <i class="fas fa-ellipsis-v"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#" @click.prevent="showDetails(req)">
            <i class="fas fa-info-circle me-2"></i>
            {{ $t('tenant_dashboard.stock_check_requests.viewDetails') }}
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#" @click.prevent="openStatusModal(req)">
            <i class="fas fa-edit me-2"></i>
            {{ $t('tenant_dashboard.stock_check_requests.updateStatus') }}
          </a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
          <a class="dropdown-item text-danger" href="#" @click.prevent="deleteRequest(req)">
            <i class="fas fa-trash me-2"></i>
            {{ $t('tenant_dashboard.stock_check_requests.delete') }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create Request Modal -->
    <div class="modal fade" id="createRequestModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.stock_check_requests.createTitle') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="createRequest">
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.stock_check_requests.product') }}</label>
                <select v-model="newRequest.product_id" class="form-select" required>
                  <option value="">{{ $t('tenant_dashboard.stock_check_requests.selectProduct') }}</option>
                  <option v-for="product in products" :key="product.id" :value="product.id">
                    {{ product.name }} (Stock: {{ product.stock_quantity || 0 }})
                  </option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.stock_check_requests.quantity') }}</label>
                <input type="number" v-model="newRequest.quantity" class="form-control" min="1" required>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.stock_check_requests.specialInstructions') }}</label>
                <textarea v-model="newRequest.special_instructions" class="form-control" rows="3"
                          :placeholder="$t('tenant_dashboard.stock_check_requests.instructionsPlaceholder')"></textarea>
              </div>
              <div class="mb-3">
                <label class="form-label">Internal Notes</label>
                <textarea v-model="newRequest.internal_notes" class="form-control" rows="2"
                          placeholder="Internal notes for team..."></textarea>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t('tenant_dashboard.stock_check_requests.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="createRequest" :disabled="creating">
              <span v-if="creating" class="spinner-border spinner-border-sm me-2"></span>
              {{ $t('tenant_dashboard.stock_check_requests.create') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.stock_check_requests.detailsTitle') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="selectedRequest" class="request-details">
              <div class="row">
                <div class="col-md-6">
                  <h6 class="text-muted mb-3">{{ $t('tenant_dashboard.stock_check_requests.productDetails') }}</h6>
                  <p><strong>{{ $t('tenant_dashboard.stock_check_requests.product') }}:</strong> {{ getProductName(selectedRequest.product) }}</p>
                  <p><strong>{{ $t('tenant_dashboard.stock_check_requests.quantity') }}:</strong> {{ selectedRequest.quantity || 0 }}</p>
                  <p><strong>In Stock:</strong> 
                    <span :class="`badge bg-${selectedRequest.in_stock ? 'success' : 'danger'}`">
                      {{ selectedRequest.in_stock ? 'Yes' : 'No' }}
                    </span>
                  </p>
                </div>
                <div class="col-md-6">
                  <h6 class="text-muted mb-3">{{ $t('tenant_dashboard.stock_check_requests.requestInfo') }}</h6>
                  <p><strong>{{ $t('tenant_dashboard.stock_check_requests.status') }}:</strong> 
                    <span :class="`badge bg-${getStatusBadge(selectedRequest.status)}`">
                      {{ getStatusText(selectedRequest.status) }}
                    </span>
                  </p>
                  <p><strong>Created:</strong> {{ formatDate(selectedRequest.created_at) }}</p>
                  <p><strong>Last Updated:</strong> {{ formatDate(selectedRequest.updated_at) }}</p>
                </div>
              </div>
              
              <div class="row mt-4" v-if="selectedRequest.special_instructions">
                <div class="col-12">
                  <h6 class="text-muted mb-3">{{ $t('tenant_dashboard.stock_check_requests.specialInstructions') }}</h6>
                  <div class="alert alert-info">
                    {{ selectedRequest.special_instructions }}
                  </div>
                </div>
              </div>

              <div class="row mt-4" v-if="selectedRequest.admin_notes">
                <div class="col-12">
                  <h6 class="text-muted mb-3">{{ $t('tenant_dashboard.stock_check_requests.adminNotes') }}</h6>
                  <div class="alert alert-warning">
                    {{ selectedRequest.admin_notes }}
                  </div>
                </div>
              </div>

              <div class="row mt-4" v-if="selectedRequest.internal_notes">
                <div class="col-12">
                  <h6 class="text-muted mb-3">Internal Notes</h6>
                  <div class="alert alert-secondary">
                    {{ selectedRequest.internal_notes }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t('tenant_dashboard.stock_check_requests.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Update Status Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.stock_check_requests.updateStatus') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateStatus">
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.stock_check_requests.status') }}</label>
                <select v-model="statusUpdate.status" class="form-select" required>
                  <option value="pending">{{ $t('tenant_dashboard.stock_check_requests.pending') }}</option>
                  <!-- <option value="in_progress">{{ $t('tenant_dashboard.stock_check_requests.in_progress') }}</option> -->
                  <option value="available">{{ $t('tenant_dashboard.stock_check_requests.available') }}</option>
                  <option value="out_of_stock">{{ $t('tenant_dashboard.stock_check_requests.out_of_stock') }}</option>
                  <option value="completed">{{ $t('tenant_dashboard.stock_check_requests.completed') }}</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('tenant_dashboard.stock_check_requests.adminNotes') }}</label>
                <textarea v-model="statusUpdate.admin_notes" class="form-control" rows="3"
                          :placeholder="$t('tenant_dashboard.stock_check_requests.adminNotesPlaceholder')"></textarea>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" v-model="statusUpdate.notify_user" id="notifyUser">
                <label class="form-check-label" for="notifyUser">
                  {{ $t('tenant_dashboard.stock_check_requests.notifyUser') }}
                </label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              {{ $t('tenant_dashboard.stock_check_requests.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="updateStatus" :disabled="updatingStatus">
              <span v-if="updatingStatus" class="spinner-border spinner-border-sm me-2"></span>
              {{ $t('tenant_dashboard.stock_check_requests.update') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal } from 'bootstrap'
import { useI18n } from 'vue-i18n'

const { t: $t } = useI18n()

const requests = ref([])
const products = ref([])
const loading = ref(false)
const creating = ref(false)
const updatingStatus = ref(false)

const filters = ref({
  search: '',
  status: ''
})

const newRequest = ref({
  product_id: '',
  quantity: 1,
  special_instructions: '',
  internal_notes: ''
})

const selectedRequest = ref(null)
const statusUpdate = ref({
  status: '',
  admin_notes: '',
  notify_user: false
})

let createModal = null
let detailsModal = null
let statusModal = null

// Helper Methods
const getProductName = (product) => {
  return product?.name || '-'
}

const formatDate = (dateString) => {
  if (!dateString) return '-'
  try {
    return new Date(dateString).toLocaleDateString()
  } catch (error) {
    return '-'
  }
}

const getStatusBadge = (status) => {
  const badges = {
    pending: 'warning',
    in_progress: 'info',
    available: 'success',
    out_of_stock: 'danger',
    completed: 'dark'
  }
  return badges[status] || 'secondary'
}

const getStatusText = (status) => {
  const statusMap = {
    pending: $t('tenant_dashboard.stock_check_requests.pending'),
    in_progress: $t('tenant_dashboard.stock_check_requests.in_progress'),
    available: $t('tenant_dashboard.stock_check_requests.available'),
    out_of_stock: $t('tenant_dashboard.stock_check_requests.out_of_stock'),
    completed: $t('tenant_dashboard.stock_check_requests.completed')
  }
  return statusMap[status] || status
}

const openCreateModal = () => {
  newRequest.value = {
    product_id: '',
    quantity: 1,
    special_instructions: '',
    internal_notes: ''
  }
  if (!createModal) {
    createModal = new Modal(document.getElementById('createRequestModal'))
  }
  createModal.show()
}

const showDetails = (request) => {
  selectedRequest.value = request
  if (!detailsModal) {
    detailsModal = new Modal(document.getElementById('detailsModal'))
  }
  detailsModal.show()
}

const openStatusModal = (request) => {
  selectedRequest.value = request
  statusUpdate.value = {
    status: request.status,
    admin_notes: request.admin_notes || '',
    notify_user: false
  }
  if (!statusModal) {
    statusModal = new Modal(document.getElementById('statusModal'))
  }
  statusModal.show()
}

const fetchRequests = async () => {
  loading.value = true
  try {
    const params = {
      ...filters.value
    }
    const { data } = await axios.get('/tenant/stock-check-reqs', { params })
    
    if (data.success) {
      const requestsData = data.data?.data || data.data || []
      
      requests.value = requestsData.map(req => ({
        id: req.id || '',
        request_number: req.request_number || 'N/A',
        user_name: req.full_name || '-',
        product: req.product || null,
        quantity: req.qty || 0,
        email: req.email || '-',
        phone: req.phone_number || '-',
        status: req.status || 'pending',
        special_instructions: req.special_instructions || '',
        admin_notes: req.admin_notes || '',
        internal_notes: req.internal_notes || '',
        in_stock: req.in_stock || false,
        created_at: req.created_at || '',
        updated_at: req.updated_at || ''
      }))
    } else {
      requests.value = []
    }
  } catch (error) {
    console.error('Error fetching requests:', error)
    requests.value = []
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: $t('tenant_dashboard.stock_check_requests.fetchFailed')
    })
  } finally {
    loading.value = false
  }
}

const fetchProducts = async () => {
  try {
    const response = await axios.get('/tenant/products')
    if (response.data.success) {
      products.value = response.data.data?.data || response.data.data || []
    } else {
      products.value = []
    }
  } catch (error) {
    console.error('Failed to fetch products:', error)
    products.value = []
  }
}

const createRequest = async () => {
  if (!newRequest.value.product_id) {
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: 'Please select a product',
      confirmButtonColor: '#c62828'
    })
    return
  }

  creating.value = true
  try {
    const response = await axios.post('/tenant/stock-check-reqs', newRequest.value)
    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: $t('dashboard_common.success'),
        text: $t('tenant_dashboard.stock_check_requests.alerts.createSuccess'),
        confirmButtonColor: '#c62828'
      })
      createModal.hide()
      await fetchRequests()
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.stock_check_requests.createFailed'),
      confirmButtonColor: '#c62828'
    })
  } finally {
    creating.value = false
  }
}

const updateStatus = async () => {
  if (!selectedRequest.value?.id) {
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: 'Invalid request selected'
    })
    return
  }

  updatingStatus.value = true
  try {
    const response = await axios.put(`/tenant/stock-check-reqs/${selectedRequest.value.id}/status`, statusUpdate.value)
    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: $t('dashboard_common.success'),
        text: $t('tenant_dashboard.stock_check_requests.alerts.statusUpdateSuccess'),
        confirmButtonColor: '#c62828'
      })
      statusModal.hide()
      await fetchRequests()
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.stock_check_requests.updateFailed'),
      confirmButtonColor: '#c62828'
    })
  } finally {
    updatingStatus.value = false
  }
}

const deleteRequest = async (request) => {
  if (!request?.id) {
    Swal.fire({
      icon: 'error',
      title: $t('dashboard_common.error'),
      text: 'Invalid request selected'
    })
    return
  }

  const confirm = await Swal.fire({
    title: $t('tenant_dashboard.stock_check_requests.confirmDelete'),
    text: $t('tenant_dashboard.stock_check_requests.deleteWarning'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#c62828',
    cancelButtonColor: '#6c757d',
    confirmButtonText: $t('tenant_dashboard.stock_check_requests.delete'),
    cancelButtonText: $t('tenant_dashboard.stock_check_requests.cancel')
  })

  if (!confirm.isConfirmed) return

  try {
    const { data } = await axios.delete(`/tenant/stock-check-reqs/${request.id}`)
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: $t('tenant_dashboard.stock_check_requests.deleted'),
        text: data.message,
        confirmButtonColor: '#c62828'
      })
      await fetchRequests()
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('tenant_dashboard.stock_check_requests.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.stock_check_requests.deleteFailed')
    })
  }
}

const applyFilters = () => {
  fetchRequests()
}

const resetFilters = () => {
  filters.value = { search: '', status: '' }
  fetchRequests()
}

// Lifecycle
onMounted(() => {
  fetchRequests()
  fetchProducts()
})
</script>

<style scoped>
.StockCheckRequests-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
}

.filter-body {
  padding: 0;
}

.filter-input {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  background: #fafbfc;
}

.table-responsive-custom {
  overflow-x: auto;
  position: relative;
}

.StockCheckRequests-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  border-radius: 8px;
  overflow: hidden;
}

.StockCheckRequests-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}

.StockCheckRequests-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}

.StockCheckRequests-table tbody tr:hover {
  background: #fff3f3;
}

.badge {
  font-size: 0.75em;
  font-weight: 500;
}

.request-details p {
  margin-bottom: 0.75rem;
}

.request-details strong {
  color: #495057;
  min-width: 120px;
  display: inline-block;
}

.table td {
  vertical-align: middle;
}

/* FIXED: Dropdown styles to ensure it shows above everything */
.dropdown-custom {

  position: static !important; /* Remove table cell positioning constraints */
}

.dropdown-menu-custom {
  position: fixed !important; /* Use fixed positioning to break out of containers */
  right: auto !important;
  z-index: 9999 !important; /* Highest z-index to ensure it's above everything */
  transform: translate3d(0, 0, 0) !important; /* Force hardware acceleration */
  will-change: transform; /* Optimize for animations */
}

/* Ensure the table responsive container doesn't clip dropdowns */
.table-responsive-custom {
  overflow-x: auto;
  overflow-y: visible !important; /* Allow dropdowns to overflow vertically */
}

/* Additional safety for dropdown positioning */
.dropdown-toggle::after {
  display: none;
}

/* Make sure the dropdown has proper styling */
.dropdown-menu-custom {
  min-width: 200px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border: 1px solid rgba(0,0,0,0.1);
  background: white;
}

/* Ensure table cells don't constrain the dropdown */
.StockCheckRequests-table td:last-child {
  position: relative;
}

/* Fix for Bootstrap dropdown in tables */
.table-responsive-custom .dropdown-menu {
  position: fixed !important;
  z-index: 9999 !important;
}

/* Alternative approach if fixed positioning doesn't work */
@media (min-width: 768px) {
  .dropdown-menu-custom {
    position: absolute !important;
    z-index: 9999 !important;
    inset: 0px auto auto 0px !important;
    transform: translate3d(-120px, 38px, 0px) !important;
  }
}
</style>

<style>

.dropdown-menu {
  z-index: 9999 !important;
}

.table-responsive .dropdown-menu {
  position: fixed !important;
  z-index: 9999 !important;
}


.modal {
  z-index: 1050;
}

.modal-backdrop {
  z-index: 1040;
}


.modal .dropdown-menu {
  z-index: 1060 !important;
}
</style>