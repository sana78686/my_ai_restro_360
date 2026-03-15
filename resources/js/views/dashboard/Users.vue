<template>
  <div class="users-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('users') }}</h4>
      <button class="btn btn-primary" @click="showCreateModal">
        <i class="fas fa-plus me-2"></i>{{ $t('common.create') }}
      </button>
    </div>

    <!-- Users Table -->
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-hover table-sm">
            <thead>
              <tr>
                <th>{{ $t('auth.register.restaurantName') || 'Name' }}</th>
                <th>{{ $t('auth.login.email') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('role') }}</th>
                <th class="d-none d-md-table-cell">{{ $t('status') || 'Status' }}</th>
                <th class="d-none d-md-table-cell">{{ $t('createdAt') || 'Created At' }}</th>
                <th class="d-none d-md-table-cell">{{ $t('common.actions') }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in users" :key="user.id" :class="['d-block d-md-table-row w-100 mb-2 mb-md-0', { 'table-danger': user.deleted_at }]">
                <td>
                  <div class="fw-bold">{{ user.name }}</div>
                  <div class="small text-muted">{{ user.email }}</div>
                  <div class="d-md-none small mt-1">
                    <div>{{ $t('role') }}: <span class="badge bg-info">{{ user.roles[0]?.name || $t('noRole') || 'No Role' }}</span></div>
                    <div>{{ $t('status') || 'Status' }}: <span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? $t('deleted') || 'Deleted' : $t('active') || 'Active' }}</span></div>
                    <div>{{ $t('created') || 'Created' }}: {{ new Date(user.created_at).toLocaleDateString() }}</div>
                    <div class="mt-1">
                      <div class="btn-group">
                        <button class="btn btn-sm btn-info me-1" @click="viewUser(user)"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-primary me-1" @click="editUser(user)" v-if="!user.deleted_at"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-trash"></i></button>
                        <button class="btn btn-sm btn-warning me-1" @click="restoreUser(user)" v-if="user.deleted_at"><i class="fas fa-undo"></i></button>
                        <button class="btn btn-sm btn-danger" @click="confirmHardDelete(user)" v-if="user.deleted_at"><i class="fas fa-times"></i></button>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="d-none d-md-table-cell">{{ user.email }}</td>
                <td class="d-none d-md-table-cell"><span class="badge bg-info">{{ user.roles[0]?.name || $t('noRole') || 'No Role' }}</span></td>
                <td class="d-none d-md-table-cell"><span class="badge" :class="user.deleted_at ? 'bg-danger' : 'bg-success'">{{ user.deleted_at ? $t('deleted') || 'Deleted' : $t('active') || 'Active' }}</span></td>
                <td class="d-none d-md-table-cell">{{ new Date(user.created_at).toLocaleDateString() }}</td>
                <td class="d-none d-md-table-cell">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-info me-1" @click="viewUser(user)"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-primary me-1" @click="editUser(user)" v-if="!user.deleted_at"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger me-1" @click="confirmDelete(user)" v-if="!user.deleted_at"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-sm btn-warning me-1" @click="restoreUser(user)" v-if="user.deleted_at"><i class="fas fa-undo"></i></button>
                    <button class="btn btn-sm btn-danger" @click="confirmHardDelete(user)" v-if="user.deleted_at"><i class="fas fa-times"></i></button>
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
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? ($t('common.edit') || 'Edit') + ' ' + ($t('users') || 'Users') : ($t('common.create') || 'Create') + ' ' + ($t('users') || 'Users') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveUser">
              <div class="mb-3">
                <label class="form-label">{{ $t('auth.register.restaurantName') || 'Name' }}</label>
                <input type="text" class="form-control" v-model="form.name" required>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('auth.login.email') }}</label>
                <input type="email" class="form-control" v-model="form.email" required>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('auth.login.password') }}</label>
                <input type="password" class="form-control" v-model="form.password" :required="!isEditing">
                <small class="text-muted" v-if="isEditing">{{ $t('usersLeaveBlank') || 'Leave blank to keep current password' }}</small>
              </div>
              <div class="mb-3">
                <label class="form-label">{{ $t('role') }}</label>
                <select class="form-select" v-model="form.role" required>
                  <option value="">{{ $t('selectRole') || 'Select Role' }}</option>
                  <option
                    v-for="role in availableRoles"
                    :key="role.id"
                    :value="role.name"
                  >
                    {{ role.name }}
                  </option>
                </select>
              </div>
              <div class="text-end">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">{{ $t('common.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ $t('common.save') }}</button>
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
            <h5 class="modal-title">{{ $t('userDetails') || 'User Details' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="user-details">
              <p><strong>{{ $t('name') || 'Name' }}:</strong> {{ selectedUser?.name }}</p>
              <p><strong>{{ $t('auth.login.email') }}:</strong> {{ selectedUser?.email }}</p>
              <p><strong>{{ $t('role') }}:</strong> {{ selectedUser?.roles[0]?.name || $t('noRole') || 'No Role' }}</p>
              <p><strong>{{ $t('status') || 'Status' }}:</strong> {{ selectedUser?.deleted_at ? ($t('deleted') || 'Deleted') : ($t('active') || 'Active') }}</p>
              <p><strong>{{ $t('createdAt') || 'Created At' }}:</strong> {{ new Date(selectedUser?.created_at).toLocaleString() }}</p>
              <p v-if="selectedUser?.deleted_at">
                <strong>Deleted At:</strong> {{ new Date(selectedUser?.deleted_at).toLocaleString() }}
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ $t('common.close') || 'Close' }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { Modal } from 'bootstrap'
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

    const form = ref({
      name: '',
      email: '',
      password: '',
      role: ''
    })

    const fetchUsers = async () => {
      try {
        const response = await axios.get('/dashboard/users')
        users.value = response.data.data.data
      } catch (error) {
        console.error('Error fetching users:', error)
      }
    }

    const fetchRoles = async () => {
      try {
        const response = await axios.get('/dashboard/roles')
        availableRoles.value = response.data.filter(role => role.name !== 'super_user')
      } catch (error) {
        console.error('Error fetching roles:', error)
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
          await axios.put(`/dashboard/users/${form.value.id}`, form.value)
        } else {
          await axios.post('/dashboard/users', form.value)
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
            await axios.delete(`/dashboard/users/${user.id}`)
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
            await axios.delete(`/dashboard/users/${user.id}/force`)
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
        await axios.post(`/dashboard/users/${user.id}/restore`)
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

    onMounted(() => {
      userModal.value = new Modal(document.getElementById('userModal'))
      viewUserModal.value = new Modal(document.getElementById('viewUserModal'))
      fetchUsers()
      fetchRoles()
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
      restoreUser
    }
  }
}
</script>

<style scoped>
.users-page {
  padding: 20px;
}

.user-details p {
  margin-bottom: 0.5rem;
}

.table td {
  vertical-align: middle;
}

.btn-group .btn {
  padding: 0.25rem 0.5rem;
}

.btn-group .btn i {
  font-size: 0.875rem;
}
</style>
