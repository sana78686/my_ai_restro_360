<template>
  <div class="restro-login">
    <div class="restro-login__center">
      <div class="restro-login__intro">
        <h1 class="restro-login__title">{{ pageTitle }}</h1>
        <p class="restro-login__subtitle">{{ pageSubtitle }}</p>
        <p v-if="isEmailStep" class="restro-login__detail">{{ $t('auth.superAdmin.unifiedDetail') }}</p>
      </div>
      <form class="restro-form" @submit.prevent="onSubmit" novalidate>
        <p v-if="formError" class="restro-form__msg restro-form__msg--error" role="alert">
          {{ formError }}
        </p>

        <div v-if="isEmailStep" class="restro-form__stack">
          <a
            v-if="googleSignInEnabled"
            class="restro-login__google"
            href="/auth/google/redirect"
          >
            <i class="fab fa-google restro-login__google-icon" aria-hidden="true" />
            {{ $t('auth.superAdmin.signInWithGoogle') }}
          </a>
          <p v-if="googleSignInEnabled" class="restro-login__or">
            <span>{{ $t('auth.superAdmin.orContinueEmail') }}</span>
          </p>
          <div class="restro-form__field">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="unified-login-email">{{ $t('auth.login.email') }}</label>
              <RestroInfoTip :text="$t('auth.superAdmin.tipEmailLookup')" />
            </div>
            <input
              id="unified-login-email"
              v-model.trim="lookupEmail"
              type="email"
              class="restro-form__input"
              :class="{ 'restro-form__input--invalid': fieldErrors.lookup }"
              autocomplete="username"
              :placeholder="$t('auth.login.email')"
              :disabled="loading"
              required
            />
            <p v-if="fieldErrors.lookup" class="restro-form__field-error">{{ fieldErrors.lookup }}</p>
          </div>
          <TurnstileChallenge
            v-if="turnstileEnabled"
            ref="turnstileEmailRef"
            action-key="super-login-email"
            @token="onTsEmailToken"
            @expire="onTsEmailExpire"
            @fail="onTsEmailExpire"
          />
          <button class="restro-form__submit" type="submit" :disabled="loading">
            {{ loading ? '…' : $t('auth.superAdmin.continue') }}
          </button>
        </div>

        <div v-else class="restro-form__stack">
          <div class="restro-form__field">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="super-login-email-ro">{{ $t('auth.login.email') }}</label>
            </div>
            <input
              id="super-login-email-ro"
              :value="form.email"
              type="email"
              class="restro-form__input restro-form__input--readonly"
              readonly
              tabindex="-1"
              autocomplete="username"
            />
          </div>
          <div class="restro-form__field restro-form__field--pw">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="super-login-pw">{{ $t('auth.login.password') }}</label>
              <RestroInfoTip :text="$t('auth.superAdmin.tipPassword')" />
            </div>
            <div class="restro-form__pw-inner">
              <input
                id="super-login-pw"
                v-model="form.password"
                :type="showPassword ? 'text' : 'password'"
                class="restro-form__input restro-form__input--with-toggle"
                autocomplete="current-password"
                :class="{ 'restro-form__input--invalid': fieldErrors.password }"
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
            <p v-if="fieldErrors.password" class="restro-form__field-error">{{ fieldErrors.password }}</p>
          </div>
          <TurnstileChallenge
            v-if="turnstileEnabled"
            ref="turnstilePwRef"
            action-key="super-login-password"
            @token="onTsPwToken"
            @expire="onTsPwExpire"
            @fail="onTsPwExpire"
          />
          <button class="restro-form__submit" type="submit" :disabled="loading">
            {{ loading ? '…' : $t('auth.login.submit') }}
          </button>
          <button type="button" class="restro-login__textbtn" @click="goBackToEmailStep">
            {{ $t('auth.superAdmin.changeEmail') }}
          </button>
        </div>
      </form>
      <p class="restro-login__register">
        <span class="restro-login__register-prompt">{{ $t('auth.superAdmin.registerPrompt') }}</span>
        <router-link :to="{ name: 'home' }" class="restro-login__register-link">
          {{ $t('auth.superAdmin.registerCta') }}
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRoute, useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import RestroInfoTip from '../../components/frontend/RestroInfoTip.vue'
import TurnstileChallenge from '../../components/TurnstileChallenge.vue'
import { isTurnstileEnabled } from '../../utils/turnstile.js'

const { t } = useI18n()
const route = useRoute()
const router = useRouter()

const loading = ref(false)
const showPassword = ref(false)
const formError = ref('')
const lookupEmail = ref('')

