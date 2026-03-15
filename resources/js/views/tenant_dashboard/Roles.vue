<template>
  <div class="roles-management">
    <div class="container-fluid">
      <div class="row mb-4">
        <div class="col">
          <h2>{{ $t('tenant_dashboard.roles.title') }}</h2>
          <p class="text-muted">{{ $t('tenant_dashboard.roles.description') }}</p>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" @click="openAddRoleModal">
            <i class="fas fa-plus me-2"></i>{{ $t('tenant_dashboard.roles.addNew') }}
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center my-5">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span>
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
          <h3>{{ $t('tenant_dashboard.roles.noRolesFound') }}</h3>
          <p class="text-muted">{{ $t('tenant_dashboard.roles.noRolesMessage') }}</p>
          <button class="btn btn-primary mt-3" @click="openAddRoleModal">
            <i class="fas fa-plus me-2"></i>{{ $t('tenant_dashboard.roles.addFirstRole') }}
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
                  <th>{{ $t('tenant_dashboard.roles.tableHeaders.name') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.roles.tableHeaders.description') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.roles.tableHeaders.users') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.roles.tableHeaders.permissions') }}</th>
                  <th class="d-none d-md-table-cell">{{ $t('tenant_dashboard.roles.tableHeaders.actions') }}</th>
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
                      {{ $t('tenant_dashboard.roles.users') }}: {{ role.users_count || 0 }}<br>
                      <span v-if="role.permissions && role.permissions.length">{{ $t('tenant_dashboard.roles.permissions') }}: {{ role.permissions.slice(0,3).map(p=>p.name).join(', ') }}<span v-if="role.permissions.length > 3"> +{{ role.permissions.length-3 }} {{ $t('tenant_dashboard.roles.more') }}</span></span>
                    </div>
                  </td>
                  <td class="d-none d-md-table-cell">{{ role.description || '-' }}</td>
                  <td class="d-none d-md-table-cell">{{ role.users_count || 0 }}</td>
                  <td class="d-none d-md-table-cell">
                    <div class="permissions-badges">
                      <span v-for="permission in (role.permissions || []).slice(0, 3)" :key="permission.id" class="badge bg-info me-1">{{ permission.name }}</span>
                      <span v-if="role.permissions && role.permissions.length > 3" class="badge bg-secondary">+{{ role.permissions.length - 3 }} {{ $t('tenant_dashboard.roles.more') }}</span>
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
        <div class="modal-content role-modal-content">
          <div class="modal-header role-modal-header">
            <h5 class="modal-title role-modal-title">{{ isEditing ? $t('tenant_dashboard.roles.editRole') : $t('tenant_dashboard.roles.addNewRole') }}</h5>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>
          <div class="modal-body role-modal-body">
            <form @submit.prevent="saveRole">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                    <input type="text" class="form-control" v-model="roleForm.name" :placeholder="$t('tenant_dashboard.roles.form.namePlaceholder')" required data-bs-toggle="tooltip" :title="$t('tenant_dashboard.roles.form.nameTooltip')">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                    <input type="text" class="form-control" v-model="roleForm.description" :placeholder="$t('tenant_dashboard.roles.form.descriptionPlaceholder')" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.roles.form.descriptionTooltip')">
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label mb-2">
                  {{ $t('tenant_dashboard.roles.form.permissions') }}
                  <i class="fas fa-info-circle ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.roles.form.permissionsTooltip')"></i>
                </label>
                <div class="mb-2">
                  <input type="checkbox" id="selectAllPermissions" v-model="selectAll" @change="toggleSelectAll">
                  <label for="selectAllPermissions" class="ms-1" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.roles.form.selectAllTooltip')">{{ $t('tenant_dashboard.roles.form.selectAll') }}</label>
                </div>
                <div v-if="!permissions || permissions.length === 0" class="alert alert-warning">
                  {{ $t('tenant_dashboard.roles.noPermissionsFound') }}
                </div>
                <div v-else class="permissions-grid role-permissions-grid">
                  <div v-for="permission in permissions" :key="permission.id" class="form-check">
                    <input type="checkbox" class="form-check-input" :id="'permission-' + permission.id" :value="permission.id" v-model="roleForm.permissions" :title="'Allow ' + formatPermissionName(permission.name)" data-bs-toggle="tooltip">
                    <label class="form-check-label" :for="'permission-' + permission.id">
                      {{ formatPermissionName(permission.name) }}
                    </label>
                  </div>
                </div>
              </div>
              <div class="modal-footer role-modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeModal">
                  {{ $t('tenant_dashboard.roles.cancel') }}
                </button>
                <button type="submit" class="btn btn-submit" :disabled="loading">
                  <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                  {{ isEditing ? $t('tenant_dashboard.roles.update') : $t('tenant_dashboard.roles.create') }}
                </button>
              </div>
            </form>
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
    const selectAll = ref(false)

    const roleForm = ref({
      name: '',
      description: '',
      permissions: []
    })

    const fetchRoles = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await axios.get('/tenant/roles')
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
        const response = await axios.get('/tenant/permissions')
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
          text: err.response?.data?.message || $t('tenant_dashboard.roles.messages.loadPermissionsFailed')
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
          ? `/tenant/roles/${editingId.value}`
          : '/tenant/roles'
        const method = isEditing.value ? 'put' : 'post'

        const response = await axios[method](url, roleForm.value)
        console.log('Save role response:', response.data)

        await Swal.fire({
          icon: 'success',
          title: 'Success',
          text: isEditing.value ? $t('tenant_dashboard.roles.messages.roleUpdated') : $t('tenant_dashboard.roles.messages.roleCreated'),
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
            text: err.response?.data?.message || $t('tenant_dashboard.roles.messages.saveFailed')
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
        title: $t('dashboard_common.confirm'),
        text: $t('tenant_dashboard.roles.messages.deleteWarning'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: $t('dashboard_common.yes')
      })

      if (result.isConfirmed) {
        try {
          loading.value = true
          error.value = null
          await axios.delete(`/tenant/roles/${role.id}`)

          await Swal.fire({
            icon: 'success',
            title: 'Success',
            text: $t('tenant_dashboard.roles.messages.roleDeleted'),
            timer: 1500,
            showConfirmButton: false
          })

          await fetchRoles()
        } catch (err) {
          console.error('Error deleting role:', err)
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: err.response?.data?.message || $t('tenant_dashboard.roles.messages.deleteFailed')
          })
        } finally {
          loading.value = false
        }
      }
    }

    function toggleSelectAll() {
      if (selectAll.value) {
        roleForm.value.permissions = permissions.value.map(p => p.id)
      } else {
        roleForm.value.permissions = []
      }
    }

    function formatPermissionName(name) {
      if (!name) return '';
      return name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    onMounted(async () => {
      try {
        await Promise.all([fetchRoles(), fetchPermissions()])
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          new window.bootstrap.Tooltip(el)
        })
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
      deleteRole,
      selectAll,
      toggleSelectAll,
      formatPermissionName
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

.role-modal-content {
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
.role-modal-header {
  background: #c62828;
  color: #fff;
  border-radius: 0;
  border: none;
  padding: 1.2rem 2rem;
  position: relative;
}
.role-modal-title {
  font-size: 1.25rem;
  font-weight: 400;
  letter-spacing: 0.01em;
}
.role-modal-header .btn-close {
  filter: invert(1) grayscale(1) brightness(2);
  opacity: 1;
}
.role-modal-body {
  background: #fafbfc;
  padding: 2rem 2rem 1.5rem 2rem;
}
.role-modal-footer {
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
.permissions-grid.role-permissions-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.3rem 1.2rem;
  padding: 0.5rem 0.5rem 0.2rem 0.5rem;
}
</style>
