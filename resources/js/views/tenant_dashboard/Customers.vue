<template>
  <div class="Customers-page">
    <div class="d-flex justify-content-between align-items-center mb-4">

      <h4 class="mb-0">{{ $t('tenant_dashboard.customers.title') }}</h4>

    </div>

    <!-- Filters + Column Filter Row -->
    <div class="mb-4">
      <div class="filter-body row g-3 align-items-end flex-wrap">
        <div class="col-12 col-md-4 mb-2 mb-md-0">
          <label class="form-label mb-1">{{ $t('tenant_dashboard.customers.search') }}</label>
          <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.customers.searchPlaceholder')">
        </div>
        <div class="col-12 col-md-3 mb-2 mb-md-0">
          <label class="form-label mb-1">{{ $t('tenant_dashboard.customers.status') }}</label>
          <select v-model="filters.status" @change="applyFilters" class="form-select filter-input">
            <option value="">{{ $t('tenant_dashboard.customers.all') }}</option>
            <option value="active">{{ $t('tenant_dashboard.customers.active') }}</option>
            <option value="inactive">{{ $t('tenant_dashboard.customers.inactive') }}</option>
          </select>
        </div>
        <div class="col-12 col-md-3 ms-auto d-flex justify-content-md-end justify-content-start align-items-end">
          <!-- Column Filter Dropdown -->
          <div class="dropdown column-filter-dropdown w-100 w-md-auto">
            <button class="btn btn-theme btn-sm dropdown-toggle w-100" type="button" id="columnDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-columns"></i> {{ $t('tenant_dashboard.customers.columns') }}
            </button>
            <ul class="dropdown-menu theme-dropdown shadow-lg p-2" aria-labelledby="columnDropdown" style="min-width:220px;">
              <li v-for="col in allColumns" :key="col.key">
                <label class="dropdown-item mb-0 d-flex align-items-center gap-2">
                  <input type="checkbox" v-model="tempSelectedColumns" :value="col.key" class="form-check-input" />
                  <span>{{ col.label }}</span>
                </label>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <button class="btn btn-theme w-100" @click="applyColumnSelection">{{ $t('tenant_dashboard.customers.apply') }}</button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Customers Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="customers.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-users fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.customers.noCustomers') }}</div>
          <div>{{ $t('tenant_dashboard.customers.noCustomersMessage') }}</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm customers-table">
            <thead>
              <tr>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="customer in customers" :key="customer.id">
                <td v-for="col in visibleColumns" :key="col.key">
                  <template v-if="col.key === 'email'">
                    {{ customer.email }}
                    <button class="btn btn-link btn-sm p-0 ms-1 copy-btn" @click="copyToClipboard(customer.email, 'Email')" :title="$t('tenant_dashboard.customers.copyEmail')">
                      <i class="fas fa-copy"></i>
                    </button>
                  </template>
                  <template v-else-if="col.key === 'created_at'">
                    {{ new Date(customer.created_at).toLocaleDateString() }}
                  </template>
                  <template v-else-if="col.key === 'status'">
                    <span class="badge" :class="customer.status === 'active' ? 'bg-success' : 'bg-danger'">
                      {{ customer.status === 'active' ? $t('tenant_dashboard.customers.active') : $t('tenant_dashboard.customers.inactive') }}
                    </span>
                  </template>
                  <template v-else>
                    {{ customer[col.key] }}
                  </template>
                </td>
              </tr>
            </tbody>
          </table>
          <!-- Pagination Controls -->
          <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
              {{ $t('tenant_dashboard.customers.showing') }} {{ pagination.from }} {{ $t('tenant_dashboard.customers.to') }} {{ pagination.to }} {{ $t('tenant_dashboard.customers.of') }} {{ pagination.total }} {{ $t('tenant_dashboard.customers.entries') }}
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
    </div>


    <!-- View Customer Modal -->
    <div class="modal fade" id="viewCustomerModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.customers.customerDetails') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="customerLoading" class="text-center py-4">
              <div class="spinner-border text-danger" role="status"></div>
            </div>
            <div v-else-if="selectedCustomer">
              <p><strong>{{ $t('tenant_dashboard.customers.id') }}:</strong> {{ selectedCustomer.id }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.uniqueId') }}:</strong> {{ selectedCustomer.unique_id }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.name') }}:</strong> {{ selectedCustomer.name }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.email') }}:</strong> {{ selectedCustomer.email }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.phone') }}:</strong> {{ selectedCustomer.phone }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.deviceInfo') }}:</strong> {{ selectedCustomer.device_info }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.address') }}:</strong> {{ selectedCustomer.address }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.status') }}:</strong> {{ selectedCustomer.status }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.createdBy') }}:</strong> {{ selectedCustomer.creator ? selectedCustomer.creator.name : '-' }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.createdAt') }}:</strong> {{ new Date(selectedCustomer.created_at).toLocaleString() }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.updatedBy') }}:</strong> {{ selectedCustomer.updater ? selectedCustomer.updater.name : '-' }}</p>
              <p><strong>{{ $t('tenant_dashboard.customers.updatedAt') }}:</strong> {{ new Date(selectedCustomer.updated_at).toLocaleString() }}</p>
            </div>
            <div v-else class="text-muted">{{ $t('tenant_dashboard.customers.noCustomerDetails') }}</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.customers.close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'