const turnstileEmailRef = ref(null)
const turnstilePwRef = ref(null)
const tsEmailToken = ref('')
const tsPwToken = ref('')

const turnstileEnabled = computed(() => isTurnstileEnabled())

function onTsEmailToken (token) {
  tsEmailToken.value = token || ''
}
function onTsEmailExpire () {
  tsEmailToken.value = ''
}
function onTsPwToken (token) {
  tsPwToken.value = token || ''
}
function onTsPwExpire () {
  tsPwToken.value = ''
}

const fieldErrors = reactive({
  lookup: '',
  password: ''
})

const form = reactive({
  email: '',
  password: ''
})

const isEmailStep = computed(() => route.name !== 'login-password')

const googleSignInEnabled = computed(
  () => typeof window !== 'undefined' && !!window.GOOGLE_SIGNIN_ENABLED
)

const pageTitle = computed(() =>
  isEmailStep.value ? t('auth.superAdmin.unifiedTitle') : t('auth.superAdmin.passwordStepTitle')
)

const pageSubtitle = computed(() =>
  isEmailStep.value
    ? t('auth.superAdmin.unifiedSubtitle')
    : t('auth.superAdmin.passwordStepSubtitle', { email: form.email })
)

function scrollIntoFormAndFocus (el) {
  if (!el) return
  el.scrollIntoView({ behavior: 'smooth', block: 'center' })
  try {
    el.focus({ preventScroll: true })
  } catch {
    el.focus()
  }
}

function focusById (id) {
  const el = document.getElementById(id)
  scrollIntoFormAndFocus(el)
}

function clearFieldErrors () {
  fieldErrors.lookup = ''
  fieldErrors.password = ''
}

const STEP1_ROUTE_KEY = 'airestro_login_step1_route'

watch(
  () => [route.name, route.query.email],
  () => {
    formError.value = ''
    clearFieldErrors()
    if (route.name === 'login-password') {
      const q = route.query.email
      if (typeof q === 'string' && q.trim()) {
        form.email = q.trim()
        lookupEmail.value = form.email
      } else {
        void router.replace({ name: 'login' })
      }
    } else if (route.name === 'login' || route.name === 'super-login') {
      form.password = ''
      const q = route.query.email
      if (typeof q === 'string' && q.trim()) {
        lookupEmail.value = q.trim()
      }
    }
  },
  { immediate: true }
)

function goBackToEmailStep () {
  form.password = ''
  formError.value = ''
  const from = sessionStorage.getItem(STEP1_ROUTE_KEY)
  const target = from === 'super-login' ? 'super-login' : 'login'
  void router.push({ name: target })
}

async function runEmailLookup () {
  clearFieldErrors()
  sessionStorage.setItem(STEP1_ROUTE_KEY, route.name)
  const emailEl = document.getElementById('unified-login-email')
  const raw = lookupEmail.value.trim()
  if (!raw) {
    fieldErrors.lookup = t('auth.superAdmin.emailRequired')
    scrollIntoFormAndFocus(emailEl)
    return
  }
  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRe.test(raw)) {
    fieldErrors.lookup = t('auth.superAdmin.emailInvalid')
    scrollIntoFormAndFocus(emailEl)
    return
  }

  if (turnstileEnabled.value && !tsEmailToken.value) {
    formError.value = t('auth.turnstile.required')
    scrollIntoFormAndFocus(emailEl)
    return
  }

  loading.value = true
  formError.value = ''
  try {
    const { data } = await window.axios.post('/login/lookup', {
      email: raw,
      ...(tsEmailToken.value ? { turnstile_token: tsEmailToken.value } : {})
    })
    if (data.account_type === 'super_admin') {
      await router.push({ name: 'login-password', query: { email: data.email } })
      return
    }
    if (data.account_type === 'tenant' && data.login_url) {
      const url = new URL(data.login_url)
      url.searchParams.set('email', data.email)
      window.location.href = url.toString()
      return
    }
    formError.value = t('auth.superAdmin.lookupUnexpected')
    scrollIntoFormAndFocus(emailEl)
  } catch (err) {
    const msg = err.response?.data?.message || t('auth.superAdmin.lookupNotFound')
    formError.value = msg
    scrollIntoFormAndFocus(emailEl)
    tsEmailToken.value = ''
    turnstileEmailRef.value?.reset()
  } finally {
    loading.value = false
  }
}

