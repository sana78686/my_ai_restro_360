/** Shared loader: one script, `loading=async` + callback (matches Google’s recommended load pattern). */
const KEY = import.meta.env.VITE_GOOGLE_MAPS_API_KEY || 'AIzaSyCZDgTTb7vm0co-2yHGinkgSs_yDTNtbSo'
const CB = '__restroGmapsPlacesCb'
const SELECTOR = 'script[data-restro-places]'

const queue = []
let creating = false

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
  script.src = `https://maps.googleapis.com/maps/api/js?key=${KEY}&libraries=places&loading=async&callback=${CB}`
  document.head.appendChild(script)
}
