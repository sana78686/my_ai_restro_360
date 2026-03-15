<template>
  <div class="mt-5 login-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="login-card d-flex flex-row shadow overflow-hidden bg-white">
      <!-- Left: Image -->
      <div class="login-image d-none d-md-block">
        <img
          src="/Mox_files/login.jpg"
          alt="Login Food"
          class="img-fluid h-100 w-100"
          style="object-fit: cover; min-width: 320px; min-height: 350px;"
        />
      </div>

      <!-- Right: Form -->
      <div class="login-form login-card-inner p-4 p-md-5 d-flex flex-column justify-content-center">
        <h2 class="text-center mb-2 login-title">{{ $t('auth.reset.title') }}</h2>
        <div class="text-center mb-3 login-subtext">{{ $t('welcomeBack') || 'Welcome!' }}</div>

        <form @submit.prevent="handleResetPassword">
          <!-- OTP field -->
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="material-icons text-muted">key</i></span>
              <input
                type="text"
                class="form-control"
                :placeholder="$t('auth.reset.otp')"
                v-model="otp"
                required
              />
            </div>
          </div>

          <!-- New password field -->
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="material-icons text-muted">lock</i></span>
              <input
                :type="showPassword ? 'text' :'password'"
                class="form-control"
                :placeholder="$t('auth.reset.password')"
                v-model="newPassword"
                required
              />
              <span
                class="input-group-text bg-white"
                style="cursor:pointer;"
                @click="showPassword = !showPassword"
              >
                <i class="material-icons text-muted">{{ showPassword ? 'visibility_off' : 'visibility' }}</i>
              </span>
            </div>
          </div>

          <!-- Confirm password field -->
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="material-icons text-muted">lock</i></span>
              <input
                :type="showPassword ? 'text' : 'password'"
                class="form-control"
                :placeholder="$t('auth.reset.confirmPassword')"
                v-model="newPasswordConfirm"
                required
              />
            </div>
          </div>

          <!-- Submit button -->
          <button
            type="submit"
            class="btn btn-success w-100 fw-bold mb-3 login-btn"
            :disabled="loading"
          >
            {{ loading ? 'Processing...' : $t('auth.reset.submit') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Swal from 'sweetalert2'

const newPassword = ref('')
const newPasswordConfirm = ref('')
const otp = ref('')
const showPassword = ref(false)
const router = useRouter()
const loading = ref(false)
const error = ref('')

async function handleResetPassword() {
  error.value = ''
  loading.value = true
  try {
    const response = await axios.post('/tenant/reset-password', {
      password: newPassword.value,
      password_confirmation: newPasswordConfirm.value,
      otp: otp.value,
    })

    // CASE 1: OTP verification still required
    if (response.data.status === 'verify_required') {
      await Swal.fire({
        icon: 'info',
        title: 'Email Verification Required',
        text: 'We have sent an OTP to your email. Please verify to continue.',
        confirmButtonColor: '#3085d6'
      })
      router.push('/verify-otp')
      return
    }

    // CASE 2: Reset successful
    const token = response.data.token
    localStorage.setItem('token', token)
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    await Swal.fire({
      icon: 'success',
      title: 'Reset Password Successful',
      text: 'Welcome back!',
      timer: 1500,
      showConfirmButton: false,
      position: 'top-end',
      toast: true
    })

    router.push('/dashboard/home')
  } catch (err) {
    error.value = err.response?.data?.message || 'Reset password failed. Please check your input.'
    Swal.fire({
      icon: 'error',
      title: 'Reset Password Failed',
      text: error.value,
      confirmButtonColor: '#dc3545'
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* your existing styles remain unchanged */
.login-page {
  background: #f8f9fa;
}
.login-card {
  min-width: 1000px;
  max-width: 900px;
  min-height: 350px;
  background: #fff;
}
.login-image {
  width: 500px;
  min-height: 350px;
  background: #eee;
}
.login-form {
  flex: 1 1 0%;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.07);
  min-width: 500px;
  max-width: 500px;
  align-items: center;
}
.input-group-text {
  background: #fff;
  border-right: 0;
  border-radius: 6px 0 0 6px;
}
.form-control {
  border-left: 0;
  border-radius: 0 6px 6px 0;
  box-shadow: none;
}
.btn-success {
  background: #388e3c;
  border: none;
}
.btn-success:hover {
  background: #2e7031;
  border: none;
}
.btn-light {
  background: #fff;
  border: 1px solid #eee;
}
.btn-light:hover {
  background: #f5f5f5;
}
@media (max-width: 991.98px) {
  .login-card {
    flex-direction: column;
    min-width: 320px;
    max-width: 95vw;
  }
  .login-image {
    width: 100%;
    min-height: 180px;
    max-height: 220px;
  }
  .login-form {
    min-width: 100%;
    max-width: 100%;
  }
  .login-card-inner {
    min-width: 100%;
    max-width: 100%;
  }
}

@media (max-width: 767.98px) {
  .login-page {
    padding: 1rem;
  }
  .login-card {
    min-width: 100%;
    max-width: 100%;
    border-radius: 12px;
  }
  .login-title {
    font-size: 1.75rem;
  }
  .login-subtext {
    font-size: 0.95rem;
  }
  .login-form {
    padding: 1.5rem !important;
  }
}

/* Android-specific fixes */
@media screen and (-webkit-min-device-pixel-ratio: 0) {
  .login-page,
  .login-card {
    -webkit-transform: translateZ(0);
    transform: translateZ(0);
    -webkit-font-smoothing: antialiased;
  }

  input {
    -webkit-appearance: none;
    -webkit-border-radius: 4px;
    border-radius: 4px;
    font-size: 16px; /* Prevents zoom on Android */
  }

  button, .btn {
    -webkit-tap-highlight-color: rgba(67, 160, 71, 0.2);
    touch-action: manipulation;
  }
}

@media (max-width: 575.98px) {
  .login-page {
    padding: 0.5rem;
  }
  .login-card {
    border-radius: 8px;
  }
  .login-title {
    font-size: 1.5rem;
  }
  .login-subtext {
    font-size: 0.9rem;
  }
  .login-form {
    padding: 1rem !important;
  }
  .login-btn {
    font-size: 1rem;
  }
}
/* Login Card Inner Styling */
.login-card-inner {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  box-shadow: 0 2px 12px rgba(0,0,0,0.07);
  min-width: 500px;
  max-width: 500px;
  margin: 0 auto;
}
.login-title {
  font-family: 'Playfair Display', serif;
  font-weight: bold;
  color: #222;
  font-size: 2rem;
}
.login-subtext {
  color: #388e3c;
  font-size: 1rem;
}
.login-link {
  color: #388e3c;
  text-decoration: none;
  font-weight: 500;
}
.login-link:hover {
  text-decoration: underline;
}
.login-btn {
  background: #43a047;
  border-color: #43a047;
  border-radius: 4px;
  font-weight: 600;
  font-size: 1.05rem;
}
.login-btn:hover {
  background: #388e3c;
  border-color: #388e3c;
}
.login-or {
  color: #888;
  font-size: 0.97rem;
}
.login-social {
  background: #fff;
  border: 1px solid #eee;
  transition: box-shadow 0.2s;
}
.login-social:hover {
  box-shadow: 0 2px 8px rgba(67,160,71,0.12);
}
.login-reset {
  color: #444;
  font-size: 0.97rem;
  text-decoration: none;
  transition: color 0.2s;
}
.login-reset:hover {
  color: #43a047;
  text-decoration: underline;
}
</style>
