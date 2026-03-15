<template>
  <div class="users-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('tenant_dashboard.users.title') }}</h4>
      <button class="btn btn-add-user" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>{{ $t('tenant_dashboard.users.addUser') }}
      </button>
    </div>

    <!-- Filters -->
    <div class="filter-card mb-4">
      <div class="filter-body">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.users.search') }}</label>
            <input v-model="filters.search" @input="applyFilters" type="text" class="form-control filter-input" :placeholder="$t('tenant_dashboard.users.searchPlaceholder')">
          </div>
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.users.role') }}</label>
            <select v-model="filters.role" @change="applyFilters" class="form-select filter-input">
              <option value="">{{ $t('tenant_dashboard.users.allRoles') }}</option>
              <option v-for="role in availableRoles" :key="role.id" :value="role.name">{{ formatRoleName(role.name) }}</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label mb-1">{{ $t('tenant_dashboard.users.status') }}</label>
            <select v-model="filters.status" @change="applyFilters" class="form-select filter-input">
              <option value="">{{ $t('tenant_dashboard.users.all') }}</option>
              <option value="active">{{ $t('tenant_dashboard.users.statusActive') }}</option>
              <option value="deleted">{{ $t('tenant_dashboard.users.statusDeleted') }}</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status"><span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span></div>
        </div>
        <div v-else-if="users.length === 0" class="text-center py-5">
          <p>{{ $t('tenant_dashboard.users.noUsersFound') }}</p>
        </div>
        <div v-else class="table-responsive">
          <table class="table table-hover table-sm users-table">
            <thead class="d-none d-md-table-header-group">
              <tr>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.name') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.email') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.role') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.status') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.locationStatus') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.createdAt') }}</th>
                <th>{{ $t('tenant_dashboard.users.tableHeaders.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" :class="{'table-danger': user.deleted_at}">
                <!-- Mobile View -->
                <td class="d-md-none">
                  <div class="fw-bold">{{ user.name }}</div>
                  <div class="small text-muted">{{ user.email }}</div>
                  <div class="small mt-1">
                    <div>{{ $t('tenant_dashboard.users.role') }}: <span class="badge bg-info">{{ user.roles[0]?.name ? formatRoleName(user.roles[0].name) : $t('tenant_dashboard.users.noRole') }}</span></div>
                    <div>{{ $t('tenant_dashboard.users.status') }}: <span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? $t('tenant_dashboard.users.statusDeleted') : $t('tenant_dashboard.users.statusActive') }}</span></div>
                    <div>{{ $t('tenant_dashboard.users.locationStatus') }}: 
                      <span class="badge" :class="user.is_in_range ? 'bg-success' : 'bg-danger'">
                        {{ user.is_in_range ? 'In Range' : 'Out of Range' }}
                      </span>
                    </div>
                    <div>{{ $t('tenant_dashboard.users.created') }}: {{ formatDate(user.created_at) }}</div>
                    <div class="mt-2">
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
                
                <!-- Desktop View -->
                <td class="d-none d-md-table-cell">{{ user.name }}</td>
                <td class="d-none d-md-table-cell">{{ user.email }}</td>
                <td class="d-none d-md-table-cell"><span class="badge bg-info">{{ user.roles[0]?.name ? formatRoleName(user.roles[0].name) : $t('tenant_dashboard.users.noRole') }}</span></td>
                <td class="d-none d-md-table-cell"><span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? $t('tenant_dashboard.users.statusDeleted') : $t('tenant_dashboard.users.statusActive') }}</span></td>
                <td class="d-none d-md-table-cell">
                  <span class="badge" :class="user.is_in_range ? 'bg-success' : 'bg-danger'">
                    {{ user.is_in_range ? 'In Range' : 'Out of Range' }}
                  </span>
                </td>
                <td class="d-none d-md-table-cell">{{ formatDate(user.created_at) }}</td>
                <td class="d-none d-md-table-cell">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-action btn-view me-1" @click="viewUser(user)"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-action btn-edit me-1" @click="editUser(user)" v-if="!user.deleted_at"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-action btn-delete me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-trash"></i></button>
                    <!-- <button class="btn btn-sm btn-action btn-info me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-envelope"></i></button> -->
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
            <h5 class="modal-title user-modal-title">{{ isEditing ? $t('tenant_dashboard.users.editUser') : $t('tenant_dashboard.users.addUser') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body user-modal-body">
            <form @submit.prevent="saveUser">
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input id="userName" type="text" class="form-control" v-model="form.name" required :placeholder="$t('tenant_dashboard.users.namePlaceholder')" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.users.nameTooltip')">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input id="userEmail" type="email" class="form-control" v-model="form.email" required placeholder="user@email.com" data-bs-toggle="tooltip" :title="$t('tenant_dashboard.users.emailTooltip')">
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                    <input id="userPassword" type="password" class="form-control" v-model="form.password" :required="!isEditing" :placeholder="isEditing ? $t('tenant_dashboard.users.leaveBlankToKeepPassword') : $t('tenant_dashboard.users.passwordMin8')" data-bs-toggle="tooltip" :title="isEditing ? $t('tenant_dashboard.users.leaveBlankToKeepPassword') : $t('tenant_dashboard.users.passwordMin8')">
                  </div>
                  <small class="text-muted" v-if="isEditing">{{ $t('tenant_dashboard.users.leaveBlankToKeepPassword') }}</small>
                </div>
                <div class="col-md-6">
                  <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                    <select id="userRole" class="form-select" v-model="form.role" required data-bs-toggle="tooltip" :title="$t('tenant_dashboard.users.roleTooltip')">
                      <option value="">{{ $t('tenant_dashboard.users.selectRole') }}</option>
                      <option v-for="role in availableRoles" :key="role.id" :value="role.name">
                        {{ formatRoleName(role.name) }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer user-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.users.cancel') }}</button>
                <button type="submit" class="btn btn-submit">{{ isEditing ? $t('tenant_dashboard.users.update') : $t('tenant_dashboard.users.create') }}</button>
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
            <h5 class="modal-title">{{ $t('tenant_dashboard.users.userDetails') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="user-details" v-if="selectedUser">
              <p><strong>{{ $t('tenant_dashboard.users.name') }}:</strong> {{ selectedUser.name }}</p>
              <p><strong>{{ $t('tenant_dashboard.users.email') }}:</strong> {{ selectedUser.email }}</p>
              <p><strong>{{ $t('tenant_dashboard.users.role') }}:</strong> {{ selectedUser.roles[0]?.name ? formatRoleName(selectedUser.roles[0].name) : $t('tenant_dashboard.users.noRole') }}</p>
              <p><strong>{{ $t('tenant_dashboard.users.status') }}:</strong> {{ selectedUser.deleted_at ? $t('tenant_dashboard.users.statusDeleted') : $t('tenant_dashboard.users.statusActive') }}</p>
              <p><strong>{{ $t('tenant_dashboard.users.locationStatus') }}:</strong> 
                <span class="badge" :class="selectedUser.is_active ? 'bg-success' : 'bg-danger'">
                  {{ selectedUser.is_active ? 'In Range' : 'Out of Range' }}
                </span>
              </p>
              <!-- <p v-if="selectedUser.latitude && selectedUser.longitude">
                <strong>{{ $t('tenant_dashboard.users.coordinates') }}:</strong> 
                {{ selectedUser.latitude }}, {{ selectedUser.longitude }}
              </p> -->
              <p><strong>{{ $t('tenant_dashboard.users.createdAt') }}:</strong> {{ formatDate(selectedUser.created_at, true) }}</p>
              <p v-if="selectedUser.deleted_at">
                <strong>{{ $t('tenant_dashboard.users.deletedAt') }}:</strong> {{ formatDate(selectedUser.deleted_at, true) }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('tenant_dashboard.users.close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, getCurrentInstance } from 'vue'
import { Modal, Tooltip } from 'bootstrap'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'Users',
  setup() {
    const instance = getCurrentInstance()
    const { $t } = instance.appContext.config.globalProperties
    
    const users = ref([])
    const availableRoles = ref([])
    const isEditing = ref(false)
    const selectedUser = ref(null)
    const userModal = ref(null)
    const viewUserModal = ref(null)
    const loading = ref(false)

    const form = ref({
      id: null,
      name: '',
      email: '',
      password: '',
      role: ''
    })

    const filters = ref({ 
      search: '', 
      role: '', 
      status: '' 
    })

    const fetchUsers = async () => {
      loading.value = true
      try {
        const params = {}
        if (filters.value.search) params.search = filters.value.search
        if (filters.value.role) params.role = filters.value.role
        if (filters.value.status) params.status = filters.value.status
        
        const response = await axios.get('/tenant/users', { params })
        users.value = response.data.data?.data || response.data.data || []
      } catch (error) {
        console.error('Error fetching users:', error)
        Swal.fire({
          icon: 'error',
          title: $t('tenant_dashboard.users.error'),
          text: error.response?.data?.message || $t('tenant_dashboard.users.failedToFetchUsers')
        })
      } finally {
        loading.value = false
      }
    }

    const fetchRoles = async () => {
      try {
        const response = await axios.get('/tenant/roles')
        availableRoles.value = response.data.data?.filter(role => role.name !== 'super_user') || []
      } catch (error) {
        console.error('Error fetching roles:', error)
      }
    }

    const showCreateModal = () => {
      isEditing.value = false
      form.value = {
        id: null,
        name: '',
        email: '',
        password: '',
        role: ''
      }
      userModal.value.show()
    }

    const editUser = (user) => {
      isEditing.value = true
      selectedUser.value = user
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
        const payload = { ...form.value }
        // Remove id from payload for create
        if (!isEditing.value) {
          delete payload.id
        }

        if (isEditing.value) {
          await axios.put(`/tenant/users/${form.value.id}`, payload)
        } else {
          await axios.post('/tenant/users', payload)
        }

        await fetchUsers()
        userModal.value.hide()

        Swal.fire({
          icon: 'success',
          title: isEditing.value ? $t('tenant_dashboard.users.userUpdated') : $t('tenant_dashboard.users.userCreated'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error saving user:', error)
        Swal.fire({
          icon: 'error',
          title: $t('tenant_dashboard.users.error'),
          text: error.response?.data?.message || $t('tenant_dashboard.users.failedToSaveUser')
        })
      }
    }

    const confirmDelete = (user) => {
      Swal.fire({
        title: $t('tenant_dashboard.users.areYouSure'),
        text: $t('tenant_dashboard.users.softDeleteWarning'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: $t('tenant_dashboard.users.yesDeleteIt')
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/users/${user.id}`)
            await fetchUsers()
            Swal.fire({
              icon: 'success',
              title: $t('tenant_dashboard.users.userDeleted'),
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error deleting user:', error)
            Swal.fire({
              icon: 'error',
              title: $t('tenant_dashboard.users.error'),
              text: error.response?.data?.message || $t('tenant_dashboard.users.failedToDeleteUser')
            })
          }
        }
      })
    }

    const confirmHardDelete = (user) => {
      Swal.fire({
        title: $t('tenant_dashboard.users.areYouAbsolutelySure'),
        text: $t('tenant_dashboard.users.hardDeleteWarning'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: $t('tenant_dashboard.users.yesPermanentlyDelete')
      }).then(async (result) => {
        if (result.isConfirmed) {
          try {
            await axios.delete(`/tenant/users/${user.id}/force`)
            await fetchUsers()
            Swal.fire({
              icon: 'success',
              title: $t('tenant_dashboard.users.userPermanentlyDeleted'),
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 3000
            })
          } catch (error) {
            console.error('Error permanently deleting user:', error)
            Swal.fire({
              icon: 'error',
              title: $t('tenant_dashboard.users.error'),
              text: error.response?.data?.message || $t('tenant_dashboard.users.failedToPermanentlyDeleteUser')
            })
          }
        }
      })
    }

    const restoreUser = async (user) => {
      try {
        await axios.post(`/tenant/users/${user.id}/restore`)
        await fetchUsers()
        Swal.fire({
          icon: 'success',
          title: $t('tenant_dashboard.users.userRestored'),
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000
        })
      } catch (error) {
        console.error('Error restoring user:', error)
        Swal.fire({
          icon: 'error',
          title: $t('tenant_dashboard.users.error'),
          text: error.response?.data?.message || $t('tenant_dashboard.users.failedToRestoreUser')
        })
      }
    }

    const formatRoleName = (name) => {
      if (!name) return ''
      return name.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
    }

    const formatDate = (dateString, includeTime = false) => {
      if (!dateString) return ''
      const date = new Date(dateString)
      return includeTime ? date.toLocaleString() : date.toLocaleDateString()
    }

    const applyFilters = () => {
      fetchUsers()
    }

    const checkLocation = () => {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          async (pos) => {
            try {
              await axios.get('/tenant/check/location', {
                params: {
                  latitude: pos.coords.latitude,
                  longitude: pos.coords.longitude,
                }
              })
              // Location checked successfully, continue with normal flow
            } catch (err) {
              console.error('Location API error:', err)
            }
          },
          (err) => {
            console.error('Geolocation error:', err)
          }
        )
      }
    }

    onMounted(() => {
      userModal.value = new Modal(document.getElementById('userModal'))
      viewUserModal.value = new Modal(document.getElementById('viewUserModal'))
      
      fetchUsers()
      fetchRoles()
      checkLocation()
      
      // Initialize tooltips
      setTimeout(() => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
          new Tooltip(el)
        })
      }, 100)
    })

    return {
      users,
      availableRoles,
      isEditing,
      selectedUser,
      form,
      filters,
      loading,
      showCreateModal,
      editUser,
      viewUser,
      saveUser,
      confirmDelete,
      confirmHardDelete,
      restoreUser,
      formatRoleName,
      formatDate,
      applyFilters
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
.btn-info { background: #b4d2ff63; color: #1fbeef; }
.btn-delete { background: #ffebee; color: #c62828; }
.btn-delete:hover { background: #ffcdd2; color: #b71c1c; }
.btn-info:hover { background: #b4d2fff1; color: #1c93b7; }
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

/* Mobile responsive fixes */
@media (max-width: 768px) {
  .users-table tbody tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
  }
  
  .users-table tbody tr td.d-md-none {
    display: block !important;
    padding: 1rem;
  }
  
  .d-md-table-header-group {
    display: none !important;
  }
}
</style>