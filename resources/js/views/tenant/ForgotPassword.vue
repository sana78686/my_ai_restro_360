<template>
  <div class="mt-5 login-page d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="login-card d-flex flex-row shadow overflow-hidden bg-white">
      <!-- Left: Image -->
      <div class="login-image d-none d-md-block">
        <img src="/Mox_files/login.jpg" alt="Verify OTP" class="img-fluid h-100 w-100" style="object-fit: cover; min-width: 320px; min-height: 350px;" />
      </div>

      <!-- Right: Form -->
      <div class="login-form login-card-inner p-4 p-md-5 d-flex flex-column justify-content-center">
        <h2 class="text-center mb-2 login-title">{{ $t('auth.forgot.title') }}</h2>
        <div class="text-center mb-3 login-subtext">{{ $t('auth.forgot.subtitle') }}</div>

        <!-- Error Message -->
        <div v-if="error" class="alert alert-danger mb-3" role="alert">
          {{ error }}
        </div>

        <form @submit.prevent="handleSendOtp">
          <div class="mb-3">
            <div class="input-group">
              <span class="input-group-text bg-white"><i class="material-icons text-muted">email</i></span>
              <input
                type="email"
                autocomplete="email"
                class="form-control"
                :class="{ 'is-invalid': error }"
                :placeholder="$t('auth.forgot.placeholder')"
                v-model="email"
                :disabled="loading"
                maxlength="255"
                pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}"
                required
              />
            </div>
            <div v-if="error" class="invalid-feedback">
              {{ error }}
            </div>
          </div>

          <button
            type="submit"
            class="btn btn-success w-100 fw-bold mb-3 login-btn"
            :disabled="loading || !email"
          >
            <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ loading ? $t('common.processing') : $t('auth.forgot.submit') }}
          </button>
        </form>

        <!-- <div class="text-end mt-2">
          <button
            type="button"
            class="btn btn-link login-reset p-0"
            @click="handleResendOtp"
            :disabled="loading || resendCooldown > 0"
          >
            <i class="material-icons align-middle" style="font-size: 1.1em;">refresh</i>
            {{ resendCooldown > 0 ? `${$t('auth.forgot.resend')} (${resendCooldown}s)` : $t('auth.forgot.resend') }}
          </button>
        </div> -->
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import Swal from 'sweetalert2'

const { t } = useI18n()
const email = ref('')
const router = useRouter()
const loading = ref(false)
const error = ref('')
const resendCooldown = ref(0)
let resendTimer = null

// === Send Password Reset OTP ===
async function handleSendOtp() {
  error.value = ''
  loading.value = true
  try {
    const response = await axios.post('/tenant/send-otp', { email: email.value })

    await Swal.fire({
      icon: 'success',
      title: t('auth.forgot.emailSentTitle') || 'OTP Sent',
      text: response.data?.message || t('auth.forgot.emailSentText') || 'We have sent an OTP to your email.',
      timer: 1500,
      showConfirmButton: false,
      position: 'top-end',
      toast: true
    })

    localStorage.setItem('password_reset_email', email.value)
    router.push('/reset-password')
  } catch (err) {
    console.error('Send OTP error:', err)
    error.value = err.response?.data?.message || t('auth.forgot.sendError') || 'Failed to send OTP. Please try again.'
    Swal.fire({
      icon: 'error',
      title: t('common.error'),
      text: error.value,
      confirmButtonColor: '#dc3545'
    })
  } finally {
    loading.value = false
  }
}

// === Resend OTP (password reset) ===
async function handleResendOtp() {
  if (resendCooldown.value > 0) return
  loading.value = true
  error.value = ''

  try {
    const targetEmail = email.value || localStorage.getItem('password_reset_email')
    if (!targetEmail) throw new Error('No email provided')

    const response = await axios.post('/tenant/send-otp', { email: targetEmail })

    if (response.data.success) {
      Swal.fire({
        icon: 'success',
        title: t('auth.otp.resendSuccess'),
        text: response.data.message,
        timer: 1500,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
      })
      startResendCooldown()
    } else {
      throw new Error(response.data.message || t('auth.otp.resendErrorMessage'))
    }
  } catch (err) {
    console.error('Resend OTP error:', err)
    error.value = err.response?.data?.message || t('auth.otp.resendErrorMessage')

    Swal.fire({
      icon: 'error',
      title: t('common.error'),
      text: error.value,
      confirmButtonColor: '#dc3545'
    })
  } finally {
    loading.value = false
  }
}

// === Cooldown timer ===
function startResendCooldown() {
  resendCooldown.value = 60
  resendTimer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) {
      clearInterval(resendTimer)
      resendTimer = null
    }
  }, 1000)
}

onBeforeUnmount(() => {
  if (resendTimer) clearInterval(resendTimer)
})
</script>

<style scoped>
/* ✅ keeping all your styles the same */
</style>
<style scoped>
/* keeping your exact same styles */
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
