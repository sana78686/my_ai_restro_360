import { ref, onMounted, onUnmounted, watch } from 'vue'
import { loadGoogleMapsPlaces } from '../utils/googleMapsPlacesLoader.js'

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
  let autocompleteService = null
  let placesService = null

  const initServices = () => {
    if (!window.google?.maps?.places) return
    autocompleteService = new window.google.maps.places.AutocompleteService()
    placesService = new window.google.maps.places.PlacesService(document.createElement('div'))
  }

  const loadScript = () => {
    loadGoogleMapsPlaces(initServices)
  }

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
        return
      }
      debounceTimer = setTimeout(() => {
        if (!autocompleteService) return
        const request = {
          input: name.value,
          types: ['establishment']
        }
      if (userLocation.value) {
        request.location = new window.google.maps.LatLng(userLocation.value.lat, userLocation.value.lng)
        request.radius = 50000
      }
      autocompleteService.getPlacePredictions(request, (results, status) => {
        if (status === window.google.maps.places.PlacesServiceStatus.OK) {
          predictions.value = results
          showAutocomplete.value = true
        } else {
          predictions.value = []
        }
      })
    }, 200)
  }

  const selectPlace = (prediction) => {
    name.value = prediction.description
    placeId.value = prediction.place_id
    isCustom.value = false
    showAutocomplete.value = false
    if (!placesService) return
    placesService.getDetails(
      {
        placeId: prediction.place_id,
        fields: ['name', 'geometry', 'formatted_address', 'address_components', 'place_id']
      },
      (place, status) => {
        if (status !== window.google.maps.places.PlacesServiceStatus.OK || !place) return
        const addressComponents = {}
        place.address_components.forEach((component) => {
          const type = component.types[0]
          addressComponents[type] = component.long_name
          if (component.types.includes('country')) {
            addressComponents.country_code = component.short_name
          }
        })
        placeDetails.value = {
          lat: place.geometry.location.lat(),
          lng: place.geometry.location.lng(),
          formatted_address: place.formatted_address,
          postal_code: addressComponents.postal_code || '',
          country: addressComponents.country || '',
          country_iso: addressComponents.country_code || null,
          state: addressComponents.administrative_area_level_1 || '',
          city: addressComponents.locality || addressComponents.administrative_area_level_2 || ''
        }
      }
    )
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
        initServices()
        runReverseGeocode()
      })
    }
  )

  onMounted(() => {
    loadScript()
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
