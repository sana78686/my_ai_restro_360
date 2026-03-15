<template>
  <div class="Notifications-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-outline-secondary" @click="refresh">
          <i class="fas fa-sync-alt me-1"></i> {{ $t('tenant_dashboard.notifications.actions.refresh') }}
        </button>
        <button
          class="btn btn-mark-all btn-outline-danger"
          :disabled="!hasUnread || markingAll"
          @click="markAllAsRead"
          :title="$t('tenant_dashboard.notifications.actions.markAllRead')"
        >
          <i class="fas fa-check-double me-1"></i>
          <span v-if="!markingAll">{{ $t('tenant_dashboard.notifications.actions.markAllRead') }}</span>
          <span v-else>{{ $t('tenant_dashboard.notifications.states.marking') }}</span>
        </button>
      </div>

      <h4 class="mb-0">{{ $t('tenant_dashboard.notifications.title') }}</h4>

      <div class="d-flex align-items-center gap-2">
        <div class="position-relative me-2">
          <input
            v-model="filters.search"
            class="form-control filter-input"
            :placeholder="$t('tenant_dashboard.notifications.search.placeholder')"
            @input="onSearchInput"
            style="min-width: 220px"
          />
        </div>
        <div class="btn-group">
          <button
            class="btn btn-outline-secondary"
            :class="{ active: filters.only_unread === false }"
            @click="toggleUnread(false)"
          >
            {{ $t('tenant_dashboard.notifications.filters.all') }}
          </button>
          <button
            class="btn btn-outline-secondary"
            :class="{ active: filters.only_unread === true }"
            @click="toggleUnread(true)"
          >
            {{ $t('tenant_dashboard.notifications.filters.unread') }} <span class="badge bg-danger ms-1">{{ unreadCount }}</span>
          </button>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">{{ $t('common.loading') }}</span>
          </div>
        </div>

        <div v-else-if="filteredNotifications.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-bell-slash fa-3x mb-3" style="color: #bdbdbd"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.notifications.empty.title') }}</div>
          <div>
            {{ $t('tenant_dashboard.notifications.empty.message') }}
          </div>
        </div>

        <div v-else>
          <ul class="list-group notifications-list">
            <li
              v-for="note in paginated"
              :key="note.id"
              class="list-group-item d-flex justify-content-between align-items-start"
              :class="{ 'unread': !getIsRead(note) }"
            >
              <div class="d-flex align-items-start gap-3 w-100">
                <div class="notif-icon">
                  <i :class="noteIcon(note)"></i>
                </div>
                <div class="w-100">
                  <div class="d-flex justify-content-between align-items-start">
                    <div>
                      <div class="fw-semibold notif-title">{{ noteTitle(note) }}</div>
                      <div class="small text-muted notif-body">{{ noteBody(note) }}</div>
                    </div>
                    <div class="text-end ms-3">
                      <div class="small text-muted">{{ formatDate(note.created_at) }}</div>
                      <div class="mt-2">
                        <button
                          v-if="!getIsRead(note)"
                          class="btn btn-sm btn-read"
                          @click="markAsRead(note)"
                          :disabled="marking[note.id]"
                          :title="$t('tenant_dashboard.notifications.actions.markRead')"
                        >
                          <i class="fas fa-check me-1"></i>
                          {{ $t('tenant_dashboard.notifications.actions.markRead') }}
                        </button>
                        <span v-else class="badge bg-success">{{ $t('tenant_dashboard.notifications.states.read') }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>

          <!-- Pagination -->
          <div v-if="pagination.total > pagination.per_page" class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
              {{ $t('tenant_dashboard.notifications.pagination.showing', { from: pagination.from, to: pagination.to, total: filteredNotifications.length }) }}
            </div>
            <nav>
              <ul class="pagination mb-0">
                <li class="page-item" :class="{ disabled: pagination.current === 1 }">
                  <button class="page-link" @click="goToPage(pagination.current - 1)" :disabled="pagination.current === 1">&laquo;</button>
                </li>
                <li v-for="p in pageWindow" :key="p" class="page-item" :class="{ active: p === pagination.current }">
                  <button class="page-link" @click="goToPage(p)">{{ p }}</button>
                </li>
                <li class="page-item" :class="{ disabled: pagination.current === pagination.last }">
                  <button class="page-link" @click="goToPage(pagination.current + 1)" :disabled="pagination.current === pagination.last">&raquo;</button>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted, watch } from "vue";
import axios from "axios";
import Swal from "sweetalert2";

