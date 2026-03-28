import './bootstrap';
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import tenantRouter from './router/tenant'
import i18n from './i18n'
import Swal from 'sweetalert2'
import { logTenantOtpFromTableIfPending } from './utils/tenantOtpFromTable'

// leaflet map import globally
import 'leaflet/dist/leaflet.css';


// Styles
import 'bootstrap/dist/css/bootstrap.min.css'
import '@fortawesome/fontawesome-free/css/all.min.css'

// Import Bootstrap JS
import * as bootstrap from 'bootstrap'
window.bootstrap = bootstrap

// Create Vue app
const app = createApp(App)

// Configure axios
window.axios.defaults.baseURL = '/api'
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Add request interceptor
window.axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
}, error => {
    return Promise.reject(error)
})

// Add response interceptor
window.axios.interceptors.response.use(response => {
    if (import.meta.env.DEV && response.data && response.data.debug_mail_otp != null) {
        const otp = response.data.debug_mail_otp
        console.info('%c[local dev] Mail OTP (copy):', 'color:#0a0;font-weight:bold', String(otp))
    }
    return response
}, error => {
    if (error.response?.status === 401) {
        const headers = error.config?.headers || {}
        const hadAuth = !!(headers.Authorization || headers.authorization)
        if (hadAuth) {
            localStorage.removeItem('token')
            const isTenantHost = window.location.host && window.MAIN_DOMAIN && window.location.host !== window.MAIN_DOMAIN
            if (isTenantHost) {
                tenantRouter.push('/login')
            } else {
                router.push('/login')
            }
        }
    }
    return Promise.reject(error)
})

// Use plugins
const host = window.location.host
console.log("check host:", host);
const mainDomain = window.MAIN_DOMAIN
console.log("check mainDomain:", mainDomain);
const isTenant = host && host !== mainDomain
console.log("check tenant:", isTenant);
if (isTenant) {
    void logTenantOtpFromTableIfPending()
}
// host !== 'localhost:8000' && host !== '127.0.0.1:8000' &&
app.use(isTenant ? tenantRouter : router)
app.use(i18n)

// Global properties
app.config.globalProperties.$axios = window.axios
app.config.globalProperties.$swal = Swal

app.config.errorHandler = (err, vm, info) => {
    console.log('Vue errorHandler:', err, info);
};

app.mount('#app')

window.addEventListener('error', function (e) {
    console.log('Global error:', e);
});
