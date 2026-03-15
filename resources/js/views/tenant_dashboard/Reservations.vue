<template>
  <div class="Reservations-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.reservations.title') }}</h4>
    </div>
    <!-- Filters Row -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end flex-nowrap">
          <div class="col-md-3">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.reservations.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.reservations.searchPlaceholder')">
          </div>
          <div class="col-md-3">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.reservations.status') }}</label>
            <select v-model="filters.status" @change="applyFilters" class="form-control filter-input">
              <option value="">{{ $t('tenant_dashboard.reservations.allStatus') }}</option>
              <option value="pending">{{ $t('tenant_dashboard.reservations.pending') }}</option>
              <option value="confirmed">{{ $t('tenant_dashboard.reservations.confirmed') }}</option>
              <!-- <option value="assigned">{{ $t('tenant_dashboard.reservations.assigned') }}</option> -->
              <!-- <option value="seated">{{ $t('tenant_dashboard.reservations.seated') }}</option> -->
              <option value="completed">{{ $t('tenant_dashboard.reservations.completed') }}</option>
              <option value="cancelled">{{ $t('tenant_dashboard.reservations.cancelled') }}</option>
              <!-- <option value="no_show">{{ $t('tenant_dashboard.reservations.noShow') }}</option> -->
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.reservations.date') }}</label>
            <input v-model="filters.date" @change="applyFilters" type="date" class="form-control filter-input">
          </div>
          <div class="col-md-3 d-flex align-items-end justify-content-end">
            <!-- Column Filter Dropdown -->
            <div class="dropdown column-filter-dropdown ms-auto">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" @click="toggleColumnDropdown" ref="dropdownBtn">
                <i class="fas fa-filter me-1"></i> {{ $t('tenant_dashboard.reservations.columns') }}
              </button>
              <div class="dropdown-menu p-3" :class="{ show: showColumnDropdown }" style="min-width: 260px; max-height: 320px; overflow-y: auto;">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="selectAllCols" v-model="selectAllChecked" @change="toggleSelectAll">
                  <label class="form-check-label" for="selectAllCols">{{ $t('tenant_dashboard.reservations.selectAll') }}</label>
                </div>
                <div v-for="col in allColumns" :key="col.key" class="form-check mb-1">
                  <input class="form-check-input" type="checkbox" :id="'col-' + col.key" v-model="tempSelectedColumns" :value="col.key">
                  <label class="form-check-label" :for="'col-' + col.key">{{ col.label }}</label>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                  <button class="btn btn-sm btn-primary" @click="applyColumnSelection">{{ $t('tenant_dashboard.reservations.ok') }}</button>
                  <button class="btn btn-sm btn-secondary" @click="cancelColumnSelection">{{ $t('tenant_dashboard.reservations.cancel') }}</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Reservations Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="reservations.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-store fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.reservations.noReservations') }}</div>
          <div>{{ $t('tenant_dashboard.reservations.noReservationsMessage') }}</div>
        </div>
        <div v-else class="table-responsive-custom">
          <table class="table table-hover table-sm Reservations-table">
            <thead>
              <tr>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
                <th>{{ $t('tenant_dashboard.reservations.action') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="reservation in reservations" :key="reservation.id">
                <td v-for="col in visibleColumns" :key="col.key">
                  <div v-if="col.key === 'full_name'">
                    <div class="fw-bold">{{ reservation.full_name }}</div>
                    <div class="small text-muted">{{ reservation.email }}</div>
                  </div>
                  <div v-else-if="col.key === 'status'">
                    <span :class="`badge bg-${getStatusBadge(reservation.status)}`">
                      {{ getStatusText(reservation.status) }}
                    </span>
                  </div>
                  <div v-else-if="col.key === 'table_no'">
                    <span v-if="reservation.table_no" class="badge bg-success">{{ reservation.table_no }}</span>
                    <span v-else class="badge bg-secondary">{{ $t('tenant_dashboard.reservations.notAssigned') }}</span>
                  </div>
                  <span v-else-if="col.key === 'created_at'">
                    {{ reservation.created_at ? new Date(reservation.created_at).toLocaleString() : '' }}
                  </span>
                  <span v-else-if="col.key === 'assigned_at' && reservation.assigned_at">
                    {{ new Date(reservation.assigned_at).toLocaleString() }}
                  </span>
                  <span v-else>
                    {{ reservation[col.key] }}
                  </span>
                </td>
                <td>
  <div class="btn-group btn-group-sm action-dropdown-container">
    <button class="btn btn-outline-primary" @click="showDetails(reservation)">
      <i class="fas fa-eye"></i> 
    </button>
    <button class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" 
            data-bs-toggle="dropdown" aria-expanded="false">
      <span class="visually-hidden">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="#" @click="openEditModal(reservation)"><i class="fas fa-edit me-2"></i>{{ $t('tenant_dashboard.reservations.edit') }}</a></li>
      <li><a class="dropdown-item" href="#" @click="assignTable(reservation)"><i class="fas fa-table me-2"></i>{{ $t('tenant_dashboard.reservations.assignTable') }}</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item text-danger" href="#" @click="deleteReservation(reservation.id)"><i class="fas fa-trash me-2"></i>{{ $t('tenant_dashboard.reservations.delete') }}</a></li>
    </ul>
  </div>
</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            {{ $t('tenant_dashboard.reservations.showing') }} {{ pagination.from }} {{ $t('tenant_dashboard.reservations.to') }} {{ pagination.to }} {{ $t('tenant_dashboard.reservations.of') }} {{ pagination.total }} {{ $t('tenant_dashboard.reservations.entries') }}
          </div>
          <nav v-if="pagination.last_page > 1">
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <button class="page-link" @click="goToPage(pagination.current_page - 1)" :disabled="pagination.current_page === 1">&laquo;</button>
              </li>
              <li v-for="page in paginationWindow" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
                <button class="page-link" @click="goToPage(page)">{{ page }}</button>
              </li>
              <li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
                <button class="page-link" @click="goToPage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page">&raquo;</button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.reservations.detailsTitle') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tbody>
                <tr v-for="col in allColumns" :key="col.key">
                  <th>{{ col.label }}</th>
                  <td>
                    <span v-if="col.key === 'created_at' && selectedReservation.created_at">
                      {{ new Date(selectedReservation.created_at).toLocaleString() }}
                    </span>
                    <span v-else-if="col.key === 'assigned_at' && selectedReservation.assigned_at">
                      {{ new Date(selectedReservation.assigned_at).toLocaleString() }}
                    </span>
                    <span v-else-if="col.key === 'status'">
                      <span :class="`badge bg-${getStatusBadge(selectedReservation.status)}`">
                        {{ getStatusText(selectedReservation.status) }}
                      </span>
                    </span>
                    <span v-else-if="col.key === 'table_no'">
                      <span v-if="selectedReservation.table_no" class="badge bg-success">{{ selectedReservation.table_no }}</span>
                      <span v-else class="badge bg-secondary">{{ $t('tenant_dashboard.reservations.notAssigned') }}</span>
                    </span>
                    <span v-else>
                      {{ selectedReservation[col.key] || '-' }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.reservations.close') }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Reservation Modal -->
    <div class="modal fade" id="editReservationModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.reservations.editReservation') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateReservation">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.reservations.status') }}</label>
                  <select v-model="editingReservation.status" class="form-select" required>
                    <option value="pending">{{ $t('tenant_dashboard.reservations.pending') }}</option>
                    <option value="confirmed">{{ $t('tenant_dashboard.reservations.confirmed') }}</option>
                    <!-- <option value="assigned">{{ $t('tenant_dashboard.reservations.assigned') }}</option> -->
                    <!-- <option value="seated">{{ $t('tenant_dashboard.reservations.seated') }}</option> -->
                    <option value="completed">{{ $t('tenant_dashboard.reservations.completed') }}</option>
                    <option value="cancelled">{{ $t('tenant_dashboard.reservations.cancelled') }}</option>
                    <!-- <option value="no_show">{{ $t('tenant_dashboard.reservations.noShow') }}</option> -->
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">{{ $t('tenant_dashboard.reservations.tableNo') }}</label>
                  <input type="text" v-model="editingReservation.table_no" class="form-control" 
                         :placeholder="$t('tenant_dashboard.reservations.tableNoPlaceholder')">
                </div>
                <div class="col-12" v-if="editingReservation.status === 'assigned' && !editingReservation.table_no">
                  <div class="alert alert-warning py-2">
                    <small><i class="fas fa-exclamation-triangle me-1"></i> 
                    {{ $t('tenant_dashboard.reservations.tableRequiredWarning') }}</small>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.reservations.cancel') }}</button>
            <button type="button" class="btn btn-primary" @click="updateReservation" :disabled="updating">
              <span v-if="updating" class="spinner-border spinner-border-sm me-2"></span>
              {{ $t('tenant_dashboard.reservations.update') }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Assign Table Modal -->
    <div class="modal fade" id="assignTableModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.reservations.assignTable') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">{{ $t('tenant_dashboard.reservations.tableNo') }}</label>
              <input type="text" v-model="tableAssignment.table_no" class="form-control" 
                     :placeholder="$t('tenant_dashboard.reservations.tableNoPlaceholder')" required>
            </div>
            <div class="alert alert-info py-2">
              <small><i class="fas fa-info-circle me-1"></i> 
              {{ $t('tenant_dashboard.reservations.assignTableHelp') }}</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.reservations.cancel') }}</button>
            <button type="button" class="btn btn-primary" @click="confirmAssignTable" :disabled="assigning">
              <span v-if="assigning" class="spinner-border spinner-border-sm me-2"></span>
              {{ $t('tenant_dashboard.reservations.assign') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, computed, nextTick } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal } from 'bootstrap'
import { useI18n } from 'vue-i18n'
const { t: $t } = useI18n()

const reservations = ref([])
const loading = ref(false)
const updating = ref(false)
const assigning = ref(false)
const filters = ref({ 
  search: '',
  status: '',
  date: ''
})
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 8,
  total: 0,
  from: 0,
  to: 0
})

const allColumns = [
  { key: 'full_name', label: $t('tenant_dashboard.reservations.name') },
  { key: 'phone_number', label: $t('tenant_dashboard.reservations.phone') },
  { key: 'guests', label: $t('tenant_dashboard.reservations.guests') },
  { key: 'email', label: $t('tenant_dashboard.reservations.email') },
  { key: 'date', label: $t('tenant_dashboard.reservations.date') },
  { key: 'time', label: $t('tenant_dashboard.reservations.time') },
  { key: 'status', label: $t('tenant_dashboard.reservations.status') },
  { key: 'table_no', label: $t('tenant_dashboard.reservations.tableNo') },
  { key: 'message', label: $t('tenant_dashboard.reservations.message') },
  { key: 'created_at', label: $t('tenant_dashboard.reservations.createdAt') },
  { key: 'assigned_at', label: $t('tenant_dashboard.reservations.assignedAt') },
]
const COLUMN_CACHE_KEY = 'reservations_selected_columns_v1';
const selectedColumns = ref(['full_name', 'phone_number', 'guests', 'status', 'table_no'])
const visibleColumns = ref(allColumns.filter(col => selectedColumns.value.includes(col.key)))

// Column filter dropdown logic
const showColumnDropdown = ref(false)
const tempSelectedColumns = ref([...selectedColumns.value])
const selectAllChecked = ref(false)
const dropdownBtn = ref(null)

const toggleColumnDropdown = () => {
  showColumnDropdown.value = !showColumnDropdown.value
  tempSelectedColumns.value = [...selectedColumns.value]
  updateSelectAllChecked()
}
const applyColumnSelection = () => {
  selectedColumns.value = [...tempSelectedColumns.value]
  visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key))
  showColumnDropdown.value = false
}
const cancelColumnSelection = () => {
  tempSelectedColumns.value = [...selectedColumns.value]
  showColumnDropdown.value = false
}
const toggleSelectAll = () => {
  if (selectAllChecked.value) {
    tempSelectedColumns.value = allColumns.map(col => col.key)
  } else {
    tempSelectedColumns.value = []
  }
}
const updateSelectAllChecked = () => {
  selectAllChecked.value = tempSelectedColumns.value.length === allColumns.length
}
watch(tempSelectedColumns, updateSelectAllChecked)

