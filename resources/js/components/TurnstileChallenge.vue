<template>
  <div v-if="siteKey" class="cf-turnstile-host">
    <div ref="hostEl" class="cf-turnstile-inner" />
  </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { getTurnstileSiteKey } from '../utils/turnstile.js'

defineProps({
  actionKey: {
    type: String,
    default: 'default',
  },
})

const emit = defineEmits(['token', 'expire', 'fail'])

const hostEl = ref(null)
/** Resolved at runtime so Blade + VITE_* both work */
const siteKey = ref('')

let widgetId = null

function loadScript () {
  if (typeof window === 'undefined') return Promise.resolve()
  if (window.turnstile) return Promise.resolve()
  return new Promise((resolve, reject) => {
    const s = document.createElement('script')
    s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit'
    s.async = true
    s.defer = true
    s.onload = () => resolve()
    s.onerror = () => reject(new Error('Turnstile script failed to load'))
    document.head.appendChild(s)
  })
}

function renderWidget () {
  const key = siteKey.value
  if (!key || !hostEl.value || !window.turnstile) return
  hostEl.value.innerHTML = ''
  widgetId = window.turnstile.render(hostEl.value, {
    sitekey: key,
    callback: (token) => emit('token', token),
    'expired-callback': () => emit('expire'),
    'error-callback': () => emit('fail'),
  })
}

onMounted(async () => {
  siteKey.value = getTurnstileSiteKey()
  if (!siteKey.value) return
  try {
    await loadScript()
    await nextTick()
    renderWidget()
  } catch {
    emit('fail')
  }
})

onBeforeUnmount(() => {
  if (widgetId != null && window.turnstile?.remove) {
    try {
      window.turnstile.remove(widgetId)
    } catch {
      /* ignore */
    }
  }
  widgetId = null
})

function reset () {
  siteKey.value = getTurnstileSiteKey()
  if (!siteKey.value) return
  if (widgetId != null && window.turnstile?.reset) {
    window.turnstile.reset(widgetId)
    return
  }
  void nextTick(() => renderWidget())
}

defineExpose({ reset })
</script>

<style scoped>
.cf-turnstile-host {
  display: flex;
  justify-content: center;
  margin: 0.35rem 0 0.75rem;
  min-height: 65px;
}
.cf-turnstile-inner {
  min-height: 65px;
}
</style>
