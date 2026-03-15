// ... existing code ...
<template>
  <div class="Reservations-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.subscribers.title') }}</h4>
    </div>
    <!-- Filters -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.subscribers.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.subscribers.searchPlaceholder')">
          </div>
        </div>
      </div>
    </div>
    <!-- Subscribers Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="subscribers.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-store fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.subscribers.noSubscribers') }}</div>
          <div>{{ $t('tenant_dashboard.subscribers.noSubscribersMessage') }}</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm Reservations-table">
            <thead>
              <tr>
                <th>{{ $t('tenant_dashboard.subscribers.tableHeaders.email') }}</th>
                <th>{{ $t('tenant_dashboard.subscribers.tableHeaders.createdAt') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="subscriber in subscribers" :key="subscriber.id">
                <td>
                  <span>{{ subscriber.email }}</span>
                  <button class="btn btn-link btn-copy p-0 ms-2" @click="copyEmail(subscriber.email)" :title="$t('tenant_dashboard.subscribers.copyEmail')">
                    <i class="fas fa-copy"></i>
                  </button>
                </td>
                <td>{{ subscriber.created_at ? new Date(subscriber.created_at).toLocaleString() : '' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            {{ $t('tenant_dashboard.subscribers.showing') }} {{ pagination.from }} {{ $t('tenant_dashboard.subscribers.to') }} {{ pagination.to }} {{ $t('tenant_dashboard.subscribers.of') }} {{ pagination.total }} {{ $t('tenant_dashboard.subscribers.entries') }}
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
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

const subscribers = ref([])
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

const fetchSubscribers = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      per_page: pagination.value.per_page,
      search: filters.value.search
    }
    const response = await axios.get('/tenant/subscribers-list', { params })
    if (response.data.success) {
      const { data, current_page, last_page, per_page, total, from, to } = response.data.data
      subscribers.value = data
      pagination.value = { current_page, last_page, per_page, total, from, to }
    } else {
      subscribers.value = []
      pagination.value = { current_page: 1, last_page: 1, per_page: 8, total: 0, from: 0, to: 0 }
    }
  } catch (error) {
    subscribers.value = []
    Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch subscribers.' })
  } finally {
    loading.value = false
  }
}

const applyFilters = () => {
  fetchSubscribers(1)
}

const goToPage = (page) => {
  if (page < 1 || page > pagination.value.last_page) return
  fetchSubscribers(page)
}

const copyEmail = (email) => {
  navigator.clipboard.writeText(email)
    .then(() => {
      Swal.fire({
        icon: 'success',
        title: 'Copied!',
        text: 'Email copied to clipboard.',
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
        text: 'Failed to copy email.'
      })
    })
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

onMounted(() => {
  fetchSubscribers()
})
</script>

<style scoped>
/* ... reuse styles from Reservations.vue ... */
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
</style>