const COLUMN_CACHE_KEY = 'customers_selected_columns_v1';

export default {
  name: 'Customers',
  data() {
    return {
      customers: [],
      loading: false,
      filters: { search: '', status: '' },
      selectedCustomer: null,
      customerLoading: false,
      viewCustomerModal: null,
      // Pagination
      pagination: {
        current_page: 1,
        last_page: 1,
        per_page: 10,
        total: 0,
        from: 0,
        to: 0
      },
      // Column filter logic
      allColumns: [
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'phone', label: 'Phone' },
        { key: 'device_info', label: 'Device Info' },
        { key: 'status', label: 'Status' },
        { key: 'created_at', label: 'Created At' },
        { key: 'creator', label: 'Created By' },
      ],
      defaultColumns: ['name', 'email', 'phone'],
      selectedColumns: [],
      tempSelectedColumns: [],
      visibleColumns: []
    }
  },
  computed: {
    // Pagination window logic
    paginationWindow() {
      const total = this.pagination.last_page
      const current = this.pagination.current_page
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
    }
  },
  mounted() {
    // Update column labels with localization
    this.allColumns = [
      { key: 'name', label: this.$t('tenant_dashboard.customers.name') },
      { key: 'email', label: this.$t('tenant_dashboard.customers.email') },
      { key: 'phone', label: this.$t('tenant_dashboard.customers.phone') },
      { key: 'device_info', label: this.$t('tenant_dashboard.customers.deviceInfo') },
      { key: 'status', label: this.$t('tenant_dashboard.customers.status') },
      { key: 'created_at', label: this.$t('tenant_dashboard.customers.createdAt') },
      { key: 'creator', label: this.$t('tenant_dashboard.customers.createdBy') },
    ];

    // Load columns from localStorage or use default
    const saved = localStorage.getItem(COLUMN_CACHE_KEY);
    if (saved) {
      this.selectedColumns = JSON.parse(saved);
    } else {
      this.selectedColumns = [...this.defaultColumns];
    }
    this.tempSelectedColumns = [...this.selectedColumns];
    this.updateVisibleColumns();
    this.fetchCustomers(1);
  },
  methods: {
    updateVisibleColumns() {
      this.visibleColumns = this.allColumns.filter(col => this.selectedColumns.includes(col.key));
    },

    applyColumnSelection() {
      this.selectedColumns = [...this.tempSelectedColumns];
      localStorage.setItem(COLUMN_CACHE_KEY, JSON.stringify(this.selectedColumns));
      this.updateVisibleColumns();
    },

    // Fetch customers with pagination
    async fetchCustomers(page = 1) {
      this.loading = true
      try {
        const params = {
          page,
          per_page: this.pagination.per_page
        }
        if (this.filters.search) params.search = this.filters.search
        if (this.filters.status) params.status = this.filters.status
        const response = await axios.get('/tenant/customers', { params })
        if (response.data.success) {
          const { data, current_page, last_page, per_page, total, from, to } = response.data.data
          this.customers = data
          this.pagination = { current_page, last_page, per_page, total, from, to }
        } else {
          this.customers = []
          this.pagination = { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
        }
      } catch (error) {
        this.customers = []
        this.pagination = { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
      } finally {
        this.loading = false
      }
    },

    goToPage(page) {
      if (page < 1 || page > this.pagination.last_page) return
      this.fetchCustomers(page)
    },

    applyFilters() {
      this.fetchCustomers(1)
    },

    async viewCustomer(id) {
      this.customerLoading = true
      this.selectedCustomer = null
      if (!this.viewCustomerModal) {
        this.viewCustomerModal = new Modal(document.getElementById('viewCustomerModal'))
      }
      this.viewCustomerModal.show()
      try {
        const response = await axios.get(`/tenant/customers/${id}`)
        this.selectedCustomer = response.data.data
      } catch (error) {
        this.selectedCustomer = null
      } finally {
        this.customerLoading = false
      }
    },

    // Copy to clipboard
    copyToClipboard(value, label) {
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
  }
}
</script>

<style scoped>
.Customers-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
}

.filter-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(60,60,60,0.08);
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
  box-shadow: 0 2px 8px rgba(21,101,192,0.08);
  border: none;
  transition: background 0.15s, box-shadow 0.15s;
}
.btn-add-Category:hover, .btn-add-Category:focus {
  background: #0d47a1;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21,101,192,0.13);
}

