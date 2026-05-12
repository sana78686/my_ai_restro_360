<template>
  <div class="restro-login">
    <div class="restro-login__center">
      <h1 class="restro-login__title">{{ $t('auth.login.title') }}</h1>
      <form class="restro-form" @submit.prevent="onSubmit" novalidate>
          <p
            v-if="showRegisteredInfo"
            class="restro-form__msg restro-form__msg--info"
            role="status"
          >
            {{ $t('auth.login.registeredInfo') }}
          </p>
          <p v-if="formError" class="restro-form__msg restro-form__msg--error" role="alert">
            {{ formError }}
          </p>
          <p
            v-if="showVerifyPanel && !formError"
            class="restro-form__msg restro-form__msg--info"
            role="status"
          >
            {{ $t('auth.login.emailVerifyHint') }}
          </p>

          <div v-show="!showVerifyPanel" class="restro-form__stack">
            <div class="restro-form__field">
              <div class="restro-form__label-row">
                <label class="restro-form__label" for="tenant-login-email">{{
                  $t('auth.login.email')
                }}</label>
                <RestroInfoTip :text="$t('auth.login.tipEmail')" />
              </div>
              <input
                id="tenant-login-email"
                v-model.trim="username"
                type="text"
                class="restro-form__input"
                autocomplete="username"
                :placeholder="$t('auth.login.email')"
                :disabled="loading"
                required
              />
            </div>
            <div class="restro-form__field restro-form__field--pw">
              <div class="restro-form__label-row">
                <label class="restro-form__label" for="tenant-login-pw">{{
                  $t('auth.login.password')
                }}</label>
                <RestroInfoTip :text="$t('auth.login.tipPassword')" />
              </div>
              <div class="restro-form__pw-inner">
                <input
                  id="tenant-login-pw"
                  v-model="password"
                  :type="showPassword ? 'text' : 'password'"
                  class="restro-form__input restro-form__input--with-toggle"
                  autocomplete="current-password"
                  :placeholder="$t('auth.login.password')"
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
            <div class="restro-form__check-row">
              <input
                id="tenant-login-remember"
                v-model="keepSignedIn"
                type="checkbox"
                class="restro-form__checkbox"
              />
              <label for="tenant-login-remember">{{ $t('auth.login.remember') }}</label>
            </div>
            <TurnstileChallenge
              v-if="turnstileEnabled"
              ref="turnstileLoginRef"
              action-key="tenant-login"
              @token="onTsLoginToken"
              @expire="onTsLoginExpire"
              @fail="onTsLoginExpire"
            />
            <button class="restro-form__submit" type="submit" :disabled="loading">
              {{ loading && !showVerifyPanel ? '…' : $t('auth.login.submit') }}
            </button>
            <router-link class="restro-form__link" to="/forgot-password">{{
              $t('auth.login.forgot')
            }}</router-link>
          </div>

          <template v-if="showVerifyPanel">
            <hr class="restro-form__hr" />
            <div class="restro-form__verify" role="region" :aria-label="$t('auth.otp.title')">
              <p class="restro-form__verify-title">{{ $t('auth.otp.subtitle') }}</p>
              <p class="restro-form__verify-hint">{{ $t('auth.login.otpOnSamePage') }}</p>
              <div class="restro-form__field">
                <div class="restro-form__label-row">
                  <label class="restro-form__label" for="tenant-verify-otp">{{
                    $t('auth.otp.placeholder')
                  }}</label>
                  <RestroInfoTip :text="$t('auth.login.tipOtp')" />
                </div>
                <input
                  id="tenant-verify-otp"
                  v-model="verifyOtp"
                  type="text"
                  class="restro-form__input"
                  inputmode="numeric"
                  maxlength="6"
                  pattern="[0-9]*"
                  autocomplete="one-time-code"
                  :disabled="verifyLoading"
                  :placeholder="$t('auth.otp.placeholder')"
                />
              </div>
              <p v-if="verifyError" class="restro-form__msg restro-form__msg--error" role="alert">
                {{ verifyError }}
              </p>
              <TurnstileChallenge
                v-if="turnstileEnabled"
                ref="turnstileOtpRef"
                action-key="tenant-verify-otp"
                @token="onTsOtpToken"
                @expire="onTsOtpExpire"
                @fail="onTsOtpExpire"
              />
              <div class="restro-form__row--2 restro-form__row--verify-actions">
                <button
                  type="submit"
                  class="restro-form__submit"
                  :disabled="verifyLoading || !canVerifyOtp"
                >
                  {{ verifyLoading ? '…' : $t('auth.otp.submit') }}
                </button>
                <button
                  type="button"
                  class="restro-form__subbtn"
                  :disabled="verifyLoading || resendCooldown > 0"
                  @click="handleResendOtp"
                >
                  {{
                    resendCooldown > 0
                      ? `${$t('auth.otp.resend')} (${resendCooldown}s)`
                      : $t('auth.otp.resend')
                  }}
                </button>
              </div>
            </div>
          </template>
      </form>
    </div>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import { logTenantOtpFromTableIfPending } from '../../utils/tenantOtpFromTable'
