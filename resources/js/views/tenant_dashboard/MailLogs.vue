<template>
  <div class="mail-logs-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.mail_logs.title') }}</h4>
    </div>

    <!-- 🔹 Filters -->
    <div class="mb-4">
      <div class="filter-body row g-3 align-items-end flex-wrap">
        <!-- Search -->
        <div class="col-12 col-md-4">
          <label class="form-label mb-1">{{ $t('tenant_dashboard.mail_logs.search') }}</label>
          <input
            v-model="filters.search"
            @input="applyFilters"
            type="text"
            class="form-control filter-input"
            :placeholder="$t('tenant_dashboard.mail_logs.searchPlaceholder')"
          />
        </div>

        <!-- Column Selector -->
        <div class="col-12 col-md-3 ms-auto d-flex justify-content-md-end">
          <div class="dropdown column-filter-dropdown w-100 w-md-auto">
            <button
              class="btn-add-Category dropdown-toggle w-100"
              type="button"
              id="mailLogColumnDropdown"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="fas fa-columns"></i>
              {{ $t('tenant_dashboard.mail_logs.columns') }}
            </button>

            <ul
              class="dropdown-menu theme-dropdown shadow-lg p-2"
              aria-labelledby="mailLogColumnDropdown"
            >
              <li v-for="col in allColumns" :key="col.key">
                <label class="dropdown-item d-flex align-items-center gap-2">
                  <input
                    type="checkbox"
                    v-model="tempSelectedColumns"
                    :value="col.key"
                    class="form-check-input"
                  />
                  <span>{{ col.label }}</span>
                </label>
              </li>

              <li>
                <hr class="dropdown-divider" />
              </li>

              <li>
                <button class="btn-add-Category w-100" @click="applyColumnSelection">
                  {{ $t('tenant_dashboard.mail_logs.apply') }}
                </button>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- 🔹 Mail Logs Table -->
    <div class="card">
      <div class="card-body">
        <!-- Loader -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"></div>
        </div>

        <!-- Table -->
        <div v-else>
          <table v-if="logs.length > 0" class="table Orders-table">
            <thead>
              <tr>
                <th v-for="col in visibleColumns" :key="col.key">{{ col.label }}</th>
                <th>{{ $t('tenant_dashboard.mail_logs.actions') }}</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="log in logs" :key="log.id">
                <td v-for="col in visibleColumns" :key="col.key">
                  <span v-if="col.key === 'created_at'">
                    {{ formatDate(log.created_at) }}
                  </span>
                  <span v-else>
                    {{ log[col.key] }}
                  </span>
                </td>
                <td>
                  <button class="btn-add-Category btn-sm" @click="viewLog(log)">
                    <i class="fas fa-eye"></i> {{ $t('tenant_dashboard.mail_logs.view') }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Empty state -->
          <div v-else class="text-center py-4 text-muted">
            {{ $t('tenant_dashboard.mail_logs.noData') }}
          </div>

          <!-- 🔹 Pagination -->
          <nav v-if="pagination.last_page > 1" class="mt-3">
            <ul class="pagination justify-content-center">
              <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
                <button class="page-link" @click="changePage(pagination.current_page - 1)">
                  ‹
                </button>
              </li>

              <li
                class="page-item"
                v-for="page in pagination.last_page"
                :key="page"
                :class="{ active: pagination.current_page === page }"
              >
                <button class="page-link" @click="changePage(page)">
                  {{ page }}
                </button>
              </li>

              <li
                class="page-item"
                :class="{ disabled: pagination.current_page === pagination.last_page }"
              >
                <button class="page-link" @click="changePage(pagination.current_page + 1)">
                  ›
                </button>
              </li>
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <!-- 🔹 Log Details Modal -->
    <div
      class="modal fade"
      id="mailLogModal"
      tabindex="-1"
      aria-hidden="true"
      ref="mailLogModal"
    >
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content filter-card">
          <div class="modal-header border-0 border-bottom">
            <h5 class="modal-title">{{ $t('tenant_dashboard.mail_logs.details') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div v-if="selectedLog">
              <p><strong>ID:</strong> {{ selectedLog.id }}</p>
              <p><strong>{{ $t('tenant_dashboard.mail_logs.send_by') }}:</strong> {{ selectedLog.send_by }}</p>
              <p><strong>{{ $t('tenant_dashboard.mail_logs.sent_to') }}:</strong> {{ selectedLog.sent_to }}</p>
              <p><strong>{{ $t('tenant_dashboard.mail_logs.type') }}:</strong> {{ selectedLog.mail_type }}</p>
              <p><strong>{{ $t('tenant_dashboard.mail_logs.content') }}:</strong></p>

              <div class="p-2 border rounded bg-light" v-html="selectedLog.content"></div>
            </div>
          </div>

          <div class="modal-footer border-0 border-top">
            <button type="button" class="btn-add-Category" data-bs-dismiss="modal">
              {{ $t('tenant_dashboard.mail_logs.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from "vue";
import axios from "axios";
import * as bootstrap from "bootstrap";

export default {
  name: "MailLogs",
  setup() {
    const loading = ref(true);
    const filters = ref({ search: "" });
    const logs = ref([]);
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      total: 0,
      per_page: 10,
    });
    const selectedLog = ref(null);

    // Columns
    const allColumns = [
      { key: "id", label: "ID" },
      { key: "send_by", label: "Send By" },
      { key: "sent_to", label: "Sent To" },
      { key: "mail_type", label: "Type" },
      { key: "created_at", label: "Date" },
    ];

    const formatDate = (date) => {
      if (!date) return "";
      return new Intl.DateTimeFormat("en-GB", {
        year: "numeric",
        month: "short",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
      }).format(new Date(date));
    };

    const tempSelectedColumns = ref(allColumns.map((c) => c.key));
    const selectedColumns = ref(allColumns.map((c) => c.key));

    const visibleColumns = computed(() =>
      allColumns.filter((c) => selectedColumns.value.includes(c.key))
    );

    const fetchLogs = async (page = 1) => {
      loading.value = true;
      try {
        const res = await axios.get("/tenant/orders/mail-logs", {
          params: { page, search: filters.value.search },
        });

        // ✅ Laravel pagination response
        logs.value = res.data.data.data;
        pagination.value = {
          current_page: res.data.data.current_page,
          last_page: res.data.data.last_page,
          total: res.data.data.total,
          per_page: res.data.data.per_page,
        };
      } catch (e) {
        console.error("Error fetching mail logs", e);
      } finally {
        loading.value = false;
      }
    };

    const applyFilters = () => {
      fetchLogs(1);
    };

    const applyColumnSelection = () => {
      selectedColumns.value = [...tempSelectedColumns.value];
    };

    const viewLog = (log) => {
      selectedLog.value = log;
      const modal = new bootstrap.Modal(document.getElementById("mailLogModal"));
      modal.show();
    };

    const changePage = (page) => {
      if (page >= 1 && page <= pagination.value.last_page) {
        fetchLogs(page);
      }
    };

    onMounted(() => {
      fetchLogs();
    });

    return {
      loading,
      filters,
      logs,
      allColumns,
      tempSelectedColumns,
      selectedColumns,
      visibleColumns,
      selectedLog,
      pagination,
      formatDate,
      applyFilters,
      applyColumnSelection,
      viewLog,
      changePage,
    };
  },
};
</script>
