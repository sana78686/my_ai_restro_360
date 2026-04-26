<template>
  <div class="restro-login">
    <div class="restro-login__center">
      <h1 class="restro-login__title">{{ $t('auth.forgot.title') }}</h1>
      <p class="restro-login__subtitle">{{ $t('auth.forgot.subtitle') }}</p>
      <form class="restro-form" @submit.prevent="handleSendOtp" novalidate>
        <p v-if="error" class="restro-form__msg restro-form__msg--error" role="alert">
          {{ error }}
        </p>
        <div class="restro-form__stack">
          <div class="restro-form__field">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="tenant-forgot-email">{{ $t('auth.login.email') }}</label>
              <RestroInfoTip :text="$t('auth.login.tipEmail')" />
            </div>
            <input
              id="tenant-forgot-email"
              v-model.trim="email"
              type="email"
              class="restro-form__input"
              autocomplete="email"
              :placeholder="$t('auth.forgot.placeholder')"
              :disabled="loading"
              maxlength="255"
              required
            />
          </div>
          <button class="restro-form__submit" type="submit" :disabled="loading || !email">
            {{ loading ? '…' : $t('auth.forgot.submit') }}
          </button>
          <router-link class="restro-form__link" to="/login">{{ $t('auth.forgot.backToSignIn') }}</router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import axios from 'axios'
import Swal from 'sweetalert2'
import RestroInfoTip from '../../components/frontend/RestroInfoTip.vue'

const { t } = useI18n()
const email = ref('')
const router = useRouter()
const loading = ref(false)
const error = ref('')

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
    error.value =
      err.response?.data?.message || t('auth.forgot.sendError') || 'Failed to send OTP. Please try again.'
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: error.value,
      confirmButtonColor: '#dc3545'
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.restro-login {
  width: 100%;
  flex: 1 1 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 0;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.restro-login__center {
  width: 100%;
  max-width: 32rem;
}

.restro-login__title {
  font-size: 1.28rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 0.5rem;
  line-height: 1.25;
  letter-spacing: -0.02em;
  text-align: center;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.restro-login__subtitle {
  margin: 0 0 1.35rem;
  font-size: 0.9rem;
  color: #5e5e5e;
  line-height: 1.45;
  text-align: center;
}
</style>

<style>
.restro-login .restro-form {
  display: flex;
  flex-direction: column;
  gap: 0.9rem;
}
.restro-login .restro-form__stack {
  display: flex;
  flex-direction: column;
  gap: 0.95rem;
}
.restro-login .restro-form__field {
  display: flex;
  flex-direction: column;
  gap: 0.45rem;
  min-width: 0;
}
.restro-login .restro-form__label-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}
.restro-login .restro-form__label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}
.restro-login .restro-form__input {
  width: 100%;
  min-width: 0;
  border: 1px solid #d0d0d0;
  border-radius: 10px;
  padding: 0.68rem 0.9rem;
  font-size: 1rem;
  background: #fff;
  color: #111;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.restro-login .restro-form__input::placeholder {
  color: #9a9a9a;
  opacity: 1;
  font-size: 0.95rem;
}
.restro-login .restro-form__input:focus {
  outline: none;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 25%, #fff);
  border-color: #00844d;
}
.restro-login .restro-form__msg {
  margin: 0 0 0.5rem;
  font-size: 0.88rem;
  line-height: 1.45;
  padding: 0.55rem 0.7rem;
  border-radius: 8px;
}
.restro-login .restro-form__msg--error {
  color: #6e1414;
  background: #fff0f0;
  border: 1px solid #e8b4b4;
}
.restro-login .restro-form__submit {
  width: 100%;
  border: none;
  border-radius: 999px;
  background: #00844d;
  color: #fff;
  font-weight: 700;
  font-size: 0.95rem;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 0.88rem 1.15rem;
  cursor: pointer;
  margin-top: 0.25rem;
  transition: filter 0.15s, opacity 0.15s;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.restro-login .restro-form__submit:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #00844d;
}
.restro-login .restro-form__submit:hover:not(:disabled) {
  filter: brightness(0.95);
}
.restro-login .restro-form__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.restro-login .restro-form__link {
  color: #00844d;
  font-weight: 600;
  text-decoration: none;
  font-size: 0.85rem;
  text-align: center;
  margin-top: 0.15rem;
}
.restro-login .restro-form__link:hover {
  text-decoration: underline;
}
</style>