async function handlePasswordLogin () {
  clearFieldErrors()
  formError.value = ''
  if (!form.email?.trim()) {
    void router.replace({ name: 'login' })
    return
  }
  if (!form.password) {
    fieldErrors.password = t('auth.superAdmin.passwordRequired')
    focusById('super-login-pw')
    return
  }

  if (turnstileEnabled.value && !tsPwToken.value) {
    formError.value = t('auth.turnstile.required')
    focusById('super-login-pw')
    return
  }

  loading.value = true
  try {
    const http = window.axios
    const response = await http.post('/login', {
      email: form.email,
      password: form.password,
      ...(tsPwToken.value ? { turnstile_token: tsPwToken.value } : {})
    })

    if (response.data.success) {
      localStorage.setItem('token', response.data.token)
      localStorage.setItem(
        'user',
        JSON.stringify({
          ...response.data.user,
          roles: response.data.user.roles
        })
      )
      http.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

      await Swal.fire({
        icon: 'success',
        title: 'Login Successful',
        text: 'Welcome back!',
        timer: 1500,
        showConfirmButton: false,
        position: 'top-end',
        toast: true
      })
      await router.push('/dashboard')
    } else {
      formError.value = response.data.message || 'Login failed.'
    }
  } catch (error) {
    const data = error.response?.data
    if (data?.errors?.email?.[0]) fieldErrors.lookup = data.errors.email[0]
    if (data?.errors?.password?.[0]) fieldErrors.password = data.errors.password[0]
    const msg = data?.message || 'Invalid credentials'
    formError.value = msg
    focusById('super-login-pw')
    await Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: msg,
      confirmButtonColor: '#dc3545'
    })
    tsPwToken.value = ''
    turnstilePwRef.value?.reset()
  } finally {
    loading.value = false
  }
}

async function onSubmit () {
  if (isEmailStep.value) {
    await runEmailLookup()
  } else {
    await handlePasswordLogin()
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

.restro-login__intro {
  text-align: left;
  margin-bottom: 1.1rem;
}

.restro-login__title {
  font-size: 1.28rem;
  font-weight: 800;
  color: #1a1a1a;
  margin: 0 0 0.5rem;
  line-height: 1.25;
  letter-spacing: -0.02em;
  text-align: left;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.restro-login__subtitle {
  margin: 0 0 0.5rem;
  font-size: 0.9rem;
  color: #5e5e5e;
  line-height: 1.5;
  text-align: left;
}

.restro-login__detail {
  margin: 0;
  font-size: 0.82rem;
  color: #6e6e6e;
  line-height: 1.55;
  text-align: left;
  text-wrap: pretty;
}

.restro-login__google {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.65rem;
  width: 100%;
  padding: 0.72rem 1rem;
  border: 1px solid #dadce0;
  border-radius: 999px;
  background: #fff;
  color: #3c4043;
  font-weight: 600;
  font-size: 0.9rem;
  text-decoration: none;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  transition: background 0.15s, box-shadow 0.15s, border-color 0.15s;
}

.restro-login__google:hover {
  background: #f8f9fa;
  border-color: #c6c6c6;
  box-shadow: 0 1px 3px rgba(60, 64, 67, 0.12);
  color: #202124;
}

.restro-login__google-icon {
  font-size: 1.1rem;
  color: #4285f4;
}

.restro-login__or {
  margin: 0;
  text-align: center;
  font-size: 0.78rem;
  color: #888;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  position: relative;
}

.restro-login__or span {
  background: #fff;
  padding: 0 0.65rem;
  position: relative;
  z-index: 1;
}

.restro-login__or::before {
  content: '';
  position: absolute;
  left: 0;
  right: 0;
  top: 50%;
  height: 1px;
  background: #e5e5e5;
  z-index: 0;
}

.restro-login__register {
  margin: 1.15rem 0 0;
  text-align: left;
  font-size: 0.88rem;
  line-height: 1.5;
  color: #444;
}

.restro-login__register-prompt {
  margin-right: 0.35rem;
}

.restro-login__register-link {
  font-weight: 600;
  color: #00844d;
  text-decoration: underline;
  text-underline-offset: 2px;
}

.restro-login__register-link:hover {
  color: #006a3e;
}

.restro-login__textbtn {
  margin: 0.25rem 0 0;
  width: 100%;
  padding: 0.5rem;
  border: none;
  background: none;
  font-size: 0.88rem;
  font-weight: 600;
  color: #00844d;
  cursor: pointer;
  font-family: inherit;
  text-decoration: underline;
  text-underline-offset: 2px;
}

.restro-login__textbtn:hover {
  color: #006a3e;
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
.restro-login .restro-form__input--readonly {
  background: #f4f4f4;
  color: #333;
}
.restro-login .restro-form__input--invalid {
  border-color: #c62828;
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
.restro-login .restro-form__field-error {
  margin: 0;
  font-size: 0.8rem;
  color: #6e1414;
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
</style>