export default {
  name: "Notifications",
  setup() {
    const allNotifications = ref([]);
    const loading = ref(false);
    const filters = ref({
      search: "",
      only_unread: false,
    });

    const marking = ref({}); // map id -> bool
    const markingAll = ref(false);

    // client-side pagination (server currently returns all)
    const pagination = ref({
      current: 1,
      per_page: 8,
      total: 0,
      from: 0,
      to: 0,
      last: 1,
    });

    const fetchNotifications = async () => {
      loading.value = true;
      try {
        const { data } = await axios.get("/tenant/notifications/all");
        // Expecting array of notifications
        allNotifications.value = Array.isArray(data) ? data : data.data || [];
        setupPagination();
      } catch (e) {
        allNotifications.value = [];
        Swal.fire({ 
          icon: "error", 
          title: this.$t('common.error'), 
          text: this.$t('tenant_dashboard.notifications.errors.loadFailed') 
        });
      }
      loading.value = false;
    };

    const setupPagination = () => {
      const total = filteredNotifications.value.length;
      pagination.value.total = total;
      pagination.value.last = Math.max(1, Math.ceil(total / pagination.value.per_page));
      goToPage(1);
    };

    const getIsRead = (n) => {
      // pivot.is_read or is_read or read
      return !!(n.is_read ?? (n.pivot && n.pivot.is_read) ?? n.read);
    };

    const setAsReadLocal = (n) => {
      if ("is_read" in n) n.is_read = true;
      else if (n.pivot) n.pivot.is_read = true;
      else n.read = true;
    };

    const markAsRead = async (notification) => {
      if (getIsRead(notification)) return;
      marking.value[notification.id] = true;
      try {
        await axios.post(`/tenant/notifications/${notification.id}/mark-as-read`);
        setAsReadLocal(notification);
      } catch (e) {
        Swal.fire({ 
          icon: "error", 
          title: this.$t('common.error'), 
          text: this.$t('tenant_dashboard.notifications.errors.markReadFailed') 
        });
      } finally {
        marking.value[notification.id] = false;
      }
    };

    const markAllAsRead = async () => {
      const unread = filteredNotifications.value.filter((n) => !getIsRead(n));
      if (!unread.length) return;
      const confirm = await Swal.fire({
        title: this.$t('tenant_dashboard.notifications.confirm.markAll.title'),
        text: this.$t('tenant_dashboard.notifications.confirm.markAll.message', { count: unread.length }),
        icon: "question",
        showCancelButton: true,
        confirmButtonText: this.$t('tenant_dashboard.notifications.confirm.markAll.confirm'),
        cancelButtonText: this.$t('common.cancel')
      });
      if (!confirm.isConfirmed) return;

      markingAll.value = true;
      try {
        // make sequential to avoid overwhelming server; small count expected
        for (const n of unread) {
          await axios.post(`/tenant/notifications/${n.id}/read`);
          setAsReadLocal(n);
        }
        Swal.fire({ 
          icon: "success", 
          title: this.$t('tenant_dashboard.notifications.success.markAllRead') 
        });
      } catch (e) {
        Swal.fire({ 
          icon: "error", 
          title: this.$t('common.error'), 
          text: this.$t('tenant_dashboard.notifications.errors.markAllFailed') 
        });
      } finally {
        markingAll.value = false;
      }
    };

    const formatDate = (iso) => {
      try {
        return new Date(iso).toLocaleString();
      } catch {
        return iso || "";
      }
    };

    const noteTitle = (n) => {
      // support various notification structures
      return n.title || (n.data && (n.data.title || n.data.heading)) || this.$t('tenant_dashboard.notifications.default.title');
    };

    const noteBody = (n) => {
      return n.body || (n.data && (n.data.message || n.data.body)) || (typeof n.message === "string" ? n.message : this.$t('tenant_dashboard.notifications.default.body'));
    };

    const noteIcon = (n) => {
      // simple heuristic based on type or data
      const t = (n.type || (n.data && n.data.type) || "").toLowerCase();
      if (t.includes("order")) return "fas fa-receipt text-danger";
      if (t.includes("invoice")) return "fas fa-file-invoice text-danger";
      if (t.includes("warning") || t.includes("alert")) return "fas fa-exclamation-triangle text-warning";
      return "fas fa-bell text-danger";
    };

    // Filtering + search
    const filteredNotifications = computed(() => {
      const q = (filters.value.search || "").toLowerCase().trim();
      return allNotifications.value.filter((n) => {
        if (filters.value.only_unread && getIsRead(n)) return false;
        if (!q) return true;
        const hay = `${noteTitle(n)} ${noteBody(n)} ${n.type || ""}`.toLowerCase();
        return hay.includes(q);
      });
    });

    // client-side pagination slices
    const paginated = computed(() => {
      const start = (pagination.value.current - 1) * pagination.value.per_page;
      const end = start + pagination.value.per_page;
      const slice = filteredNotifications.value.slice(start, end);
      pagination.value.from = filteredNotifications.value.length ? start + 1 : 0;
      pagination.value.to = Math.min(filteredNotifications.value.length, end);
      return slice;
    });

    const goToPage = (p) => {
      if (p < 1) p = 1;
      if (p > pagination.value.last) p = pagination.value.last;
      pagination.value.current = p;
    };

    const pageWindow = computed(() => {
      const total = pagination.value.last;
      const current = pagination.value.current;
      const windowSize = 5;
      let start = Math.max(1, current - 2);
      let end = Math.min(total, current + 2);
      if (end - start < windowSize - 1) {
        if (start === 1) end = Math.min(total, start + windowSize - 1);
        else if (end === total) start = Math.max(1, end - windowSize + 1);
      }
      const arr = [];
      for (let i = start; i <= end; i++) arr.push(i);
      return arr;
    });

    // derived counts
    const unreadCount = computed(() => allNotifications.value.filter((n) => !getIsRead(n)).length);
    const hasUnread = computed(() => unreadCount.value > 0);

    // search debounce
    let searchTimer = null;
    const onSearchInput = () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(() => {
        setupPagination();
      }, 300);
    };

    const toggleUnread = (val) => {
      filters.value.only_unread = val;
      setupPagination();
    };

    const refresh = async () => {
      await fetchNotifications();
    };

    onMounted(async () => {
      await fetchNotifications();
    });

    // update pagination when filtered list changes
    watch(filteredNotifications, () => {
      setupPagination();
    });

    return {
      allNotifications,
      loading,
      filters,
      formatDate,
      noteTitle,
      noteBody,
      noteIcon,
      getIsRead,
      markAsRead,
      markAllAsRead,
      marking,
      markingAll,
      filteredNotifications,
      paginated,
      pagination,
      pageWindow,
      goToPage,
      unreadCount,
      hasUnread,
      onSearchInput,
      toggleUnread,
      refresh,
    };
  },
};
</script>

