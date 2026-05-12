/**
 * Site key from Blade window.TURNSTILE_SITE_KEY, with Vite fallback so the widget
 * works after `npm run build` / dev even if inline script order or config differs.
 */
export function getTurnstileSiteKey () {
  if (typeof window !== 'undefined' && window.TURNSTILE_SITE_KEY) {
    return String(window.TURNSTILE_SITE_KEY)
  }
  const v = import.meta.env.VITE_TURNSTILE_SITE_KEY
  return typeof v === 'string' && v ? v : ''
}

export function isTurnstileEnabled () {
  return getTurnstileSiteKey().length > 0
}
