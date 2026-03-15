<template>
  <div class="Categories-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <button class="btn btn-outline-secondary me-2" @click="showDeletedModal">
          <i class="fas fa-trash-restore-alt me-1"></i> Deleted Categories
        </button>
      </div>
      <h4 class="mb-0">Manage Categories</h4>
      <button class="btn btn-add-Category" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>Add Category
      </button>
    </div>

    <!-- Filters -->
    <div class="mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label mb-1">Search</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" placeholder="Search by name or description">
          </div>
          <!-- <div class="col-md-4">
            <label class="form-label mb-1">Parent</label>
            <select v-model="filters.parent_id" @change="applyFilters" class="form-select filter-input">
              <option value="">All</option>
              <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
          </div> -->
          <div class="col-md-4">
            <label class="form-label mb-1">Status</label>
            <select v-model="filters.is_active" @change="applyFilters" class="form-select filter-input">
              <option value="">All</option>
              <option :value="true">Active</option>
              <option :value="false">Inactive</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div v-else-if="categories.length === 0" class="text-center py-5 text-muted">
          <i class="fas fa-store fa-3x mb-3" style="color: #bdbdbd;"></i>
          <div class="h5 mb-2">No Categories Found</div>
          <div>No categories match your search criteria.<br>Try adjusting your filters.</div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm Categories-table">
            <thead>
              <tr>
                <th style="width: 32px;"></th>
                <th>Name</th>
                <!-- <th class="d-none d-md-table-cell">Parent</th> -->
                <th class="d-none d-md-table-cell">Status</th>
                <th class="d-none d-md-table-cell">Sort Order</th>
                <th class="d-none d-md-table-cell">Created At</th>
                <th class="d-none d-md-table-cell">Actions</th>
              </tr>
            </thead>
            <draggable v-model="categories" @end="onSortEnd" tag="tbody" :animation="200" handle=".drag-handle">
              <template #item="{ element: category }">
                <tr :key="category.id" :class="['d-block d-md-table-row w-100 mb-2 mb-md-0', { 'table-danger': category.deleted_at }]">
                  <td>
                    <span class="drag-handle" style="cursor: grab;"><i class="fas fa-grip-vertical"></i></span>
                  </td>
                  <td>
                    <div class="fw-bold">{{ category.name }}</div>
                    <div class="small text-muted">{{ category.description }}</div>
                  </td>
                  <!-- <td class="d-none d-md-table-cell">{{ category.parent?.name || 'None' }}</td> -->
                  <td class="d-none d-md-table-cell">
                    <span class="badge" :class="category.is_active ? 'bg-success' : 'bg-danger'">
                      {{ category.is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>
                  <td class="d-none d-md-table-cell">{{ category.sort_order }}</td>
                  <td class="d-none d-md-table-cell">{{ new Date(category.created_at).toLocaleDateString() }}</td>
                  <td class="d-none d-md-table-cell">
                    <div class="btn-group">
                      <button class="btn btn-sm btn-action btn-view me-1" @click="viewCategory(category)"><i class="fas fa-eye"></i></button>
                      <button class="btn btn-sm btn-action btn-edit me-1" @click="editCategory(category)" v-if="!category.deleted_at"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(category)" v-if="!category.deleted_at"><i class="fas fa-trash"></i></button>
                      <button class="btn btn-sm btn-action btn-restore me-1" @click="restoreCategory(category)" v-if="category.deleted_at"><i class="fas fa-undo"></i></button>
                      <button class="btn btn-sm btn-action btn-hard-delete" @click="confirmHardDelete(category)" v-if="category.deleted_at"><i class="fas fa-times"></i></button>
                    </div>
                  </td>
                </tr>
              </template>
            </draggable>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="CategoryModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content Category-modal-content">
          <div class="modal-header Category-modal-header">
            <h5 class="modal-title Category-modal-title">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body Category-modal-body">
            <form @submit.prevent="saveCategory">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                    <input id="categoryName" type="text" class="form-control" v-model="form.name" required placeholder="Category Name">
                  </div>
                </div>
                <!-- <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                    <select id="categoryParent" class="form-select" v-model="form.parent_id">
                      <option value="">No Parent</option>
                      <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                      </option>
                    </select>
                  </div>
                </div> -->
                <div class="col-md-6">
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" type="checkbox" id="categoryStatus" v-model="form.is_active">
                    <label class="form-check-label" for="categoryStatus">Active</label>
                  </div>
                </div>
                <div class="col-12">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    <textarea id="categoryDescription" class="form-control" v-model="form.description" rows="3" placeholder="Category Description"></textarea>
                  </div>
                </div>
                <!-- <div class="col-12">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-image"></i></span>
                    <input type="text" class="form-control" v-model="form.image" placeholder="Image URL">
                  </div>
                </div> -->
              </div>
              <div class="modal-footer Category-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-submit">{{ isEditing ? 'Update' : 'Create' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View Category Modal -->
    <div class="modal fade" id="viewCategoryModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Category Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="Category-details">
              <p><strong>Name:</strong> {{ selectedCategory?.name }}</p>
              <p><strong>Description:</strong> {{ selectedCategory?.description }}</p>
              <p><strong>Parent:</strong> {{ selectedCategory?.parent?.name || 'None' }}</p>
              <p><strong>Status:</strong> {{ selectedCategory?.is_active ? 'Active' : 'Inactive' }}</p>
              <p><strong>Sort Order:</strong> {{ selectedCategory?.sort_order }}</p>
              <p v-if="selectedCategory?.image"><strong>Image:</strong> <img :src="selectedCategory.image" class="img-thumbnail mt-2" style="max-width: 200px;"></p>
              <p><strong>Created At:</strong> {{ new Date(selectedCategory?.created_at).toLocaleString() }}</p>
              <p v-if="selectedCategory?.deleted_at">
                <strong>Deleted At:</strong> {{ new Date(selectedCategory?.deleted_at).toLocaleString() }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Deleted Categories Modal -->
    <div class="modal fade" id="deletedCategoriesModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Deleted Categories</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div v-if="deletedCategories.length === 0" class="text-center py-4 text-muted">
              <i class="fas fa-store-slash fa-2x mb-2"></i>
              <div>No deleted categories found.</div>
            </div>
            <div v-else class="table-responsive">
              <table class="table table-hover table-sm">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Deleted At</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="cat in deletedCategories" :key="cat.id">
                    <td>{{ cat.name }}</td>
                    <td>{{ cat.description }}</td>
                    <td>{{ new Date(cat.deleted_at).toLocaleString() }}</td>
                    <td>
                      <button class="btn btn-sm btn-success" @click="restoreCategory(cat)"><i class="fas fa-undo"></i> Restore</button>
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
import { ref, onMounted } from 'vue'
import { Modal, Tooltip } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'
import draggable from 'vuedraggable'

export default {
  name: 'Categories',
  components: { draggable },
  setup() {
    const categories = ref([])
    const parentCategories = ref([])
    const isEditing = ref(false)
    const selectedCategory = ref(null)
    const CategoryModal = ref(null)
    const viewCategoryModal = ref(null)
    const loading = ref(false)
    const deletedCategories = ref([])
    const showDeletedModal = async () => {
      try {
        const response = await axios.get('/tenant/categories', { params: { only_deleted: true } })
        deletedCategories.value = response.data.data.data
        deletedCategoriesModal.value.show()
      } catch (error) {
        console.error('Error fetching deleted categories:', error)
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to fetch deleted categories' })
      }
    }
    const deletedCategoriesModal = ref(null)

    const form = ref({
      name: '',
      description: '',
      parent_id: '',
      is_active: true,
      sort_order: 0,
      image: ''
    })

    const filters = ref({ search: '', parent_id: '', is_active: '' })

    const fetchCategories = async () => {
      loading.value = true
      try {
        const params = {}
        if (filters.value.search) params.search = filters.value.search
        if (filters.value.parent_id) params.parent_id = filters.value.parent_id
        if (filters.value.is_active !== '') params.is_active = filters.value.is_active
        const response = await axios.get('/tenant/categories', { params })
        categories.value = response.data.data.data
      } catch (error) {
        console.error('Error fetching categories:', error)
      } finally {
        loading.value = false
      }
    }

    const fetchParentCategories = async () => {
      try {
        const response = await axios.get('/tenant/categories')
        parentCategories.value = response.data.data.data
      } catch (error) {
        console.error('Error fetching parent categories:', error)
      }
    }

    const showCreateModal = () => {
      isEditing.value = false
      form.value = {
        name: '',
        description: '',
        parent_id: '',
        is_active: true,
        sort_order: 0,
        image: ''
      }
      CategoryModal.value.show()
    }

    const editCategory = (category) => {
      isEditing.value = true
      form.value = {
        id: category.id,
        name: category.name,
        description: category.description,
        parent_id: category.parent_id,
        is_active: category.is_active,
        sort_order: category.sort_order,
        image: category.image
      }
      CategoryModal.value.show()
    }

    const viewCategory = (category) => {
      selectedCategory.value = category
      viewCategoryModal.value.show()
    }

    const saveCategory = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/tenant/categories/${form.value.id}`, form.value)
        } else {
          await axios.post('/tenant/categories', form.value)
        }

        await fetchCategories()
        await fetchParentCategories()
        CategoryModal.value.hide()

        Swal.fire({
          icon: 'success',
          title: isEditing.value ? 'Category Updated' : 'Category Created',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error saving category:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to save category'
        })
      }
    }

    const confirmDelete = (category) => {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will delete the category. This action can be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/categories/${category.id}`)
            await fetchCategories()
            await fetchParentCategories()
            Swal.fire({
              icon: 'success',
              title: 'Category Deleted',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error deleting category:', error)
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.response?.data?.message || 'Failed to delete category'
            })
          }
        }
      })
    }

    const confirmHardDelete = (category) => {
      Swal.fire({
        title: 'Are you absolutely sure?',
        text: "This will permanently delete the category. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, permanently delete!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/categories/${category.id}/force`)
            await fetchCategories()
            await fetchParentCategories()
            Swal.fire({
              icon: 'success',
              title: 'Category Permanently Deleted',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error permanently deleting category:', error)
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.response?.data?.message || 'Failed to permanently delete category'
            })
          }
        }
      })
    }

    const restoreCategory = async (category) => {
      try {
        await axios.post(`/tenant/categories/${category.id}/restore`)
        await fetchCategories()
        await fetchParentCategories()
        Swal.fire({
          icon: 'success',
          title: 'Category Restored',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error restoring category:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to restore category'
        })
      }
    }

    const applyFilters = () => {
      fetchCategories()
    }

    const onSortEnd = async () => {
      try {
        const order = categories.value.map((cat, idx) => ({ id: cat.id, sort_order: idx }));
        await axios.post('/tenant/categories/reorder', { order });
        await fetchCategories();
        Swal.fire({ icon: 'success', title: 'Order updated', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500 });
      } catch (error) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update order' });
      }
    };

    onMounted(() => {
      CategoryModal.value = new Modal(document.getElementById('CategoryModal'))
      viewCategoryModal.value = new Modal(document.getElementById('viewCategoryModal'))
      deletedCategoriesModal.value = new Modal(document.getElementById('deletedCategoriesModal'))
      fetchCategories()
      fetchParentCategories()
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new Tooltip(el);
      });
    })

    return {
      categories,
      parentCategories,
      isEditing,
      selectedCategory,
      form,
      showCreateModal,
      editCategory,
      viewCategory,
      saveCategory,
      confirmDelete,
      confirmHardDelete,
      restoreCategory,
      filters,
      applyFilters,
      loading,
      deletedCategories,
      showDeletedModal,
      deletedCategoriesModal,
      onSortEnd,
    }
  }
}
</script>

<style scoped>
.Categories-page {
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

.Categories-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.Categories-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.Categories-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.Categories-table tbody tr:hover {
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
</style>