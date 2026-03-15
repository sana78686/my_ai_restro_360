<template>
  <div class="login-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body p-4">
              <div class="text-center mb-4">
                <img src="/images/logo.png" alt="Logo" class="logo mb-3">
                <h4 class="card-title">Super User Login</h4>
                <p class="text-muted">Enter your credentials to access the dashboard</p>
              </div>

              <form @submit.prevent="handleLogin">
                <div class="mb-3">
                  <label for="email" class="form-label">Email address</label>
                  <input 
                    type="email" 
                    class="form-control" 
                    id="email" 
                    v-model="form.email"
                    :class="{ 'is-invalid': errors.email }"
                    required
                  >
                  <div class="invalid-feedback" v-if="errors.email">
                    {{ errors.email[0] }}
                  </div>
                </div>

                <div class="mb-3">
                  <label for="password" class="form-label">Password</label>
                  <input 
                    type="password" 
                    class="form-control" 
                    id="password" 
                    v-model="form.password"
                    :class="{ 'is-invalid': errors.password }"
                    required
                  >
                  <div class="invalid-feedback" v-if="errors.password">
                    {{ errors.password[0] }}
                  </div>
                </div>

                <div class="d-grid">
                  <button 
                    type="submit" 
                    class="btn btn-primary"
                    :disabled="loading"
                  >
                    <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                    {{ loading ? 'Logging in...' : 'Login' }}
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

<script>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

export default {
  name: 'Login',
  setup() {
    const router = useRouter()
    const loading = ref(false)
    const errors = ref({})
    const form = reactive({
      email: '',
      password: ''
    })

    const handleLogin = async () => {
      loading.value = true
      errors.value = {}

      try {
        const response = await axios.post('/login', form)
        
        if (response.data.success) {
          // Store the token and user data
          localStorage.setItem('token', response.data.token)
          localStorage.setItem('user', JSON.stringify({
            ...response.data.user,
            roles: response.data.user.roles
          }))
          
          // Set axios default authorization header
          axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
          
          // Show success message
          await Swal.fire({
            icon: 'success',
            title: 'Login Successful',
            text: 'Welcome back!',
            timer: 1500,
            showConfirmButton: false,
            position: 'top-end',
            toast: true
          })
          
          // Redirect to dashboard
          router.push('/dashboard')
        } else {
          errors.value = {
            email: [response.data.message || 'Login failed']
          }
        }
      } catch (error) {
        console.error('Login error:', error)
        if (error.response?.data?.message) {
          Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: error.response.data.message,
            confirmButtonColor: '#dc3545'
          })
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Login Failed',
            text: 'Invalid credentials',
            confirmButtonColor: '#dc3545'
          })
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      loading,
      handleLogin
    }
  }
}
</script>

<style scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  background-color: #f8f9fa;
}

.logo {
  max-width: 150px;
  height: auto;
}

.card {
  border: none;
  border-radius: 10px;
}

.card-title {
  color: #343a40;
  font-weight: 600;
}

.form-label {
  font-weight: 500;
  color: #495057;
}

.btn-primary {
  padding: 0.75rem;
  font-weight: 500;
}

.invalid-feedback {
  font-size: 0.875rem;
}
</style> 