import RestroInfoTip from '../../components/frontend/RestroInfoTip.vue'
import TurnstileChallenge from '../../components/TurnstileChallenge.vue'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const username = ref('')
const password = ref('')
const showPassword = ref(false)
const keepSignedIn = ref(false)
const loading = ref(false)
const formError = ref('')

const showVerifyPanel = ref(false)
const verifyOtp = ref('')
const verifyLoading = ref(false)
const verifyError = ref('')
const resendCooldown = ref(0)
let resendTimer = null

const turnstileLoginRef = ref(null)
const turnstileOtpRef = ref(null)
const tsLoginToken = ref('')
const tsOtpToken = ref('')

const turnstileEnabled = computed(
  () => typeof window !== 'undefined' && !!window.TURNSTILE_SITE_KEY
)

function onTsLoginToken (token) {
  tsLoginToken.value = token || ''
}
function onTsLoginExpire () {
  tsLoginToken.value = ''
}
function onTsOtpToken (token) {
  tsOtpToken.value = token || ''
}
function onTsOtpExpire () {
  tsOtpToken.value = ''
}

const canVerifyOtp = computed(
  () => verifyOtp.value && verifyOtp.value.length === 6 && /^\d{6}$/.test(verifyOtp.value)
)

const showRegisteredInfo = computed(() => route.query.registered === '1')

function clearResendTimer() {
  if (resendTimer) {
    clearInterval(resendTimer)
    resendTimer = null
  }
}

function startResendCooldown() {
  resendCooldown.value = 60
  clearResendTimer()
  resendTimer = setInterval(() => {
    resendCooldown.value--
    if (resendCooldown.value <= 0) clearResendTimer()
  }, 1000)
}

onMounted(() => {
  const qEmail = route.query.email
  if (typeof qEmail === 'string' && qEmail.trim()) {
    username.value = decodeURIComponent(qEmail.trim())
  } else {
    const pendingEmail = localStorage.getItem('pending_verification_email')
    if (pendingEmail) username.value = pendingEmail
  }
  // Only force OTP UI for explicit deep-links (e.g. reset-password). Stale pending_verification_email
  // after admin approval must not hide the password form — user signs in and goes straight to dashboard.
  if (route.query.verify === '1') {
    showVerifyPanel.value = true
  }
  if (showVerifyPanel.value) void logTenantOtpFromTableIfPending()
})

onBeforeUnmount(() => clearResendTimer())

watch(
  () => route.query.verify,
  (v) => {
    if (v === '1') showVerifyPanel.value = true
  }
)

watch(
  () => route.query.email,
  (qEmail) => {
    if (typeof qEmail === 'string' && qEmail.trim()) {
      username.value = decodeURIComponent(qEmail.trim())
    }
  }
)

