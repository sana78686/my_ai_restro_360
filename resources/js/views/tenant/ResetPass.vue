<template>
  <div class="restro-login">
    <div class="restro-login__center">
      <h1 class="restro-login__title">{{ $t('auth.reset.title') }}</h1>
      <p class="restro-login__subtitle">{{ $t('auth.reset.subtitle') }}</p>
      <form class="restro-form" @submit.prevent="handleResetPassword" novalidate>
        <p v-if="formError" class="restro-form__msg restro-form__msg--error" role="alert">
          {{ formError }}
        </p>
        <div class="restro-form__stack">
          <div class="restro-form__field">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="tenant-reset-otp">{{ $t('auth.reset.otp') }}</label>
              <RestroInfoTip :text="$t('auth.login.tipOtp')" />
            </div>
            <input
              id="tenant-reset-otp"
              v-model="otp"
              type="text"
              class="restro-form__input"
              inputmode="numeric"
              autocomplete="one-time-code"
              :placeholder="$t('auth.reset.otp')"
              :disabled="loading"
              required
            />
          </div>
          <div class="restro-form__field restro-form__field--pw">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="tenant-reset-pw1">{{ $t('auth.reset.password') }}</label>
              <RestroInfoTip :text="$t('auth.login.tipPassword')" />
            </div>
            <div class="restro-form__pw-inner">
              <input
                id="tenant-reset-pw1"
                v-model="newPassword"
                :type="showPassword ? 'text' : 'password'"
                class="restro-form__input restro-form__input--with-toggle"
                autocomplete="new-password"
                :placeholder="$t('auth.reset.password')"
                :disabled="loading"
                required
              />
              <button
                type="button"
                class="restro-form__pw-toggle"
                :aria-pressed="showPassword"
                :aria-label="showPassword ? $t('auth.login.hidePassword') : $t('auth.login.showPassword')"
                @click="showPassword = !showPassword"
              >
                <i
                  class="restro-form__pw-icon"
                  :class="showPassword ? 'far fa-eye-slash' : 'far fa-eye'"
                  aria-hidden="true"
                />
              </button>
            </div>
          </div>
          <div class="restro-form__field restro-form__field--pw">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="tenant-reset-pw2">{{
                $t('auth.reset.confirmPassword')
              }}</label>
              <RestroInfoTip :text="$t('auth.login.tipPassword')" />
            </div>
            <div class="restro-form__pw-inner">
              <input
                id="tenant-reset-pw2"
                v-model="newPasswordConfirm"
                :type="showPassword2 ? 'text' : 'password'"
                class="restro-form__input restro-form__input--with-toggle"
                autocomplete="new-password"
                :placeholder="$t('auth.reset.confirmPassword')"
                :disabled="loading"
                required
              />
              <button
                type="button"
                class="restro-form__pw-toggle"
                :aria-pressed="showPassword2"
                :aria-label="showPassword2 ? $t('auth.login.hidePassword') : $t('auth.login.showPassword')"
                @click="showPassword2 = !showPassword2"
              >
                <i
                  class="restro-form__pw-icon"
                  :class="showPassword2 ? 'far fa-eye-slash' : 'far fa-eye'"
                  aria-hidden="true"
                />
              </button>
            </div>
          </div>
          <button class="restro-form__submit" type="submit" :disabled="loading">
            {{ loading ? '…' : $t('auth.reset.submit') }}
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
import axios from 'axios'
import Swal from 'sweetalert2'
import RestroInfoTip from '../../components/frontend/RestroInfoTip.vue'

const newPassword = ref('')
const newPasswordConfirm = ref('')
const otp = ref('')
const showPassword = ref(false)
const showPassword2 = ref(false)
const router = useRouter()
const loading = ref(false)
const formError = ref('')

async function handleResetPassword() {
  formError.value = ''
  loading.value = true
  try {
    const response = await axios.post('/tenant/reset-password', {
      password: newPassword.value,
      password_confirmation: newPasswordConfirm.value,
      otp: otp.value
    })

    if (response.data.status === 'verify_required') {
      const email = localStorage.getItem('password_reset_email')
      if (email) {
        localStorage.setItem('pending_verification_email', email)
      }
      await Swal.fire({
        icon: 'info',
        title: 'Email verification',
        text: 'Please verify your account with the code we sent, then try signing in again.',
        confirmButtonColor: '#00844d'
      })
      await router.push({ path: '/login', query: { verify: '1' } })
      return
    }

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
    formError.value = err.response?.data?.message || 'Reset password failed. Please check your input.'
    Swal.fire({
      icon: 'error',
      title: 'Reset Password Failed',
      text: formError.value,
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
.restro-login .restro-form__field--pw {
  position: relative;
}
.restro-login .restro-form__pw-inner {
  position: relative;
  display: block;
}
.restro-login .restro-form__pw-toggle {
  position: absolute;
  right: 0.35rem;
  top: 50%;
  transform: translateY(-50%);
  border: none;
  background: color-mix(in srgb, #00844d 6%, #fff);
  color: #00844d;
  cursor: pointer;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  transition: background 0.15s, color 0.15s;
  font-family: inherit;
}
.restro-login .restro-form__pw-toggle:hover {
  background: color-mix(in srgb, #00844d 12%, #fff);
  color: #006a3e;
}
.restro-login .restro-form__pw-toggle:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px color-mix(in srgb, #00844d 40%, #fff);
}
.restro-login .restro-form__pw-icon {
  font-size: 1.05rem;
  line-height: 1;
}
.restro-login .restro-form__input--with-toggle {
  padding-right: 3.25rem;
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
