/** Shared loader: one script, `loading=async` + callback (matches Google’s recommended load pattern). */
const CB = '__restroGmapsPlacesCb'
const SELECTOR = 'script[data-restro-places]'

const queue = []
let creating = false

/**
 * Maps JavaScript API key from `.env` (`VITE_GOOGLE_MAPS_API_KEY`). No fallback — expired or
 * leaked keys must not be committed.
 */
export function getGoogleMapsApiKey () {
  return String(import.meta.env.VITE_GOOGLE_MAPS_API_KEY || '').trim()
}

function runQueue () {
  const q = queue.splice(0, queue.length)
  q.forEach((fn) => {
    try {
      fn()
    } catch (e) {
      console.error(e)
    }
  })
}

/**
 * @param {() => void} done — runs when `google.maps.places` is available
 */
export function loadGoogleMapsPlaces (done) {
  const key = getGoogleMapsApiKey()
  if (!key) {
    console.warn(
      '[AiRestro360] Maps autocomplete is disabled: set VITE_GOOGLE_MAPS_API_KEY in .env, enable Maps JavaScript API + Places API in Google Cloud Console, then restart Vite (npm run dev / build).'
    )
    return
  }

  if (window.google?.maps?.places) {
    done()
    return
  }
  queue.push(done)

  if (document.querySelector(SELECTOR)) {
    const t = setInterval(() => {
      if (window.google?.maps?.places) {
        clearInterval(t)
        runQueue()
      }
    }, 40)
    return
  }

  if (creating) {
    return
  }
  creating = true

  window[CB] = () => {
    creating = false
    runQueue()
    try {
      delete window[CB]
    } catch {
      window[CB] = undefined
    }
  }
  const script = document.createElement('script')
  script.setAttribute('data-restro-places', '1')
  script.async = true
  script.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(key)}&libraries=places&loading=async&callback=${CB}`
  document.head.appendChild(script)
}