const selectedReservation = ref({})
const editingReservation = ref({})
const tableAssignment = ref({ table_no: '' })
let detailsModal = null
let editModal = null
let assignTableModal = null

// Handle dropdown toggle for positioning
const handleDropdownToggle = (event) => {
  nextTick(() => {
    const dropdownMenu = event.target.closest('.btn-group').querySelector('.dropdown-menu');
    const tableContainer = event.target.closest('.table-responsive-custom');
    const buttonRect = event.target.getBoundingClientRect();
    const tableRect = tableContainer.getBoundingClientRect();
    const viewportHeight = window.innerHeight;

    // Check if dropdown will be clipped at the bottom
    if (buttonRect.bottom + dropdownMenu.offsetHeight > viewportHeight - 10) {
      dropdownMenu.classList.add('dropdown-menu-top');
    } else {
      dropdownMenu.classList.remove('dropdown-menu-top');
    }
  });
}

const showDetails = (reservation) => {
  selectedReservation.value = reservation
  if (!detailsModal) {
    detailsModal = new Modal(document.getElementById('detailsModal'))
  }
  detailsModal.show()
}

const openEditModal = (reservation) => {
  editingReservation.value = { ...reservation }
  if (!editModal) {
    editModal = new Modal(document.getElementById('editReservationModal'))
  }
  editModal.show()
}

