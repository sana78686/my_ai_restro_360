<template>
  <div class="restro-login">
    <div class="restro-login__center">
      <h1 class="restro-login__title">{{ $t('auth.superAdmin.title') }}</h1>
      <p class="restro-login__subtitle">{{ $t('auth.superAdmin.subtitle') }}</p>
      <form class="restro-form" @submit.prevent="handleLogin" novalidate>
        <p v-if="formError" class="restro-form__msg restro-form__msg--error" role="alert">
          {{ formError }}
        </p>
        <div class="restro-form__stack">
          <div class="restro-form__field">
            <div class="restro-form__label-row">
              <label class="restro-form__label" for="super-login-email">{{ $t('auth.login.email') }}</label>
              <RestroInfoTip :text="$t('auth.superAdmin.tipEmail')" />
            </div>
            <input
              id="super-login-email"
              v-model.trim="form.email"
              type="email"
              class="restro-form__input"
              autocomplete="username"
              :class="{ 'restro-form__input--invalid': fieldErrors.email }"
              :placeholder="$t('auth.login.email')"
              :disabled="loading"
              required
            />
            <p v-if="fieldErrors.email" class="restro-form__field-error">{{ fieldErrors.email }}</p>
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
          <button class="restro-form__submit" type="submit" :disabled="loading">
            {{ loading ? '…' : $t('auth.login.submit') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import RestroInfoTip from '../../components/frontend/RestroInfoTip.vue'

const router = useRouter()
const loading = ref(false)
const showPassword = ref(false)
const formError = ref('')
const fieldErrors = reactive({
  email: '',
  password: ''
})

const form = reactive({
  email: '',
  password: ''
})

function clearFieldErrors() {
  fieldErrors.email = ''
  fieldErrors.password = ''
}

async function handleLogin() {
  loading.value = true
  formError.value = ''
  clearFieldErrors()
  try {
    const http = window.axios
    const response = await http.post('/login', {
      email: form.email,
      password: form.password
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
      router.push('/dashboard')
    } else {
      formError.value = response.data.message || 'Login failed.'
    }
  } catch (error) {
    const data = error.response?.data
    if (data?.errors?.email?.[0]) fieldErrors.email = data.errors.email[0]
    if (data?.errors?.password?.[0]) fieldErrors.password = data.errors.password[0]
    const msg = data?.message || 'Invalid credentials'
    formError.value = msg
    await Swal.fire({
      icon: 'error',
      title: 'Login Failed',
      text: msg,
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