async function onSubmit() {
  formError.value = ''
  if (showVerifyPanel.value) {
    await handleVerifyOtp()
    return
  }
  if (turnstileEnabled.value && !tsLoginToken.value) {
    formError.value = t('auth.turnstile.required')
    return
  }
  loading.value = true
  try {
    const response = await window.axios.post('/tenant/login', {
      email: username.value,
      password: password.value,
      ...(tsLoginToken.value ? { turnstile_token: tsLoginToken.value } : {})
    })

    const token = response.data.token
    localStorage.setItem('token', token)
    localStorage.removeItem('pending_verification_email')
    showVerifyPanel.value = false
    window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`

    if (typeof navigator !== 'undefined' && navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(async (pos) => {
        const t0 = localStorage.getItem('token')
        if (!t0) return
        try {
          await window.axios.post(
            '/tenant/update-location',
            {
              latitude: pos.coords.latitude,
              longitude: pos.coords.longitude
            },
            { headers: { Authorization: `Bearer ${t0}` } }
          )
        } catch {
          /* optional */
        }
      })
    }

    await Swal.fire({
      icon: 'success',
      title: 'Login Successful',
      text: 'Welcome back!',
      timer: 1500,
      showConfirmButton: false,
      position: 'top-end',
      toast: true
    })
    router.push('/dashboard/home')
  } catch (err) {
    const st = err.response?.data?.status
    const adminPending = err.response?.status === 403 && st === 'admin_pending'
    formError.value = adminPending
      ? t('auth.login.adminPending')
      : err.response?.data?.message || 'Login failed. Please check your credentials.'
    if (!adminPending) {
      await Swal.fire({
        icon: 'error',
        title: 'Login Failed',
        text: formError.value,
        confirmButtonColor: '#dc3545'
      })
      tsLoginToken.value = ''
      turnstileLoginRef.value?.reset()
    }
  } finally {
    loading.value = false
  }
}

async function handleVerifyOtp() {
  verifyError.value = ''
  if (!canVerifyOtp.value) {
    verifyError.value = t('auth.otp.invalidOtp')
    return
  }
  if (turnstileEnabled.value && !tsOtpToken.value) {
    verifyError.value = t('auth.turnstile.required')
    return
  }
  verifyLoading.value = true
  try {
    const email = localStorage.getItem('pending_verification_email') || username.value
    if (!email) {
      throw new Error('No pending verification email')
    }
    const response = await window.axios.post('/tenant/verify-otp', {
      email,
      otp: verifyOtp.value,
      ...(tsOtpToken.value ? { turnstile_token: tsOtpToken.value } : {})
    })
    if (response.data.success) {
      const token = response.data.token
      localStorage.setItem('token', token)
      localStorage.removeItem('pending_verification_email')
      window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      showVerifyPanel.value = false
      await Swal.fire({
        icon: 'success',
        title: t('auth.otp.verificationSuccess'),
        text: response.data.message || t('auth.otp.verificationSuccessMessage'),
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
      })
      router.push('/dashboard/home')
    } else {
      throw new Error(response.data.message || t('auth.otp.verificationErrorMessage'))
    }
  } catch (err) {
    let msg = t('auth.otp.verificationErrorMessage')
    if (err.response?.status === 403) msg = t('auth.login.adminPending')
    else if (err.response?.data?.message) msg = err.response.data.message
    else if (err.response?.status === 422) msg = t('auth.otp.invalidOtp')
    else if (err.response?.status === 410) msg = t('auth.otp.otpExpired')
    verifyError.value = msg
    tsOtpToken.value = ''
    turnstileOtpRef.value?.reset()
  } finally {
    verifyLoading.value = false
  }
}

async function handleResendOtp() {
  if (resendCooldown.value > 0) return
  verifyLoading.value = true
  verifyError.value = ''
  try {
    const email = localStorage.getItem('pending_verification_email') || username.value
    if (!email) throw new Error('No email')
    const response = await window.axios.post('/tenant/resend-otp', { email })
    if (response.data.success) {
      startResendCooldown()
      await Swal.fire({
        icon: 'success',
        title: t('auth.otp.resendSuccess'),
        timer: 1500,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
      })
    } else {
      throw new Error(response.data.message || t('auth.otp.resendErrorMessage'))
    }
  } catch (err) {
    verifyError.value =
      err.response?.status === 403
        ? t('auth.login.adminPending')
        : err.response?.data?.message || t('auth.otp.resendErrorMessage')
  } finally {
    verifyLoading.value = false
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
  margin: 0 0 1.35rem;
  line-height: 1.25;
  letter-spacing: -0.02em;
  text-align: center;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
</style>

<!-- Register-style form controls (match Home.vue) -->
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
.restro-login .restro-form__row--2 {
  display: grid;
  gap: 0.5rem;
}
.restro-login .restro-form__row--verify-actions {
  grid-template-columns: 1fr 1fr;
  align-items: stretch;
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
.restro-login .restro-form__input,
.restro-login .restro-form__select {
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
.restro-login .restro-form__input:focus,
.restro-login .restro-form__select:focus {
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
.restro-login .restro-form__check-row {
  display: flex;
  align-items: center;
  gap: 0.45rem;
  font-size: 0.8rem;
  color: #333;
}
.restro-login .restro-form__checkbox {
  width: 1rem;
  height: 1rem;
  accent-color: #00844d;
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
.restro-login .restro-form__msg--info {
  color: #1a3d66;
  background: #f0f6ff;
  border: 1px solid #b8cceb;
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
.restro-login .restro-form__subbtn {
  border: none;
  background: none;
  color: #00844d;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  padding: 0.4rem 0.25rem;
  text-align: center;
  text-decoration: underline;
  font-family: inherit;
}
.restro-login .restro-form__subbtn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  text-decoration: none;
}
.restro-login .restro-form__hr {
  border: none;
  border-top: 1px solid #e8e8e8;
  margin: 0.4rem 0 0.6rem;
}
.restro-login .restro-form__verify-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
}
.restro-login .restro-form__verify-hint {
  font-size: 0.8rem;
  color: #5e5e5e;
  margin: 0 0 0.35rem;
  line-height: 1.4;
}
@media (max-width: 480px) {
  .restro-login .restro-form__row--verify-actions {
    grid-template-columns: 1fr;
  }
}
</style>