const assignTable = (reservation) => {
  editingReservation.value = { ...reservation }
  tableAssignment.value = { table_no: reservation.table_no || '' }
  if (!assignTableModal) {
    assignTableModal = new Modal(document.getElementById('assignTableModal'))
  }
  assignTableModal.show()
}

const updateReservation = async () => {
  if (editingReservation.value.status === 'assigned' && !editingReservation.value.table_no) {
    Swal.fire({
      icon: 'warning',
      title: $t('tenant_dashboard.reservations.warning'),
      text: $t('tenant_dashboard.reservations.tableRequiredWarning'),
      confirmButtonColor: '#c62828'
    })
    return
  }

  updating.value = true
  try {
    const response = await axios.put(`/tenant/reservations/${editingReservation.value.id}/update-status`, {
      status: editingReservation.value.status,
      table_no: editingReservation.value.table_no
    })
    
    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: $t('tenant_dashboard.reservations.success'),
        text: response.data.message,
        confirmButtonColor: '#c62828'
      })
      editModal.hide()
      await fetchReservations()
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('tenant_dashboard.reservations.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.reservations.updateFailed'),
      confirmButtonColor: '#c62828'
    })
  } finally {
    updating.value = false
  }
}

const confirmAssignTable = async () => {
  if (!tableAssignment.value.table_no.trim()) {
    Swal.fire({
      icon: 'warning',
      title: $t('tenant_dashboard.reservations.warning'),
      text: $t('tenant_dashboard.reservations.tableNoRequired'),
      confirmButtonColor: '#c62828'
    })
    return
  }

  assigning.value = true
  try {
    const response = await axios.put(`/tenant/reservations/${editingReservation.value.id}/assign-table`, {
      table_no: tableAssignment.value.table_no
    })
    
    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: $t('tenant_dashboard.reservations.success'),
        text: response.data.message,
        confirmButtonColor: '#c62828'
      })
      assignTableModal.hide()
      await fetchReservations()
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: $t('tenant_dashboard.reservations.error'),
      text: error.response?.data?.message || $t('tenant_dashboard.reservations.assignFailed'),
      confirmButtonColor: '#c62828'
    })
  } finally {
    assigning.value = false
  }
}