<style scoped>
/* Your existing CSS remains the same */
.Notifications-page {
  max-width: 980px;
  margin: 0 auto;
  padding: 20px;
  font-family: "Inter", "Roboto", "Segoe UI", Arial, sans-serif;
  font-size: 16px;
}

.filter-input {
  border-radius: 8px;
  font-size: 15px;
  border: 1px solid #e0e0e0;
  background: #fafbfc;
  padding: 8px 12px;
}

.notifications-list {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(60, 60, 60, 0.06);
  padding: 0;
  list-style: none;
}

.notifications-list .list-group-item {
  border: 0;
  border-bottom: 1px solid #f0f0f0;
  background: #fff;
  transition: background 0.12s;
  padding: 14px 18px;
}

.notifications-list .list-group-item.unread {
  background: #fff3f3;
  box-shadow: inset 0 0 0 1px rgba(198, 40, 40, 0.03);
}

.notif-icon {
  width: 44px;
  height: 44px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fff0f0;
  color: #c62828;
  font-size: 18px;
  flex-shrink: 0;
  margin-top: 4px;
}

.notif-title {
  color: #b71c1c;
  font-weight: 600;
  margin-bottom: 4px;
}

.notif-body {
  color: #6b6b6b;
}

.btn-read {
  background: #c62828;
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 6px;
  font-size: 0.85rem;
}
.btn-read:hover {
  background: #b71c1c;
  color: #fff;
}

.btn-mark-all {
  border-radius: 8px;
  padding: 0.5rem 0.9rem;
  font-weight: 500;
  color: #c62828;
  border: 1px solid rgba(198,40,40,0.12);
}

.btn-mark-all:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 4px 20px rgba(60, 60, 60, 0.04);
}

.page-link {
  cursor: pointer;
}

.page-item.disabled .page-link {
  cursor: not-allowed;
}

/* small responsive adjustments */
@media (max-width: 576px) {
  .Notifications-page {
    padding: 12px;
  }
  .notif-icon {
    display: none;
  }
}
</style>