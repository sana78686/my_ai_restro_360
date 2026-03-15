<template>
  <div class="mail-logs">
    <div class="card">
      <div class="card-body">
        <!-- Search and Filter -->
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="input-group">
              <input
                type="text"
                class="form-control"
                v-model="search"
                placeholder="Search send_by / sent_to / mail_type / id..."
                @input="onSearchInput"
              />
              <button class="btn btn-outline-secondary" @click="clearSearch">
                <i class="fas fa-times" title="Clear"></i>
              </button>
            </div>
          </div>
          <div class="col-md-3">
            <select class="form-select" v-model.number="perPage" @change="changePerPage">
              <option v-for="n in perPageOptions" :key="n" :value="n">{{ n }} per page</option>
            </select>
          </div>
          <div class="col-md-5 text-end">
            <button class="btn btn-outline-secondary me-2" @click="resetFilters">
              <i class="fas fa-refresh me-1"></i>Reset
            </button>
            <button class="btn btn-primary" @click="exportCsv" :disabled="loading">
              <i class="fas fa-download me-1"></i>Export CSV
            </button>
          </div>
        </div>

        <!-- Mail Logs Table -->
        <div class="table-responsive" v-if="logs.length > 0">
          <table class="table table-hover table-sm align-middle">
            <thead>
              <tr>
                <th style="width: 40px;">
                  <input 
                    type="checkbox" 
                    @change="toggleSelectAll" 
                    :checked="isAllSelected"
                    class="form-check-input"
                  />
                </th>
                <th>ID</th>
                <th>Send By</th>
                <th>Sent To</th>
                <th>Type</th>
                <th>Date</th>
                <th style="width: 120px;">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="log in logs" :key="log.id">
                <td>
                  <input 
                    type="checkbox" 
                    :value="log.id" 
                    v-model="selectedIds"
                    class="form-check-input"
                  />
                </td>
                <td><span class="text-muted">#{{ log.id }}</span></td>
                <td>
                  <a href="#" @click.prevent="viewLog(log)" class="text-decoration-none">
                    {{ log.send_by || '(no sender)' }}
                  </a>
                </td>
                <td>{{ log.sent_to }}</td>
                <td><span class="badge bg-secondary">{{ log.mail_type }}</span></td>
                <td><small class="text-muted">{{ formatDate(log.created_at) }}</small></td>
                <td>
                  <div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary" @click="viewLog(log)" title="View">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-danger" @click="deleteLog(log)" title="Delete">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Loading / Empty -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted">Loading mail logs...</p>
        </div>

        <div v-else-if="!logs.length" class="text-center py-5">
          <i class="fas fa-envelope fa-3x text-muted mb-3"></i>
          <h4>No Mail Logs Found</h4>
          <p class="text-muted">
            {{ search ? 'No results match your search.' : 'No mail logs recorded yet.' }}
          </p>
        </div>

        <!-- Pagination -->
        <div v-if="logs.length > 0" class="d-flex align-items-center justify-content-between mt-4">
          <div class="text-muted">
            Showing {{ firstItem }} to {{ lastItem }} of {{ total }} entries
          </div>
          <nav>
            <ul class="pagination mb-0">
              <li class="page-item" :class="{ disabled: page === 1 }">
                <button class="page-link" @click="changePage(1)">
                  <i class="fas fa-angle-double-left"></i>
                </button>
              </li>
              <li class="page-item" :class="{ disabled: page === 1 }">
                <button class="page-link" @click="changePage(page - 1)">
                  <i class="fas fa-angle-left"></i>
                </button>
              </li>
              <li
                class="page-item"
                v-for="p in visiblePages"
                :key="p"
                :class="{ active: p === page }"
              >
                <button class="page-link" @click="changePage(p)">{{ p }}</button>
              </li>
              <li class="page-item" :class="{ disabled: page === totalPages }">
                <button class="page-link" @click="changePage(page + 1)">
                  <i class="fas fa-angle-right"></i>
                </button>
              </li>
              <li class="page-item" :class="{ disabled: page === totalPages }">
                <button class="page-link" @click="changePage(totalPages)">
                  <i class="fas fa-angle-double-right"></i>
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- View Mail Modal -->
    <div class="modal fade" id="mailModal" tabindex="-1">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="fas fa-envelope me-2"></i>Mail Log #{{ selectedLog.id }}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted small mb-1">SEND BY</label>
                <p class="mb-0">{{ selectedLog.send_by || '(no sender)' }}</p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted small mb-1">SENT TO</label>
                <p class="mb-0">{{ selectedLog.sent_to }}</p>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted small mb-1">TYPE</label>
                <p class="mb-0"><span class="badge bg-primary">{{ selectedLog.mail_type }}</span></p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold text-muted small mb-1">DATE</label>
                <p class="mb-0">{{ formatDate(selectedLog.created_at) }}</p>
              </div>
            </div>
            <hr class="my-4"/>
            <label class="form-label fw-bold text-muted small mb-2">CONTENT</label>
            <div class="email-content border rounded p-3 bg-light">
              <div v-html="selectedLog.content" class="email-html-content"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="fas fa-times me-1"></i>Close
            </button>
            <button class="btn btn-danger" @click="deleteLog(selectedLog)">
              <i class="fas fa-trash me-1"></i>Delete
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { Modal } from 'bootstrap'
import axios from 'axios'