const deleteReservation = async (id) => {
  const result = await Swal.fire({
    title: $t('tenant_dashboard.reservations.confirmDelete'),
    text: $t('tenant_dashboard.reservations.deleteWarning'),
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#c62828',
    cancelButtonColor: '#6c757d',
    confirmButtonText: $t('tenant_dashboard.reservations.delete'),
    cancelButtonText: $t('tenant_dashboard.reservations.cancel')
  })

  if (result.isConfirmed) {
    try {
      const response = await axios.delete(`/tenant/reservations/${id}`)
      if (response.data.success) {
        Swal.fire({
          icon: 'success',
          title: $t('tenant_dashboard.reservations.deleted'),
          text: response.data.message,
          confirmButtonColor: '#c62828'
        })
        await fetchReservations()
      }
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: $t('tenant_dashboard.reservations.error'),
        text: error.response?.data?.message || $t('tenant_dashboard.reservations.deleteFailed'),
        confirmButtonColor: '#c62828'
      })
    }
  }
}

const fetchReservations = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      search: filters.value.search,
      status: filters.value.status,
      date: filters.value.date
    }
    const response = await axios.get('/tenant/reservations', { params })
    if (response.data.success) {
      const { data, current_page, last_page, per_page, total, from, to } = response.data.data
      reservations.value = data
      pagination.value = { current_page, last_page, per_page, total, from, to }
    } else {
      reservations.value = []
      pagination.value = { current_page: 1, last_page: 1, per_page: 8, total: 0, from: 0, to: 0 }
    }
  } catch (error) {
    reservations.value = []
    Swal.fire({ 
      icon: 'error', 
      title: $t('tenant_dashboard.reservations.error'), 
      text: $t('tenant_dashboard.reservations.failedToFetch') 
    })
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchReservations(1)
}

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  fetchReservations(page)
}

