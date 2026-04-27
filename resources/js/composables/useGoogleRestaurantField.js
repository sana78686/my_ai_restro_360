import { ref, onMounted, onUnmounted, watch } from 'vue'
import { loadGoogleMapsPlaces } from '../utils/googleMapsPlacesLoader.js'
import {
  isNewPlacesAutocompleteAvailable,
  createPlacesSessionToken,
  fetchPlacePredictionsNew,
  fetchPlaceDetailsFromPrediction
} from '../utils/googlePlacesProgrammatic.js'

/**
 * Restaurant name + Google Places establishment autocomplete, with silent geolocation
 * bias (same behaviour as tenant registration). No UI for location state.
 */
export function useGoogleRestaurantField () {
  const name = ref('')
  const placeId = ref(null)
  const placeDetails = ref(null)
  const isCustom = ref(false)
  const predictions = ref([])
  const showAutocomplete = ref(false)
  const userLocation = ref(null)
  /** Two-letter country from browser geolocation reverse-geocode (for form default). */
  const geoCountryIso2 = ref(null)
  /** True while reverse-geocoding the user’s position for a default country. */
  const countryDetecting = ref(false)
  const wrapRef = ref(null)
  const inputRef = ref(null)
  let debounceTimer = null
  let autocompleteSessionToken = null

  const getUserLocation = () => {
    if (!navigator.geolocation) return
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLocation.value = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        }
      },
      () => {},
      { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
    )
  }

  const runReverseGeocode = () => {
    if (geoCountryIso2.value || !userLocation.value) return
    if (!window.google?.maps?.Geocoder) return
    countryDetecting.value = true
    const geocoder = new window.google.maps.Geocoder()
    geocoder.geocode(
      { location: { lat: userLocation.value.lat, lng: userLocation.value.lng } },
      (results, status) => {
        countryDetecting.value = false
        if (status !== 'OK' || !results?.[0]) return
        for (const c of results[0].address_components) {
          if (c.types.includes('country')) {
            geoCountryIso2.value = c.short_name
            return
          }
        }
      }
    )
  }

  const handleInput = () => {
    if (debounceTimer) clearTimeout(debounceTimer)
    if (!name.value) {
      predictions.value = []
      showAutocomplete.value = false
      autocompleteSessionToken = null
      return
    }
    debounceTimer = setTimeout(async () => {
      if (!isNewPlacesAutocompleteAvailable()) return
      if (!autocompleteSessionToken) {
        autocompleteSessionToken = createPlacesSessionToken()
      }
      try {
        const list = await fetchPlacePredictionsNew({
          input: name.value,
          sessionToken: autocompleteSessionToken,
          userLocation: userLocation.value,
          includedRegionCodes: null,
          includedPrimaryTypes: null
        })
        predictions.value = list
        showAutocomplete.value = list.length > 0
      } catch (e) {
        console.error('Places autocomplete:', e)
        predictions.value = []
      }
    }, 200)
  }

  const selectPlace = async (prediction) => {
    name.value = prediction.description
    placeId.value = prediction.place_id
    isCustom.value = false
    showAutocomplete.value = false
    if (!prediction.placePrediction) return
    try {
      const place = await fetchPlaceDetailsFromPrediction(prediction.placePrediction)
      placeDetails.value = {
        lat: place.lat,
        lng: place.lng,
        formatted_address: place.formatted_address,
        postal_code: place.postal_code,
        country: place.country,
        country_iso: place.country_iso,
        state: place.state,
        city: place.city
      }
      autocompleteSessionToken = createPlacesSessionToken()
    } catch (e) {
      console.error('Place details:', e)
    }
  }

  const onBlur = () => {
    setTimeout(() => {
      showAutocomplete.value = false
      if (name.value && !placeId.value) {
        isCustom.value = true
      }
    }, 200)
  }

  const onDocClick = (event) => {
    if (wrapRef.value && !wrapRef.value.contains(event.target)) {
      showAutocomplete.value = false
    }
  }

  watch(
    userLocation,
    (loc) => {
      if (!loc) return
      loadGoogleMapsPlaces(() => {
        runReverseGeocode()
      })
    }
  )

  onMounted(() => {
    loadGoogleMapsPlaces(() => {})
    getUserLocation()
    document.addEventListener('click', onDocClick)
  })

  onUnmounted(() => {
    document.removeEventListener('click', onDocClick)
  })

  return {
    name,
    placeId,
    placeDetails,
    isCustom,
    predictions,
    showAutocomplete,
    userLocation,
    geoCountryIso2,
    countryDetecting,
    wrapRef,
    inputRef,
    handleInput,
    selectPlace,
    onBlur
  }
}
