<template>
  <div class="CMS-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <button class="btn btn-outline-secondary me-2" @click="showDeletedModal">
          <i class="fas fa-trash-restore-alt me-1"></i> {{ $t('tenant_dashboard.cms.deletedPages') }}
        </button>
      </div>
      <h4 class="mb-0">{{ $t('tenant_dashboard.cms.managePages') }}</h4>
      <button class="btn btn-add-CMS" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>{{ $t('tenant_dashboard.cms.addPage') }}
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end flex-nowrap flex-md-nowrap flex-column flex-md-row">
          <div class="col-md-4 col-12 mb-2 mb-md-0">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.cms.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.cms.searchPlaceholder')">
          </div>
          <div class="col-md-4 col-12 mb-2 mb-md-0">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.cms.menu') }}</label>
            <select v-model="filters.menu_id" @change="applyFilters" class="form-select filter-input">
              <option value="">{{ $t('tenant_dashboard.cms.allMenus') }}</option>
              <option v-for="menu in menus" :key="menu.id" :value="menu.id">{{ menu.name }}</option>
            </select>
          </div>
          <div class="col-md-4 col-12 mb-2 mb-md-0">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.cms.status') }}</label>
            <select v-model="filters.status" @change="applyFilters" class="form-select filter-input">
              <option value="">{{ $t('tenant_dashboard.cms.all') }}</option>
              <option value="active">{{ $t('tenant_dashboard.cms.active') }}</option>
              <option value="inactive">{{ $t('tenant_dashboard.cms.inactive') }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- CMS Pages Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="cmsPages.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-file-alt fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">{{ $t('tenant_dashboard.cms.noPagesFound') }}</div>
          <div>{{ $t('tenant_dashboard.cms.noPagesMessage') }}</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm CMS-table">
            <thead>
              <tr>
                <th style="width: 32px;"></th>
                <th>{{ $t('tenant_dashboard.cms.pageTitle') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.menu') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.keyword') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.status') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.order') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.createdAt') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.cms.actions') }}</th>
              </tr>
            </thead>
            <draggable v-model="cmsPages" @end="onSortEnd" tag="tbody" :animation="200" handle=".drag-handle">
              <template #item="{ element: page }">
                <tr :key="page.id" :class="['d-block d-md-table-row w-100 mb-2 mb-md-0', { 'table-danger': page.deleted_at }]">
                  <td>
                    <span class="drag-handle" style="cursor: grab;"><i class="fas fa-grip-vertical"></i></span>
                  </td>
                  <td>
                    <div class="fw-bold">{{ page.page_title }}</div>
                    <div class="small text-muted">{{ page.content ? page.content.substring(0, 100) + '...' : $t('tenant_dashboard.cms.noContent') }}</div>
                  </td>
                  <td class="d-none d-md-table-cell">{{ page.menu?.page_title || $t('tenant_dashboard.cms.noMenu') }}</td>
                  <td class="d-none d-md-table-cell">{{ page.keyword }}</td>
                  <td class="d-none d-md-table-cell">
                    <span class="badge" :class="page.status === 'active' ? 'bg-success' : 'bg-danger'">
                      {{ page.status === 'active' ? $t('tenant_dashboard.cms.active') : $t('tenant_dashboard.cms.inactive') }}
                    </span>
                  </td>
                  <td class="d-none d-md-table-cell">{{ page.order }}</td>
                  <td class="d-none d-md-table-cell">{{ new Date(page.created_at).toLocaleDateString() }}</td>
                  <td class="d-none d-md-table-cell">
                    <div class="btn-group">
                      <button class="btn btn-sm btn-action btn-view me-1" @click="viewPage(page)"><i class="fas fa-eye"></i></button>
                      <button class="btn btn-sm btn-action btn-edit me-1" @click="editPage(page)" v-if="!page.deleted_at"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(page)" v-if="!page.deleted_at"><i class="fas fa-trash"></i></button>
                      <button class="btn btn-sm btn-action btn-restore me-1" @click="restorePage(page)" v-if="page.deleted_at"><i class="fas fa-undo"></i></button>
                      <button class="btn btn-sm btn-action btn-hard-delete" @click="confirmHardDelete(page)" v-if="page.deleted_at"><i class="fas fa-times"></i></button>
                    </div>
                  </td>
                </tr>
              </template>
            </draggable>
          </table>
        </div>
        <!-- Pagination -->
        <div v-if="pagination.total > 0" class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            {{ $t('tenant_dashboard.cms.showing') }} {{ pagination.from }} {{ $t('tenant_dashboard.cms.to') }} {{ pagination.total }} {{ $t('tenant_dashboard.cms.entries') }}
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

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="CMSModal" tabindex="-1">
      <div class="modal-dialog modal-xl">
        <div class="modal-content CMS-modal-content">
          <div class="modal-header CMS-modal-header">
            <h5 class="modal-title CMS-modal-title">{{ isEditing ? $t('tenant_dashboard.cms.editPage') : $t('tenant_dashboard.cms.addPage') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body CMS-modal-body">
            <form @submit.prevent="savePage">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-heading"></i></span>
                    <input id="pageTitle" type="text" class="form-control" v-model="form.page_title" required placeholder="Page Title">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                    <input id="pageKeyword" type="text" class="form-control" v-model="form.keyword" required placeholder="Page Slug like about-us">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-list"></i></span>
                    <select id="pageMenu" class="form-select" v-model="form.menu_id">
                      <option value="">{{ $t('tenant_dashboard.cms.noMenu') }}</option>
                      <option v-for="menu in menus" :key="menu.id" :value="menu.id">
                        {{ menu.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                    <input id="pageOrder" type="number" class="form-control" v-model="form.order" placeholder="Order">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="pageStatus" v-model="form.status" true-value="active" false-value="inactive">
                    <label class="form-check-label" for="pageStatus">{{ $t('tenant_dashboard.cms.active') }}</label>
                  </div>
                </div>
                 <div class="col-12">
                   <label class="form-label">{{ $t('tenant_dashboard.cms.pageContent') }}</label>
                   <div class="editor-wrapper">
                     <QuillEditor v-model:content="form.content" contentType="html" theme="snow" :options="editorOptions" />
                   </div>
                 </div>
              </div>
              <div class="modal-footer CMS-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.cms.cancel') }}</button>
                <button type="submit" class="btn btn-submit">{{ isEditing ? $t('tenant_dashboard.cms.update') : $t('tenant_dashboard.cms.create') }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Page Modal -->
    <div class="modal fade" id="viewPageModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.cms.pageDetails') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="CMS-details">
              <p><strong>{{ $t('tenant_dashboard.cms.title') }}:</strong> {{ selectedPage?.page_title }}</p>
              <p><strong>{{ $t('tenant_dashboard.cms.keyword') }}:</strong> {{ selectedPage?.keyword }}</p>
              <p><strong>{{ $t('tenant_dashboard.cms.menu') }}:</strong> {{ selectedPage?.menu?.page_title || $t('tenant_dashboard.cms.noMenu') }}</p>
              <p><strong>{{ $t('tenant_dashboard.cms.status') }}:</strong> {{ selectedPage?.status === 'active' ? $t('tenant_dashboard.cms.active') : $t('tenant_dashboard.cms.inactive') }}</p>
              <p><strong>{{ $t('tenant_dashboard.cms.order') }}:</strong> {{ selectedPage?.order }}</p>
              <p><strong>{{ $t('tenant_dashboard.cms.content') }}:</strong></p>
              <div class="border p-3 bg-light rounded" v-html="selectedPage?.content || $t('tenant_dashboard.cms.noContent')"></div>
              <p><strong>{{ $t('tenant_dashboard.cms.createdAt') }}:</strong> {{ new Date(selectedPage?.created_at).toLocaleString() }}</p>
              <p v-if="selectedPage?.deleted_at">
                <strong>{{ $t('tenant_dashboard.cms.deletedAt') }}:</strong> {{ new Date(selectedPage?.deleted_at).toLocaleString() }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.cms.close') }}</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Deleted Pages Modal -->
    <div class="modal fade" id="deletedPagesModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ $t('tenant_dashboard.cms.deletedPages') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="deletedPages.length === 0" class="text-center py-4 text-muted">
              <i class="fas fa-file-slash fa-2x mb-2"></i>
              <div>{{ $t('tenant_dashboard.cms.noDeletedPagesFound') }}</div>
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover table-sm">
                <thead>
                  <tr>
                    <th>{{ $t('tenant_dashboard.cms.title') }}</th>
                    <th>{{ $t('tenant_dashboard.cms.keyword') }}</th>
                    <th>{{ $t('tenant_dashboard.cms.deletedAt') }}</th>
                    <th>{{ $t('tenant_dashboard.cms.action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="page in deletedPages" :key="page.id">
                    <td>{{ page.page_title }}</td>
                    <td>{{ page.keyword }}</td>
                    <td>{{ new Date(page.deleted_at).toLocaleString() }}</td>
                    <td>
                      <button class="btn btn-sm btn-success" @click="restorePage(page)"><i class="fas fa-undo"></i> {{ $t('tenant_dashboard.cms.restore') }}</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useI18n } from 'vue-i18n' // Add this import
import { Modal, Tooltip } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'
import draggable from 'vuedraggable'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'

export default {
  name: 'CMS',
  components: { draggable, QuillEditor },
  setup() {
    const { t } = useI18n() // Add this line
    
    const cmsPages = ref([])
    const menus = ref([])
    const isEditing = ref(false)
    const selectedPage = ref(null)
    const CMSModal = ref(null)
    const viewPageModal = ref(null)
    const loading = ref(false)
    const deletedPages = ref([])
    const pagination = ref({
      current_page: 1,
      last_page: 1,
      per_page: 10,
      total: 0,
      from: 0,
      to: 0
    })

    const toolbarOptions = [
      ['bold', 'italic', 'underline', 'strike'],
      [{ 'header': [1, 2, 3, 4, 5, false] }],
      [{ 'list': 'ordered' }, { 'list': 'bullet' }],
      [{ 'indent': '-1' }, { 'indent': '+1' }],
      [{ 'align': [] }],
      ['link', 'image'],
      ['clean']
    ]

    const editorOptions = {
      modules: {
        toolbar: {
          container: toolbarOptions,
          handlers: {
            'image': imageHandler
          }
        }
      }
    }

    async function imageHandler() {
      const input = document.createElement('input');
      input.setAttribute('type', 'file');
      input.setAttribute('accept', 'image/*');
      input.click();

      input.onchange = async () => {
        const file = input.files[0];
        if (file) {
          const formData = new FormData();
          formData.append('image', file);

          try {
            const response = await axios.post('/tenant/cms_menu/upload-image', formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            });
            const imageUrl = response.data.url;
            const quill = this.quill;
            const range = quill.getSelection(true);
            quill.insertEmbed(range.index, 'image', imageUrl);
            quill.setSelection(range.index + 1);
          } catch (error) {
            console.error('Image upload failed:', error);
            Swal.fire({
              icon: 'error',
              title: t('tenant_dashboard.cms.alerts.failed'),
              text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.message')
            });
          }
        }
      };
    }

    const showDeletedModal = async () => {
      try {
        const response = await axios.get('/tenant/cms_menu', { params: { only_deleted: true } })
        deletedPages.value = response.data.data.data
        deletedPagesModal.value.show()
      } catch (error) {
        console.error('Error fetching deleted pages:', error)
        Swal.fire({ 
          icon: 'error', 
          title: t('dashboard_common.error'),
          text: t('tenant_dashboard.cms.alerts.deleted_modal.message') 
        })
      }
    }
    const deletedPagesModal = ref(null)

    const form = ref({
      page_title: '',
      keyword: '',
      menu_id: '',
      content: '',
      order: 0,
      status: 'active'
    })

    const filters = ref({ search: '', menu_id: '', status: '' })

    const fetchPages = async (page = 1) => {
      loading.value = true
      try {
        const params = {
          page,
          per_page: pagination.value.per_page
        }
        if (filters.value.search) params.search = filters.value.search
        if (filters.value.menu_id) params.menu_id = filters.value.menu_id
        if (filters.value.status) params.status = filters.value.status
        const response = await axios.get('/tenant/cms_menu', { params })
        const d = response.data.data
        cmsPages.value = d.data
        pagination.value = {
          current_page: d.current_page,
          last_page: d.last_page,
          per_page: d.per_page,
          total: d.total,
          from: d.from,
          to: d.to
        }
      } catch (error) {
        cmsPages.value = []
        pagination.value = { current_page: 1, last_page: 1, per_page: 10, total: 0, from: 0, to: 0 }
      } finally {
        loading.value = false
      }
    }

    const fetchMenus = async (excludeId = null) => {
      try {
        const response = await axios.get('/tenant/cms_menu/menus', { params: excludeId ? { exclude_id: excludeId } : {} })
        menus.value = response.data.data
      } catch (error) {
        console.error('Error fetching menus:', error)
      }
    }

    const showCreateModal = () => {
      isEditing.value = false
      form.value = {
        page_title: '',
        keyword: '',
        menu_id: '',
        content: '',
        order: 0,
        status: 'active'
      }
      fetchMenus().finally(() => {
        CMSModal.value.show()
      })
    }

    const editPage = (page) => {
      isEditing.value = true
      form.value = {
        id: page.id,
        page_title: page.page_title,
        keyword: page.keyword,
        menu_id: page.menu_id,
        content: page.content,
        order: page.order,
        status: page.status
      }
      fetchMenus(page.id).finally(() => {
        CMSModal.value.show()
      })
    }

    const viewPage = (page) => {
      selectedPage.value = page
      viewPageModal.value.show()
    }

    const savePage = async () => {
      try {
        const payload = { ...form.value }
        // normalize empty menu_id to null
        if (payload.menu_id === '' || payload.menu_id === undefined) payload.menu_id = null
        if (isEditing.value) {
          await axios.put(`/tenant/cms_menu/${payload.id}`, payload)
        } else {
          await axios.post('/tenant/cms_menu', payload)
        }

        await fetchPages()
        CMSModal.value.hide()

        Swal.fire({
          icon: 'success',
          title: isEditing.value 
            ? t('tenant_dashboard.cms.alerts.page_update_or_create.updated')
            : t('tenant_dashboard.cms.alerts.page_update_or_create.created'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error saving page:', error)
        Swal.fire({
          icon: 'error',
          title: t('dashboard_common.error'),
          text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.page_update_or_create.failed')
        })
      }
    }

    const confirmDelete = (page) => {
      Swal.fire({
        title: t('tenant_dashboard.cms.alerts.delete.title'),
        text: t('tenant_dashboard.cms.alerts.delete.text'),
        icon: 'warning',
        showCancelButton: true,
        cancelButtonText: t('tenant_dashboard.cms.alerts.delete.cancel_button'),
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: t('tenant_dashboard.cms.alerts.delete.confirm_button')
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/cms_menu/${page.id}`)
            await fetchPages()
            Swal.fire({
              icon: 'success',
              title: t('tenant_dashboard.cms.alerts.deleted.title'),
              toast: true,
              position: 'top-end',
              text: t('tenant_dashboard.cms.alerts.deleted.message'),
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error deleting page:', error)
            Swal.fire({
              icon: 'error',
              title: t('dashboard_common.error'),
              text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.error.message')
            })
          }
        }
      })
    }

    const confirmHardDelete = (page) => {
      Swal.fire({
        title: t('tenant_dashboard.cms.alerts.confirm_hard_delete.title'),
        text: t('tenant_dashboard.cms.alerts.confirm_hard_delete.message'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: t('tenant_dashboard.cms.alerts.confirm_hard_delete.confirmButton')
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/cms_menu/${page.id}/force`)
            await fetchPages()
            Swal.fire({
              icon: 'success',
              title: t('tenant_dashboard.cms.alerts.hard_deleted.title'),
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error permanently deleting page:', error)
            Swal.fire({
              icon: 'error',
              title: t('dashboard_common.error'),
              text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.hard_delete_error.message')
            })
          }
        }
      })
    }

    const restorePage = async (page) => {
      try {
        await axios.post(`/tenant/cms_menu/${page.id}/restore`)
        await fetchPages()
        Swal.fire({
          icon: 'success',
          title: t('tenant_dashboard.cms.alerts.restore_page.title'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error restoring page:', error)
        Swal.fire({
          icon: 'error',
          title: t('dashboard_common.error'),
          text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.failed_restore.title')
        })
      }
    }

    const applyFilters = () => {
      fetchPages(1)
    }

    const goToPage = (page) => {
      if (page < 1 || page > pagination.value.last_page) return
      fetchPages(page)
    }

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

    const onSortEnd = async () => {
      try {
        const order = cmsPages.value.map((page, idx) => ({ id: page.id, order: idx }));
        await axios.post('/tenant/cms_menu/reorder', { order });
        await fetchPages();
        Swal.fire({ 
          icon: 'success', 
          title: t('tenant_dashboard.cms.alerts.order.title'), 
          toast: true, 
          position: 'top-end', 
          showConfirmButton: false, 
          timer: 1500 
        });
      } catch (error) {
        Swal.fire({ 
          icon: 'error', 
          title: t('dashboard_common.error'), 
          text: error.response?.data?.message || t('tenant_dashboard.cms.alerts.order.failed') 
        });
      }
    };

    onMounted(() => {
      CMSModal.value = new Modal(document.getElementById('CMSModal'))
      viewPageModal.value = new Modal(document.getElementById('viewPageModal'))
      deletedPagesModal.value = new Modal(document.getElementById('deletedPagesModal'))
      fetchPages()
      fetchMenus()
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new Tooltip(el);
      });
    })

    return {
      cmsPages,
      menus,
      isEditing,
      selectedPage,
      form,
      showCreateModal,
      editPage,
      viewPage,
      savePage,
      confirmDelete,
      confirmHardDelete,
      restorePage,
      filters,
      applyFilters,
      loading,
      deletedPages,
      showDeletedModal,
      deletedPagesModal,
      onSortEnd,
      pagination,
      goToPage,
      paginationWindow,
      editorOptions,
      t // Expose t function if needed in template
    }
  }
}
</script>

<style scoped>
.CMS-page {
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

.btn-add-CMS {
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
.btn-add-CMS:hover, .btn-add-CMS:focus {
  background: #0d47a1;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21,101,192,0.13);
}

.CMS-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.CMS-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.CMS-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.CMS-table tbody tr:hover {
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

.CMS-details p {
  margin-bottom: 0.5rem;
}

.table td {
  vertical-align: middle;
}

.CMS-modal-content {
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
  background: #fff;
  border-radius: 0 0 12px 12px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 8px 32px rgba(60,60,60,0.13);
  max-width: 900px;
  margin: auto;
  padding: 0;
}
.CMS-modal-header {
  background: #c62828;
  color: #fff;
  border-radius: 0;
  border: none;
  padding: 1.2rem 2rem;
  position: relative;
}
.CMS-modal-title {
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 0.01em;
}
.CMS-modal-header .btn-close {
  filter: invert(1) grayscale(1) brightness(2);
  opacity: 1;
}
.CMS-modal-body {
  background: #fafbfc;
  padding: 2rem 2rem 1.5rem 2rem;
}
.CMS-modal-footer {
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

.editor-wrapper {
  margin-top: 4px;
  margin-bottom: 16px; /* keep footer outside editor borders */
}
.editor-wrapper :deep(.ql-toolbar.ql-snow) {
  border: 1px solid #dee2e6;
  border-bottom: none;
  background: #ffffff;
  border-top-left-radius: 0.5rem;
  border-top-right-radius: 0.5rem;
  padding: 0.5rem 0.5rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem 0.5rem;
}
.editor-wrapper :deep(.ql-toolbar .ql-formats) {
  margin-right: 0.5rem;
}
.editor-wrapper :deep(.ql-toolbar button),
.editor-wrapper :deep(.ql-toolbar .ql-picker-label) {
  border: 1px solid #e9ecef;
  background: #fff;
  color: #495057;
  border-radius: 0.375rem;
  padding: 0.35rem 0.5rem;
}
.editor-wrapper :deep(.ql-toolbar button:hover),
.editor-wrapper :deep(.ql-toolbar .ql-picker-label:hover),
.editor-wrapper :deep(.ql-toolbar button:focus),
.editor-wrapper :deep(.ql-toolbar .ql-picker-label:focus) {
  background: #f8f9fa;
  border-color: #dee2e6;
}
.editor-wrapper :deep(.ql-container.ql-snow) {
  border: 1px solid #dee2e6;
  border-bottom-left-radius: 0.5rem;
  border-bottom-right-radius: 0.5rem;
  min-height: 260px;
  background: #fff;
}
.editor-wrapper :deep(.ql-editor) {
  min-height: 220px;
  font-size: 16px;
  line-height: 1.6;
}

/* Keep icons subtle */
.editor-wrapper :deep(.ql-stroke) { stroke: #495057; }
.editor-wrapper :deep(.ql-fill) { fill: #495057; }

/* Mobile tweaks */
@media (max-width: 576px) {
  .editor-wrapper :deep(.ql-toolbar.ql-snow) {
    padding: 0.4rem;
  }
  .editor-wrapper :deep(.ql-toolbar .ql-formats) {
    margin-right: 0.25rem;
  }
  .editor-wrapper :deep(.ql-toolbar button),
  .editor-wrapper :deep(.ql-toolbar .ql-picker-label) {
    padding: 0.3rem 0.4rem;
  }
}

@media (max-width: 767.98px) {
  .filter-body .row {
    flex-direction: column !important;
    gap: 0.5rem;
  }
  .filter-body .col-md-4, .filter-body .col-12 {
    width: 100% !important;
    max-width: 100% !important;
  }
}
</style>

