<template>
  <div class="roles-management">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col">
          <h2>{{ $t('roles_module.title') }}</h2>
          <p class="text-muted">{{ $t('roles_module.description') }}</p>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" @click="openAddRoleModal">
            <i class="fas fa-plus me-2"></i>{{ $t('roles_module.addNew') }}
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ $t('common.loading') }}</span>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="alert alert-danger" role="alert">
        {{ error }}
      </div>

      <!-- Empty State -->
      <div v-else-if="!roles || roles.length === 0" class="text-center my-5">
        <div class="empty-state">
          <i class="fas fa-store fa-4x text-muted mb-4"></i>
          <h3>No Roles Found</h3>
          <p class="text-muted">There are no roles registered yet.</p>
          <button class="btn btn-primary mt-3" @click="openAddRoleModal">
            <i class="fas fa-plus me-2"></i>Add First Role
          </button>
        </div>
      </div>

      <!-- Roles List -->
      <div v-else class="card">
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover table-sm">
              <thead>
                <tr>
                  <th>{{ $t('roles_module.columns.name') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('common.description') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('common.usersCount') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('roles_module.columns.permissions') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('common.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="role in roles" :key="role.id" class="d-block d-md-table-row w-100 mb-2 mb-md-0">
                  <td>
                    <div class="fw-bold">{{ role.name }}</div>
                    <div class="d-md-none small text-muted">
                      <span v-if="role.description">{{ role.description }}</span>
                      <span v-else>-</span>
                      <br>
                      Users: {{ role.users_count || 0 }}<br>
                      <span v-if="role.permissions && role.permissions.length">Permissions: {{ role.permissions.slice(0,3).map(p=>p.name).join(', ') }}<span v-if="role.permissions.length > 3"> +{{ role.permissions.length-3 }} more</span></span>
                    </div>
                  </td>
                  <td class="d-none d-md-table-cell">{{ role.description || '-' }}</td>
                  <td class="d-none d-md-table-cell">{{ role.users_count || 0 }}</td>
                  <td class="d-none d-md-table-cell">
                    <div class="permissions-badges">
                      <span v-for="permission in (role.permissions || []).slice(0, 3)" :key="permission.id" class="badge bg-info me-1">{{ permission.name }}</span>
                      <span v-if="role.permissions && role.permissions.length > 3" class="badge bg-secondary">+{{ role.permissions.length - 3 }} more</span>
                    </div>
                  </td>
                  <td class="d-none d-md-table-cell">
                    <div class="btn-group">
                      <button class="btn btn-sm btn-outline-primary" @click="editRole(role)"><i class="fas fa-edit"></i></button>
                      <button class="btn btn-sm btn-outline-danger" @click="deleteRole(role)" :disabled="role.name === 'super_admin'"><i class="fas fa-trash"></i></button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Role Modal -->
    <div class="modal fade" id="roleModal" tabindex="-1" data-bs-backdrop="static">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? $t('roles_module.editRole') : $t('roles_module.addRole') }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveRole">
              <div class="mb-3">
                <label class="form-label">{{ $t('roles_module.form.name') }}</label>
                <input
                  type="text"
                  class="form-control"
                  v-model="roleForm.name"
                  :class="{ 'is-invalid': errors.name }"
                >
                <div class="invalid-feedback" v-if="errors.name">
                  {{ errors.name[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ $t('common.description') }}</label>
                <textarea
                  class="form-control"
                  v-model="roleForm.description"
                  :class="{ 'is-invalid': errors.description }"
                  rows="3"
                ></textarea>
                <div class="invalid-feedback" v-if="errors.description">
                  {{ errors.description[0] }}
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label">{{ $t('roles_module.form.permissions') }}</label>
                <div v-if="!permissions || permissions.length === 0" class="alert alert-warning">
                  {{ $t('roles_module.noPermissionsFound') }}
                </div>
                <div v-else class="permissions-grid">
                  <div
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="form-check"
                  >
                    <input
                      type="checkbox"
                      class="form-check-input"
                      :id="'permission-' + permission.id"
                      :value="permission.id"
                      v-model="roleForm.permissions"
                    >
                    <label
                      class="form-check-label"
                      :for="'permission-' + permission.id"
                    >
                      {{ permission.name }}
                    </label>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">
              {{ $t('common.cancel') }}
            </button>
            <button type="button" class="btn btn-primary" @click="saveRole" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
              {{ isEditing ? $t('common.update') : $t('common.create') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { Modal } from 'bootstrap'

export default {
  name: 'Roles',
  setup() {
    const roles = ref([])
    const permissions = ref([])
    const loading = ref(true)
    const error = ref(null)
    const isEditing = ref(false)
    const editingId = ref(null)
    const modalInstance = ref(null)
    const errors = ref({})

    const roleForm = ref({
      name: '',
      description: '',
      permissions: []
    })

    const fetchRoles = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await axios.get('/dashboard/roles')
        console.log('Roles response:', response.data)
        if (response.data && response.data.data) {
          roles.value = response.data.data
        } else {
          roles.value = []
        }
      } catch (err) {
        console.error('Error fetching roles:', err)
        error.value = err.response?.data?.message || 'Failed to load roles'
        roles.value = []
      } finally {
        loading.value = false
      }
    }

    const fetchPermissions = async () => {
      try {
        const response = await axios.get('/dashboard/permissions')
        console.log('Permissions response:', response.data)
        if (response.data && response.data.data) {
          permissions.value = response.data.data
        } else {
          permissions.value = []
        }
      } catch (err) {
        console.error('Error fetching permissions:', err)
        permissions.value = []
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: err.response?.data?.message || 'Failed to load permissions'
        })
      }
    }

    const openAddRoleModal = () => {
      isEditing.value = false
      editingId.value = null
      roleForm.value = {
        name: '',
        description: '',
        permissions: []
      }
      errors.value = {}
      const modalEl = document.getElementById('roleModal')
      if (modalEl) {
        modalInstance.value = new Modal(modalEl)
        modalInstance.value.show()
      }
    }

    const editRole = (role) => {
      isEditing.value = true
      editingId.value = role.id
      roleForm.value = {
        name: role.name,
        description: role.description || '',
        permissions: (role.permissions || []).map(p => p.id)
      }
      errors.value = {}
      const modalEl = document.getElementById('roleModal')
      if (modalEl) {
        modalInstance.value = new Modal(modalEl)
        modalInstance.value.show()
      }
    }

    const closeModal = () => {
      if (modalInstance.value) {
        modalInstance.value.hide()
      }
      errors.value = {}
    }

    const saveRole = async () => {
      try {
        loading.value = true
        errors.value = {}
        error.value = null

        const url = isEditing.value
          ? `/dashboard/roles/${editingId.value}`
          : '/dashboard/roles'
        const method = isEditing.value ? 'put' : 'post'

        const response = await axios[method](url, roleForm.value)
        console.log('Save role response:', response.data)

        await Swal.fire({
          icon: 'success',
          title: 'Success',
          text: isEditing.value ? 'Role updated successfully' : 'Role created successfully',
          timer: 1500,
          showConfirmButton: false
        })

        closeModal()
        await fetchRoles()
      } catch (err) {
        console.error('Error saving role:', err)
        if (err.response?.data?.errors) {
          errors.value = err.response.data.errors
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.response?.data?.message || 'Failed to save role'
          })
        }
      } finally {
        loading.value = false
      }
    }

    const deleteRole = async (role) => {
      if (role.name === 'super_admin') {
        return
      }

      const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      })

      if (result.isConfirmed) {
        try {
          loading.value = true
          error.value = null
          await axios.delete(`/dashboard/roles/${role.id}`)

          await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Role deleted successfully',
            timer: 1500,
            showConfirmButton: false
          })

          await fetchRoles()
        } catch (err) {
          console.error('Error deleting role:', err)
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.response?.data?.message || 'Failed to delete role'
          })
        } finally {
          loading.value = false
        }
      }
    }

    onMounted(async () => {
      try {
        await Promise.all([fetchRoles(), fetchPermissions()])
      } catch (err) {
        console.error('Error during initialization:', err)
      }
    })

    return {
      roles,
      permissions,
      loading,
      error,
      isEditing,
      errors,
      roleForm,
      openAddRoleModal,
      editRole,
      closeModal,
      saveRole,
      deleteRole
    }
  }
}
</script>

<style scoped>
.roles-management {
  padding: 2rem;
}

.empty-state {
  padding: 3rem 1rem;
}

.empty-state i {
  color: #e9ecef;
}

.empty-state h3 {
  margin: 1rem 0;
  color: #343a40;
}

.card {
  border: none;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge {
  font-size: 0.85rem;
  padding: 0.35em 0.65em;
}

.btn-group .btn {
  margin: 0 0.2rem;
}

.table th {
  font-weight: 600;
  color: #2c3e50;
}

.table td {
  vertical-align: middle;
}

.permissions-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 0.25rem;
}

.permissions-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1rem;
  max-height: 300px;
  overflow-y: auto;
  padding: 1rem;
  border: 1px solid #dee2e6;
  border-radius: 0.25rem;
}

.form-check {
  margin-bottom: 0.5rem;
}

.form-check-label {
  font-size: 0.9rem;
  color: #495057;
  cursor: pointer;
}

.form-check-input:checked {
  background-color: #0d6efd;
  border-color: #0d6efd;
}
</style>
