<template>
  <div class="user-profile-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('user_profile.title') }}</h4>
    </div>

    <!-- Profile Card -->
    <div class="card">
      <div class="card-body">
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-danger" role="status">
            <span class="visually-hidden">{{ $t('dashboard_common.loading') }}</span>
          </div>
        </div>

        <div v-else>
          <!-- Success Message -->
          <div v-if="success" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ success }}
            <button type="button" class="btn-close" @click="success = ''"></button>
          </div>

          <!-- Personal Information Form -->
          <div class="row">
            <div class="col-lg-8">
              <h5 class="mb-4">{{ $t('user_profile.personal_info') }}</h5>
              
              <form @submit.prevent="updateProfile">
                <div class="row">
                  <!-- <div class="col-md-6">
                    <div class="mb-3">
                      <label for="first_name" class="form-label">{{ $t('user_profile.first_name') }}</label>
                      <input 
                        v-model="profileForm.first_name" 
                        type="text" 
                        class="form-control filter-input" 
                        id="first_name"
                        :placeholder="$t('user_profile.first_name_placeholder')"
                        required
                      >
                    </div>
                  </div> -->
                  <!-- <div class="col-md-6"> -->
                    <div class="mb-3">
                      <label for="name" class="form-label">{{ $t('user_profile.name') }}</label>
                      <input 
                        v-model="profileForm.name" 
                        type="text" 
                        class="form-control filter-input" 
                        id="name"
                        :placeholder="$t('user_profile.name_placeholder')"
                        required
                      >
                    </div>
                  </div>
                <!-- </div> -->

                <div class="mb-3">
                  <label for="email" class="form-label">{{ $t('user_profile.email') }}</label>
                  <input 
                    v-model="profileForm.email" 
                    type="email" 
                    class="form-control filter-input" 
                    id="email"
                    :placeholder="$t('user_profile.email_placeholder')"
                    required
                  >
                </div>

                <div class="mb-3">
                  <label for="phone" class="form-label">{{ $t('user_profile.phone') }}</label>
                  <input 
                    v-model="profileForm.phone" 
                    type="tel" 
                    class="form-control filter-input" 
                    id="phone"
                    :placeholder="$t('user_profile.phone_placeholder')"
                  >
                </div>

                <div class="mb-4">
                  <label for="address" class="form-label">{{ $t('user_profile.address') }}</label>
                  <textarea 
                    v-model="profileForm.address" 
                    class="form-control filter-input" 
                    id="address"
                    :placeholder="$t('user_profile.address_placeholder')"
                    rows="3"
                  ></textarea>
                </div>

                <div class="d-flex justify-content-end">
                  <button 
                    type="submit" 
                    class="btn btn-primary" 
                    :disabled="updateLoading"
                  >
                    <span v-if="updateLoading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ $t('user_profile.update_button') }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Sidebar - Additional Options -->
            <div class="col-lg-4">
              <div class="border-start ps-4">
                <!-- Change Password Card -->
                <!-- <div class="card mb-4">
                  <div class="card-body">
                    <h6 class="card-title mb-3">
                      <i class="fas fa-key me-2 text-muted"></i>
                      {{ $t('user_profile.change_password') }}
                    </h6>
                    <p class="text-muted small mb-3">{{ $t('user_profile.password_description') }}</p>
                    <button 
                      class="btn btn-outline-primary btn-sm w-100"
                      @click="$router.push('/reset-password')"
                    >
                      {{ $t('user_profile.change_password_button') }}
                    </button>
                  </div>
                </div> -->

                <!-- Account Status -->
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title mb-3">
                      <i class="fas fa-user me-2 text-muted"></i>
                      {{ $t('user_profile.account_status') }}
                    </h6>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="text-muted">{{ $t('user_profile.member_since') }}</span>
                      <span class="fw-bold">{{ memberSince }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <span class="text-muted">{{ $t('user_profile.status') }}</span>
                      <span class="badge bg-success">{{ $t('user_profile.active') }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useI18n } from 'vue-i18n'

const { t: $t } = useI18n()
const router = useRouter()

const loading = ref(true)
const updateLoading = ref(false)
const success = ref('')
const profileForm = ref({
  // first_name: '',
  name: '',
  email: '',
  phone: '',
  address: ''
})

const userData = ref({})

// Fetch user profile data
const fetchUserProfile = async () => {
  loading.value = true
  try {
    const response = await axios.get('/tenant/profile')
    if (response.data.success) {
      userData.value = response.data.data
      // Populate form with user data
      profileForm.value = {
        // first_name: userData.value.first_name || '',
        name: userData.value.name || '',
        email: userData.value.email || '',
        phone: userData.value.phone || '',
        address: userData.value.address || ''
      }
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: $t('user_profile.fetch_error')
    })
  } finally {
    loading.value = false
  }
}

// Update user profile
const updateProfile = async () => {
  updateLoading.value = true
  try {
    const response = await axios.put('/tenant/profile', profileForm.value)
    if (response.data.success) {
      success.value = $t('user_profile.update_success')
      userData.value = { ...userData.value, ...profileForm.value }
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: response.data.message || $t('user_profile.update_error')
      })
    }
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: $t('user_profile.update_error')
    })
  } finally {
    updateLoading.value = false
  }
}

// Format member since date
const memberSince = computed(() => {
  if (!userData.value.created_at) return ''
  return new Date(userData.value.created_at).toLocaleDateString()
})

onMounted(() => {
  fetchUserProfile()
})
</script>

<style scoped>
.user-profile-page {
  padding: 20px;
  font-family: 'Inter', 'Roboto', 'Segoe UI', Arial, sans-serif;
  font-size: 16px;
}

.card {
  border-radius: 8px;
  border: 1px solid #e0e0e0;
  box-shadow: 0 2px 8px rgba(60,60,60,0.06);
}

.card-body {
  padding: 2rem;
}

.filter-input {
  border-radius: 8px;
  font-size: 15px;
  border: 1px solid #e0e0e0;
  background: #fafbfc;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.filter-input:focus {
  border-color: #c62828;
  box-shadow: 0 0 0 0.2rem rgba(198, 40, 40, 0.25);
}

.btn-primary {
  background-color: #c62828;
  border-color: #c62828;
  border-radius: 8px;
  font-weight: 500;
  padding: 0.75rem 1.5rem;
}

.btn-primary:hover:not(:disabled) {
  background-color: #b71c1c;
  border-color: #b71c1c;
}

.btn-primary:disabled {
  background-color: #e57373;
  border-color: #e57373;
}

.btn-outline-primary {
  border-color: #c62828;
  color: #c62828;
  border-radius: 8px;
  font-weight: 500;
}

.btn-outline-primary:hover {
  background-color: #c62828;
  border-color: #c62828;
  color: white;
}

.alert {
  border-radius: 8px;
  border: none;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}

.border-start {
  border-left: 2px solid #e9ecef !important;
}

.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}

@media (max-width: 768px) {
  .user-profile-page {
    padding: 15px;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .border-start {
    border-left: none !important;
    padding-left: 0 !important;
    border-top: 2px solid #e9ecef;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
  }
}
</style>