const getStatusBadge = (status) => {
  const badges = {
    pending: 'warning',
    confirmed: 'info',
    assigned: 'primary',
    seated: 'success',
    completed: 'dark',
    cancelled: 'danger',
    no_show: 'secondary'
  }
  return badges[status] || 'secondary'
}

const getStatusText = (status) => {
  const statusMap = {
    pending: $t('tenant_dashboard.reservations.pending'),
    confirmed: $t('tenant_dashboard.reservations.confirmed'),
    assigned: $t('tenant_dashboard.reservations.assigned'),
    seated: $t('tenant_dashboard.reservations.seated'),
    completed: $t('tenant_dashboard.reservations.completed'),
    cancelled: $t('tenant_dashboard.reservations.cancelled'),
    no_show: $t('tenant_dashboard.reservations.noShow')
  }
  return statusMap[status] || status
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

// Load from localStorage on mount
onMounted(() => {
  const cached = localStorage.getItem(COLUMN_CACHE_KEY)
  if (cached) {
    try {
      const parsed = JSON.parse(cached)
      if (Array.isArray(parsed) && parsed.every(k => allColumns.some(c => c.key === k))) {
        selectedColumns.value = parsed
        visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key))
      }
    } catch {}
  }
  fetchReservations()
  visibleColumns.value = allColumns.filter(col => selectedColumns.value.includes(col.key))
})

// Save to localStorage on change
watch(selectedColumns, (val) => {
  localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(val))
});
</script>

<style scoped>
.Reservations-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
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
.Reservations-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.Reservations-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.Reservations-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.Reservations-table tbody tr:hover {
  background: #fff3f3;
}
.pagination .page-link {
  color: #6c757d;
  border: none;
  background-color: transparent;
  border-radius: 0.25rem;
  margin: 0 2px;
  padding: 0.5rem 0.75rem;
}
.pagination .page-item.active .page-link {
  background-color: #c62828;
  color: #fff;
  z-index: 3;
}
.pagination .page-item.disabled .page-link {
  color: #ced4da;
}
.pagination .page-link:hover {
  background-color: #f1f1f1;
}
.column-filter-dropdown {
  position: relative;
}
.column-filter-dropdown .dropdown-menu {
  left: auto;
  right: 0;
  min-width: 260px;
  box-shadow: 0 4px 24px rgba(60,60,60,0.13);
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  z-index: 1052;
}
.column-filter-dropdown .form-check {
  user-select: none;
}
.fw-bold {
  font-weight: 600 !important;
}
.table td {
  vertical-align: middle;
}
.badge {
  font-size: 0.75em;
  font-weight: 500;
}
.btn-group .dropdown-toggle-split {
  border-left: 1px solid rgba(255, 255, 255, 0.3);
}

/* ===== DROPDOWN FIX STYLES ===== */
.table-responsive-custom {
  overflow-x: auto;
  position: relative;
}

/* Ensure the table and cells don't clip dropdowns */
.Reservations-table {
  position: relative;
  overflow: visible !important;
}

.Reservations-table tbody tr {
  position: relative;
}

/* Critical: Action dropdown container */
.action-dropdown-container {
  position: static !important;
}

/* Dropdown menu styling */
.dropdown-menu-custom {
  position: absolute !important;
  z-index: 99999 !important;
  left: auto !important;
  right: 0 !important;
  top: 100% !important;
  bottom: auto !important;
  transform: translate3d(0, 2px, 0) !important;
  min-width: 180px;
  background: white;
  border: 1px solid rgba(0,0,0,.15);
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

/* When dropdown needs to show above */
.dropdown-menu-custom.dropdown-menu-top {
  top: auto !important;
  bottom: 100% !important;
  transform: translate3d(0, -2px, 0) !important;
}

/* Ensure dropdown is always visible */
.table-responsive-custom,
.Reservations-table,
.table-responsive-custom .table {
  overflow: visible !important;
}

/* Remove any clipping paths */
.table-responsive-custom * {
  overflow: visible !important;
}

/* Make sure the table cell doesn't restrict dropdown */
.Reservations-table td:last-child {
  position: static;
}

/* Button group adjustments */
.btn-group {
  position: static;
}

/* Ensure dropdown appears above table borders */
.dropdown-menu-custom {
  margin-top: 2px;
  margin-bottom: 2px;
}
</style>