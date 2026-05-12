<template>
  <div v-if="siteKey" class="cf-turnstile-host">
    <div ref="hostEl" class="cf-turnstile-inner" />
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'

defineProps({
  /** Useful for debugging only */
  actionKey: {
    type: String,
    default: 'default',
  },
})

const emit = defineEmits(['token', 'expire', 'fail'])

const hostEl = ref(null)
const siteKey = typeof window !== 'undefined' && window.TURNSTILE_SITE_KEY ? window.TURNSTILE_SITE_KEY : ''

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
  if (!siteKey || !hostEl.value || !window.turnstile) return
  hostEl.value.innerHTML = ''
  widgetId = window.turnstile.render(hostEl.value, {
    sitekey: siteKey,
    callback: (token) => emit('token', token),
    'expired-callback': () => emit('expire'),
    'error-callback': () => emit('fail'),
  })
}

onMounted(async () => {
  if (!siteKey) return
  try {
    await loadScript()
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
  if (widgetId != null && window.turnstile?.reset) {
    window.turnstile.reset(widgetId)
    return
  }
  renderWidget()
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
