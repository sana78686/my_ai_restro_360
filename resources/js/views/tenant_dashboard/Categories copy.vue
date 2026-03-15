<template>
  <div class="users-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">Manage Categories</h4>
      <button class="btn btn-add-user" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>Add Category
      </button>
    </div>

    <!-- Filters -->
    <div class="filter-card mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label mb-1">Search</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" placeholder="Search by name or email">
          </div>
          <div class="col-md-4">
            <label class="form-label mb-1">Status</label>
            <select v-model="filters.status" @change="applyFilters" class="form-select filter-input">
              <option value="">All</option>
              <option value="active">Active</option>
              <option value="deleted">Deleted</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">Loading...</span></div>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm users-table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Parent</th>
                <th class="d-none d-md-table-cell">Status</th>
                <th class="d-none d-md-table-cell">Created At</th>
                <th class="d-none d-md-table-cell">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" :class="['d-block d-md-table-row w-100 mb-2 mb-md-0', { 'table-danger': user.deleted_at }]">
                <td>
                  <div class="fw-bold">{{ user.name }}</div>
                  <div class="small text-muted">{{ user.parent_id }}</div>
                  <div class="d-md-none small mt-1">
                    <div>Status: <span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? 'Deleted' : 'Active' }}</span></div>
                    <div>Created: {{ new Date(user.created_at).toLocaleDateString() }}</div>
                    <div class="mt-1">
                      <div class="btn-group">
                        <button class="btn btn-sm btn-action btn-view me-1" @click="viewUser(user)"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-action btn-edit me-1" @click="editUser(user)" v-if="!user.deleted_at"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-trash"></i></button>
                        <button class="btn btn-sm btn-action btn-restore me-1" @click="restoreUser(user)" v-if="user.deleted_at"><i class="fas fa-undo"></i></button>
                        <button class="btn btn-sm btn-action btn-hard-delete" @click="confirmHardDelete(user)" v-if="user.deleted_at"><i class="fas fa-times"></i></button>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="d-none d-md-table-cell">{{ user.parent_id }}</td>
                <td class="d-none d-md-table-cell"><span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? 'Deleted' : 'Active' }}</span></td>
                <td class="d-none d-md-table-cell">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                <td class="d-none d-md-table-cell">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-action btn-view me-1" @click="viewUser(user)"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-action btn-edit me-1" @click="editUser(user)" v-if="!user.deleted_at"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-sm btn-action btn-restore me-1" @click="restoreUser(user)" v-if="user.deleted_at"><i class="fas fa-undo"></i></button>
                    <button class="btn btn-sm btn-action btn-hard-delete" @click="confirmHardDelete(user)" v-if="user.deleted_at"><i class="fas fa-times"></i></button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="userModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content user-modal-content">
          <div class="modal-header user-modal-header">
            <h5 class="modal-title user-modal-title">{{ isEditing ? 'Edit Category' : 'Add Category' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body user-modal-body">
            <form @submit.prevent="saveCategory">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-tag"></i></span>
                    <input id="categoryName" type="text" class="form-control" v-model="form.name" required placeholder="Category Name">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                    <input id="categoryOrder" type="number" class="form-control" v-model="form.order" placeholder="Display Order">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                    <select id="categoryParent" class="form-select" v-model="form.parent_id">
                      <option value="">No Parent</option>
                      <option v-for="cat in parentCategories" :key="cat.id" :value="cat.id">
                        {{ cat.name }}
                      </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-toggle-on"></i></span>
                    <select id="categoryStatus" class="form-select" v-model="form.status" required>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="col-12">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    <textarea id="categoryDescription" class="form-control" v-model="form.description" rows="3" placeholder="Category Description"></textarea>
                  </div>
                </div>
              </div>
              <div class="modal-footer user-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-submit">{{ isEditing ? 'Update' : 'Create' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- View User Modal -->
    <div class="modal fade" id="viewUserModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">User Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="user-details">
              <p><strong>Name:</strong> {{ selectedUser?.name }}</p>
              <p><strong>Email:</strong> {{ selectedUser?.email }}</p>
              <p><strong>Role:</strong> {{ selectedUser?.roles[0]?.name ? formatRoleName(selectedUser.roles[0].name) : 'No Role' }}</p>
              <p><strong>Status:</strong> {{ selectedUser?.deleted_at ? 'Deleted' : 'Active' }}</p>
              <p><strong>Created At:</strong> {{ new Date(selectedUser?.created_at).toLocaleString() }}</p>
              <p v-if="selectedUser?.deleted_at">
                <strong>Deleted At:</strong> {{ new Date(selectedUser?.deleted_at).toLocaleString() }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

export default {
  name: 'Users',
  setup() {
    const users = ref([])
    const availableRoles = ref([])
    const isEditing = ref(false)
    const selectedUser = ref(null)
    const userModal = ref(null)
    const viewUserModal = ref(null)
    const loading = ref(false)
    
    const form = ref({
      name: '',
      email: '',
      password: '',
      role: ''
    })

    const filters = ref({ search: '', role: '', status: '' })

    const fetchUsers = async () => {
      loading.value = true
      try {
        const params = {}
        if (filters.value.search) params.search = filters.value.search
        if (filters.value.role) params.role = filters.value.role
        if (filters.value.status) params.status = filters.value.status
        const response = await axios.get('/tenant/categories', { params })
        users.value = response.data.data.data
      } catch (error) {
        console.error('Error fetching categories:', error)
      } finally {
        loading.value = false
      }
    }

    const showCreateModal = () => {
      isEditing.value = false
      form.value = {
        name: '',
        email: '',
        password: '',
        role: ''
      }
      userModal.value.show()
    }

    const editUser = (user) => {
      isEditing.value = true
      form.value = {
        id: user.id,
        name: user.name,
        email: user.email,
        password: '',
        role: user.roles[0]?.name || ''
      }
      userModal.value.show()
    }

    const viewUser = (user) => {
      selectedUser.value = user
      viewUserModal.value.show()
    }

    const saveUser = async () => {
      try {
        if (isEditing.value) {
          await axios.put(`/tenant/categories/${form.value.id}`, form.value)
        } else {
          await axios.post('/tenant/categories', form.value)
        }
        
        await fetchUsers()
        userModal.value.hide()
        
        Swal.fire({
          icon: 'success',
          title: isEditing.value ? 'User Updated' : 'User Created',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error saving user:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to save user'
        })
      }
    }

    const confirmDelete = (user) => {
      Swal.fire({
        title: 'Are you sure?',
        text: "This will soft delete the user. They won't be able to login but their data will be preserved.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/categories/${user.id}`)
            await fetchUsers()
            Swal.fire({
              icon: 'success',
              title: 'User Deleted',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error deleting user:', error)
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.response?.data?.message || 'Failed to delete user'
            })
          }
        }
      })
    }

    const confirmHardDelete = (user) => {
      Swal.fire({
        title: 'Are you absolutely sure?',
        text: "This will permanently delete the user. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, permanently delete!'
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/categories/${user.id}/force`)
            await fetchUsers()
            Swal.fire({
              icon: 'success',
              title: 'User Permanently Deleted',
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error permanently deleting user:', error)
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: error.response?.data?.message || 'Failed to permanently delete user'
            })
          }
        }
      })
    }

    const restoreUser = async (user) => {
      try {
        await axios.post(`/tenant/categories/${user.id}/restore`)
        await fetchUsers()
        Swal.fire({
          icon: 'success',
          title: 'User Restored',
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error restoring user:', error)
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response?.data?.message || 'Failed to restore user'
        })
      }
    }

    function formatRoleName(name) {
      if (!name) return '';
      return name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    const applyFilters = () => {
      fetchUsers()
    }

    onMounted(() => {
      userModal.value = new Modal(document.getElementById('userModal'))
      viewUserModal.value = new Modal(document.getElementById('viewUserModal'))
      fetchUsers()
      fetchRoles()
      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new Tooltip(el);
      });
    })

    return {
      users,
      availableRoles,
      isEditing,
      selectedUser,
      form,
      showCreateModal,
      editUser,
      viewUser,
      saveUser,
      confirmDelete,
      confirmHardDelete,
      restoreUser,
      formatRoleName,
      filters,
      applyFilters,
      loading
    }
  }
}
</script>

<style scoped>
.users-page {
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

.btn-add-user {
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
.btn-add-user:hover, .btn-add-user:focus {
  background: #0d47a1;
  color: #fff;
  box-shadow: 0 4px 16px rgba(21,101,192,0.13);
}

.users-table {
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
  font-size: 15px;
  border-radius: 8px;
  overflow: hidden;
}
.users-table thead th {
  background: #fafbfc;
  position: sticky;
  top: 0;
  z-index: 2;
  font-weight: 500;
  color: #b71c1c;
}
.users-table tbody tr:nth-child(even) {
  background: #f7f7f7;
}
.users-table tbody tr:hover {
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

.user-details p {
  margin-bottom: 0.5rem;
}

.table td {
  vertical-align: middle;
}

.user-modal-content {
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
.user-modal-header {
  background: #c62828;
  color: #fff;
  border-radius: 0;
  border: none;
  padding: 1.2rem 2rem;
  position: relative;
}
.user-modal-title {
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 0.01em;
}
.user-modal-header .btn-close {
  filter: invert(1) grayscale(1) brightness(2);
  opacity: 1;
}
.user-modal-body {
  background: #fafbfc;
  padding: 2rem 2rem 1.5rem 2rem;
}
.user-modal-footer {
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

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  color: #6c757d;
}

.empty-state i {
  opacity: 0.5;
}

.empty-state h4 {
  margin: 1rem 0 0.5rem;
}

.empty-state p {
  max-width: 300px;
  margin: 0 auto;
}
</style>