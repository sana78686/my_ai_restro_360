<template>
  <div class="reset-password-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0">{{ $t('auth.reset_password.title') }}</h4>
    </div>

    <!-- Reset Password Card -->
    <div class="card">
      <div class="card-body">
        <!-- Success Message -->
        <div v-if="success" class="text-center py-5">
          <div class="mb-4">
            <i class="fas fa-check-circle fa-4x text-success"></i>
          </div>
          <h5 class="mb-3">{{ $t('auth.reset_password.success_title') }}</h5>
          <p class="text-muted mb-4">{{ $t('auth.reset_password.success_message') }}</p>
          <button class="btn btn-primary" @click="$router.push('/login')">
            {{ $t('auth.reset_password.back_to_login') }}
          </button>
        </div>

        <!-- Main Content -->
        <div v-else>
          <!-- Forgot Password Link -->
          <div class="mb-4 text-center">
            <p class="text-muted mb-3">{{ $t('auth.reset_password.forgot_password_prompt') }}</p>
            <button 
              class="btn btn-outline-primary" 
              @click="$router.push('/forgot-password')"
            >
              <i class="fas fa-envelope me-2"></i>
              {{ $t('auth.reset_password.reset_via_email') }}
            </button>
          </div>

          <hr class="my-4">

          <!-- Change Password Form -->
          <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
              <h5 class="text-center mb-4">{{ $t('auth.reset_password.change_password') }}</h5>
              <form @submit.prevent="submitChangePassword">
                <!-- Current Password -->
                <div class="mb-3">
                  <label for="current_password" class="form-label">
                    {{ $t('auth.reset_password.current_password') }}
                  </label>
                  <div class="input-group">
                    <input 
                      v-model="changeForm.current_password" 
                      :type="showCurrentPassword ? 'text' : 'password'" 
                      class="form-control filter-input" 
                      id="current_password"
                      :placeholder="$t('auth.reset_password.current_password_placeholder')"
                      required
                    >
                    <button 
                      class="btn btn-outline-secondary" 
                      type="button" 
                      @click="showCurrentPassword = !showCurrentPassword"
                    >
                      <i :class="showCurrentPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>

                <!-- New Password -->
                <div class="mb-3">
                  <label for="new_password" class="form-label">
                    {{ $t('auth.reset_password.new_password') }}
                  </label>
                  <div class="input-group">
                    <input 
                      v-model="changeForm.new_password" 
                      :type="showNewPassword ? 'text' : 'password'" 
                      class="form-control filter-input" 
                      id="new_password"
                      :placeholder="$t('auth.reset_password.new_password_placeholder')"
                      required
                    >
                    <button 
                      class="btn btn-outline-secondary" 
                      type="button" 
                      @click="showNewPassword = !showNewPassword"
                    >
                      <i :class="showNewPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <div class="form-text">{{ $t('auth.reset_password.password_requirements') }}</div>
                </div>

                <!-- Confirm New Password -->
                <div class="mb-4">
                  <label for="new_password_confirmation" class="form-label">
                    {{ $t('auth.reset_password.confirm_new_password') }}
                  </label>
                  <div class="input-group">
                    <input 
                      v-model="changeForm.new_password_confirmation" 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      class="form-control filter-input" 
                      id="new_password_confirmation"
                      :placeholder="$t('auth.reset_password.confirm_new_password_placeholder')"
                      required
                    >
                    <button 
                      class="btn btn-outline-secondary" 
                      type="button" 
                      @click="showConfirmPassword = !showConfirmPassword"
                    >
                      <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid">
                  <button 
                    type="submit" 
                    class="btn btn-primary btn-lg" 
                    :disabled="loading || !isChangeFormValid"
                  >
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                    {{ $t('auth.reset_password.change_password_button') }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'
import { useI18n } from 'vue-i18n'

const { t: $t } = useI18n()
const router = useRouter()

const loading = ref(false)
const success = ref(false)

// Password visibility toggles
const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showConfirmPassword = ref(false)

// Change password form
const changeForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})

// Form validation
const isChangeFormValid = computed(() => {
  return changeForm.value.current_password && 
         changeForm.value.new_password && 
         changeForm.value.new_password_confirmation && 
         changeForm.value.new_password === changeForm.value.new_password_confirmation &&
         changeForm.value.new_password.length >= 8
})

// Submit change password (when user knows current password)
const submitChangePassword = async () => {
  if (!isChangeFormValid.value) return

  loading.value = true
  try {
    const response = await axios.post('/tenant/change-password', changeForm.value)
    
    if (response.data.success) {
      success.value = true
      Swal.fire({
        icon: 'success',
        title: $t('auth.reset_password.change_success_title'),
        text: $t('auth.reset_password.change_success_message'),
        timer: 3000
      })
    } else {
      Swal.fire({
        icon: 'error',
        title: $t('auth.reset_password.change_error_title'),
        text: response.data.message || $t('auth.reset_password.change_error_message')
      })
    }
  } catch (error) {
    let errorMessage = $t('auth.reset_password.change_error_message')
    
    if (error.response && error.response.status === 422) {
      const errors = error.response.data.errors
      if (errors.current_password) {
        errorMessage = errors.current_password[0]
      } else if (errors.new_password) {
        errorMessage = errors.new_password[0]
      }
    } else if (error.response && error.response.status === 401) {
      errorMessage = $t('auth.reset_password.invalid_current_password')
    }
    
    Swal.fire({
      icon: 'error',
      title: $t('auth.reset_password.change_error_title'),
      text: errorMessage
    })
  } finally {
    loading.value = false
  }
}

// Pre-fill email if available (from user profile, etc.)
onMounted(() => {
  // You can pre-fill the email from user context if available
  // resetForm.value.email = userEmail.value
})
</script>

<style scoped>
.reset-password-page {
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
  padding: 0.75rem 1.5rem;
}

.btn-outline-primary:hover {
  background-color: #c62828;
  border-color: #c62828;
  color: white;
}

.btn-outline-secondary {
  border-radius: 0 8px 8px 0;
  border-left: 0;
}

.form-label {
  font-weight: 500;
  margin-bottom: 0.5rem;
}

.form-text {
  font-size: 0.875rem;
  color: #6c757d;
  margin-top: 0.25rem;
}

.text-success {
  color: #28a745 !important;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

hr {
  border-top: 1px solid #e0e0e0;
  opacity: 0.7;
}

@media (max-width: 768px) {
  .reset-password-page {
    padding: 15px;
  }
  
  .card-body {
    padding: 1.5rem;
  }
}
</style>