export default {
  name: 'MailLogs',
  setup() {
    const logs = ref([])
    const loading = ref(false)
    const total = ref(0)
    const page = ref(1)
    const perPage = ref(10)
    const perPageOptions = [10, 15, 25, 50, 100]
    const search = ref('')
    const selectedIds = ref([])
    const selectedLog = reactive({
      id: null,
      send_by: '',
      sent_to: '',
      mail_type: '',
      content: '',
      created_at: ''
    })
    const mailModal = ref(null)

    // Pagination
    const totalPages = computed(() => Math.max(1, Math.ceil(total.value / perPage.value)))
    const firstItem = computed(() => ((page.value - 1) * perPage.value) + (logs.value.length ? 1 : 0))
    const lastItem = computed(() => Math.min(total.value, page.value * perPage.value))
    const visiblePages = computed(() => {
      const pages = []
      const totalP = totalPages.value
      const current = page.value
      const delta = 2
      let start = Math.max(1, current - delta)
      let end = Math.min(totalP, current + delta)
      for (let i = start; i <= end; i++) pages.push(i)
      return pages
    })
    const isAllSelected = computed(() => logs.value.length > 0 && selectedIds.value.length === logs.value.length)

    // Fetch logs
    const fetchLogs = async () => {
      loading.value = true
      try {
        const params = { page: page.value, per_page: perPage.value, search: search.value || undefined }
        const resp = await axios.get('/dashboard/mail-logs', { params })
        const payload = resp.data?.data ?? resp.data
        logs.value = payload.data || []
        total.value = payload.total || logs.value.length
      } catch (err) {
        console.error('Failed fetching mail logs', err)
      } finally {
        loading.value = false
      }
    }

    const onSearchInput = () => {
      page.value = 1
      fetchLogs()
    }

    const changePage = (p) => {
      if (p < 1) p = 1
      if (p > totalPages.value) p = totalPages.value
      page.value = p
      fetchLogs()
    }

    const changePerPage = () => {
      page.value = 1
      fetchLogs()
    }

    const resetFilters = () => {
      search.value = ''
      page.value = 1
      fetchLogs()
    }

    const formatDate = (d) => (d ? new Date(d).toLocaleString() : '')

    const viewLog = (log) => {
      Object.assign(selectedLog, log)
      mailModal.value.show()
    }

    const deleteLog = async (log) => {
      if (!confirm(`Delete mail log #${log.id}?`)) return
      try {
        await axios.delete(`/api/mail-logs/${log.id}`)
        fetchLogs()
        mailModal.value.hide()
      } catch (err) {
        console.error('Delete failed', err)
      }
    }

    const toggleSelectAll = (e) => {
      selectedIds.value = e.target.checked ? logs.value.map(l => l.id) : []
    }

    const exportCsv = () => {
      const params = new URLSearchParams({ search: search.value || '', per_page: perPage.value, page: page.value })
      window.open(`/api/superadmin/mail-logs/export?${params.toString()}`, '_blank')
    }

    const clearSearch = () => {
      search.value = ''
      fetchLogs()
    }

    onMounted(() => {
      mailModal.value = new Modal(document.getElementById('mailModal'))
      fetchLogs()
    })

    return {
      logs, total, loading, page, perPage, perPageOptions, search,
      selectedIds, selectedLog, isAllSelected, totalPages, visiblePages,
      firstItem, lastItem,
      fetchLogs, onSearchInput, changePage, changePerPage,
      resetFilters, formatDate, viewLog, deleteLog,
      toggleSelectAll, exportCsv, clearSearch
    }
  }
}
</script>

<style scoped>
.mail-logs {
  padding: 1rem;
}
.table th {
  background: #f8f9fa;
}
.modal-content {
  border: none;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.email-content {
  max-height: 400px;
  overflow-y: auto;
}
.email-html-content :deep(*) { max-width: 100%; }
.email-html-content :deep(img) { max-width: 100%; height: auto; }
</style>
