<template>
  <div class="Reservations-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.contact_requests.title') }}</h4>
    </div>
    <!-- Filters and Column Filter -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end flex-nowrap">
          <div class="col-md-6">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.contact_requests.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.contact_requests.searchPlaceholder')">
          </div>
          <div class="col-md-6 d-flex align-items-end justify-content-end">
            <!-- Column Filter Dropdown -->
            <div class="dropdown column-filter-dropdown ms-auto">
              <button class="btn btn-outline-secondary dropdown-toggle" type="button" @click="toggleColumnDropdown" ref="dropdownBtn">
                <i class="fas fa-filter me-1"></i> {{ $t('tenant_dashboard.contact_requests.columns') }}
              </button>
              <div class="dropdown-menu p-3" :class="{ show: showColumnDropdown }" style="min-width: 260px; max-height: 320px; overflow-y: auto;">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="selectAllCols" v-model="selectAllChecked" @change="toggleSelectAll">
                  <label class="form-check-label" for="selectAllCols">{{ $t('tenant_dashboard.contact_requests.selectAll') }}</label>
                </div>
                <div v-for="col in allColumns" :key="col.key" class="form-check mb-1">
                  <input class="form-check-input" type="checkbox" :id="'col-' + col.key" v-model="tempSelectedColumns" :value="col.key">
                  <label class="form-check-label" :for="'col-' + col.key">{{ col.label }}</label>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                  <button class="btn btn-sm btn-primary" @click="applyColumnSelection">{{ $t('tenant_dashboard.contact_requests.ok') }}</button>
                  <button class="btn btn-sm btn-secondary" @click="cancelColumnSelection">{{ $t('tenant_dashboard.contact_requests.cancel') }}</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Contact Requests Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="contacts.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-store fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.contact_requests.noRequests') }}</div>
          <div>{{ $t('tenant_dashboard.contact_requests.noRequestsMessage') }}</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm Reservations-table">
            <thead>
              <tr>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
                <th>{{ $t('tenant_dashboard.contact_requests.action') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="contact in contacts" :key="contact.id">
                <td v-for="col in visibleColumns" :key="col.key">
                  <template v-if="col.key === 'name'">
                    <div class="fw-bold">{{ contact.name }}</div>
                    <div class="small text-muted d-flex align-items-center">
                      <span>{{ contact.email }}</span>
                      <button class="btn btn-link btn-copy p-0 ms-1" @click="copyToClipboard(contact.email, 'Email')" data-bs-toggle="tooltip" data-bs-placement="top" :title="$t('tenant_dashboard.contact_requests.copyEmail')">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                  </template>
                  <template v-else-if="col.key === 'phone'">
                    <span>{{ contact.phone }}</span>
                    <button class="btn btn-link btn-copy p-0 ms-2" @click="copyToClipboard(contact.phone, 'Phone')" data-bs-toggle="tooltip" data-bs-placement="top" :title="$t('tenant_dashboard.contact_requests.copyPhone')">
                      <i class="fas fa-copy"></i>
                    </button>
                  </template>
                  <template v-else-if="col.key === 'message'">
                    <span :title="contact.message" data-bs-toggle="tooltip" data-bs-placement="top">
                      {{ contact.message.length > 18 ? contact.message.slice(0, 18) + '...' : contact.message }}
                    </span>
                  </template>
                  <template v-else-if="col.key === 'created_at'">
                    {{ contact.created_at ? new Date(contact.created_at).toLocaleString() : '' }}
                  </template>
                  <template v-else>
                    {{ contact[col.key] }}
                  </template>
                </td>
                <td>
                  <button class="btn btn-sm btn-primary" @click="showDetails(contact)" data-bs-toggle="tooltip" data-bs-placement="top" :title="$t('tenant_dashboard.contact_requests.viewDetails')"><i class="fas fa-eye"></i> {{ $t('tenant_dashboard.contact_requests.view') }}</button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            {{ $t('tenant_dashboard.contact_requests.showing') }} {{ pagination.from }} {{ $t('tenant_dashboard.contact_requests.to') }} {{ pagination.to }} {{ $t('tenant_dashboard.contact_requests.of') }} {{ pagination.total }} {{ $t('tenant_dashboard.contact_requests.entries') }}
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
            <h5 class="modal-title">{{ $t('tenant_dashboard.contact_requests.detailsModalTitle') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <table class="table table-bordered">
              <tbody>
                <tr v-for="col in allColumns" :key="col.key">
                  <th>{{ col.label }}</th>
                  <td>
                    <span v-if="col.key === 'created_at' && selectedContact.created_at">
                      {{ new Date(selectedContact.created_at).toLocaleString() }}
                    </span>
                    <span v-else>
                      {{ selectedContact[col.key] }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.contact_requests.close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal, Tooltip } from 'bootstrap'

const contacts = ref([])
const loading = ref(false)
const filters = ref({ search: '' })
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 8,
  total: 0,
  from: 0,
  to: 0
})

const allColumns = [
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'phone', label: 'Phone' },
  { key: 'subject', label: 'Subject' },
  { key: 'message', label: 'Message' },
  { key: 'created_at', label: 'Created At' },
]
const COLUMN_CACHE_KEY = 'contactreqs_selected_columns_v1';
const selectedColumns = ref(['name', 'subject', 'message'])
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

const selectedContact = ref({})
let detailsModal = null
const showDetails = (contact) => {
  selectedContact.value = contact
  if (!detailsModal) {
    detailsModal = new Modal(document.getElementById('detailsModal'))
  }
  detailsModal.show()
}

const copyToClipboard = (value, label) => {
  navigator.clipboard.writeText(value)
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: `${label} copied to clipboard.`,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1200
      })
    })
    .catch(() => {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: `Failed to copy ${label.toLowerCase()}.`
      })
    })
}

const fetchContacts = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      search: filters.value.search
    }
    const response = await axios.get('/tenant/contacts-list', { params })
    if (response.data.success) {
      const { data, current_page, last_page, per_page, total, from, to } = response.data.data
      contacts.value = data
      pagination.value = { current_page, last_page, per_page, total, from, to }
    } else {
      contacts.value = []
      pagination.value = { current_page: 1, last_page: 1, per_page: 8, total: 0, from: 0, to: 0 }
    }
  } catch (error) {
    contacts.value = []
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch contact requests.' })
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchContacts(1)
}

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  fetchContacts(page)
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
  fetchContacts()
  setTimeout(() => {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
      new Tooltip(el)
    })
  }, 500)
})

// Save to localStorage on change
watch(selectedColumns, (val) => {
  localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(val))
})
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
.btn-copy {
  color: #b71c1c;
  font-size: 1rem;
  vertical-align: middle;
  background: none;
  border: none;
  cursor: pointer;
  transition: color 0.2s;
}
.btn-copy:hover {
  color: #c62828;
}
.table td {
  vertical-align: middle;
}
</style>