.customers-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.customers-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.customers-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.customers-table tbody tr:hover {
  background: #fff3f3;
}

.btn-group .btn-action {
  padding: 0.25rem 0.5rem;
  font-size: 0.95rem;
  border-radius: 6px;
  border: none;
  transition: background 0.13s;
}
.btn-view { background: #e3f2fd; color: #1565c0; }
.btn-view:hover { background: #bbdefb; color: #0d47a1; }
.btn-edit { background: #e8f5e9; color: #388e3c; }
.btn-edit:hover { background: #c8e6c9; color: #1b5e20; }
.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #ffcdd2; color: #b71c1c; }
.btn-restore { background: #fffde7; color: #fbc02d; }
.btn-restore:hover { background: #fff9c4; color: #f9a825; }
.btn-hard-delete { background: #fbe9e7; color: #d84315; }
.btn-hard-delete:hover { background: #ffccbc; color: #bf360c; }

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
  box-shadow: 0 8px 32px rgba(60,60,60,0.13);
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
  box-shadow: 0 2px 6px rgba(198,40,40,0.08);
  padding: 0.7rem 2.2rem;
  letter-spacing: 1px;
  transition: background 0.15s, box-shadow 0.15s;
}
.btn-submit:hover, .btn-submit:focus {
  background: #b71c1c;
  color: #fff;
  box-shadow: 0 4px 12px rgba(198,40,40,0.13);
}

.drag-handle {
  vertical-align: middle;
}
.column-filter-dropdown {
  min-width: 140px;
}
.btn-theme {
  background: #b71c1cd0;
  color: #fff;
  font-weight: 500;
  border-radius: 8px;
  border: none;
  transition: background 0.15s, box-shadow 0.15s;
}
.btn-theme:hover, .btn-theme:focus {
  background: #b71c1c;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21,101,192,0.13);
}
.theme-dropdown {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 8px 32px rgba(60,60,60,0.13);
  background: #fff;
  font-size: 15px;
  padding: 0.5rem 0.5rem 0.5rem 0.5rem;
}
.theme-dropdown .dropdown-item {
  border-radius: 6px;
  padding: 0.45rem 0.7rem;
  font-size: 15px;
  color: #222;
  transition: background 0.13s, color 0.13s;
}
.theme-dropdown .dropdown-item:hover, .theme-dropdown .dropdown-item:focus {
  background: #f5f5f5;
  color: #1565c0;
}
.theme-dropdown .form-check-input {
  margin-right: 0.5rem;
  accent-color: #1565c0;
}
.theme-dropdown .btn-theme {
  margin-top: 0.5rem;
  font-size: 15px;
  padding: 0.45rem 0.7rem;
}
@media (max-width: 767.98px) {
  .filter-body {
    flex-direction: column !important;
    gap: 0.5rem;
  }
  .column-filter-dropdown {
    width: 100%;
    margin-top: 0.5rem;
  }
  .btn-theme {
    width: 100%;
  }
}
@media (max-width: 575.98px) {
  .customers-table {
    font-size: 14px;
  }
  .theme-dropdown {
    min-width: 180px;
    font-size: 14px;
  }
}
</style>
