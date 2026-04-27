/**
 * Programmatic Places autocomplete + details using the Places API (New).
 * Avoids legacy AutocompleteService / PlacesService (deprecated warnings for new API keys).
 */

export function isNewPlacesAutocompleteAvailable () {
  return Boolean(
    typeof google !== 'undefined' &&
    google.maps?.places?.AutocompleteSuggestion?.fetchAutocompleteSuggestions
  )
}

export function createPlacesSessionToken () {
  return new google.maps.places.AutocompleteSessionToken()
}

/**
 * @param {object} opts
 * @param {string} opts.input
 * @param {google.maps.places.AutocompleteSessionToken} opts.sessionToken
 * @param {{ lat: number, lng: number }|null} [opts.userLocation]
 * @param {string[]|null} [opts.includedRegionCodes] CLDR region codes, e.g. ['us']
 * @param {string[]|null} [opts.includedPrimaryTypes] Place primary types (optional)
 * @returns {Promise<Array<{ description: string, place_id: string, placePrediction: object }>>}
 */
export async function fetchPlacePredictionsNew (opts) {
  const {
    input,
    sessionToken,
    userLocation,
    includedRegionCodes,
    includedPrimaryTypes
  } = opts

  const request = {
    input,
    sessionToken
  }

  if (includedPrimaryTypes?.length) {
    request.includedPrimaryTypes = includedPrimaryTypes
  }
  if (includedRegionCodes?.length) {
    request.includedRegionCodes = includedRegionCodes
  }
  if (userLocation) {
    const pad = 0.35
    const { lat, lng } = userLocation
    request.locationBias = {
      south: lat - pad,
      north: lat + pad,
      west: lng - pad,
      east: lng + pad
    }
  }

  const { suggestions } =
    await google.maps.places.AutocompleteSuggestion.fetchAutocompleteSuggestions(request)

  return (suggestions || [])
    .map((s) => s.placePrediction)
    .filter(Boolean)
    .map((pp) => ({
      description: pp.text?.text ?? '',
      place_id: pp.placeId,
      placePrediction: pp
    }))
}

function displayNameToString (displayName) {
  if (displayName == null) return ''
  if (typeof displayName === 'string') return displayName
  return displayName.text ?? ''
}

/** @param {object} placePrediction — `PlacePrediction` from the new Autocomplete API */
export async function fetchPlaceDetailsFromPrediction (placePrediction) {
  const place = placePrediction.toPlace()
  await place.fetchFields({
    fields: ['displayName', 'location', 'formattedAddress', 'addressComponents', 'id']
  })

  const loc = place.location
  let lat
  let lng
  if (loc) {
    lat = typeof loc.lat === 'function' ? loc.lat() : loc.lat
    lng = typeof loc.lng === 'function' ? loc.lng() : loc.lng
  }

  const addressComponents = {}
  const comps = place.addressComponents || []
  for (const component of comps) {
    const type = component.types?.[0]
    if (!type) continue
    const longName = component.longText ?? component.long_name
    addressComponents[type] = longName
    if (component.types.includes('country')) {
      addressComponents.country_code = component.shortText ?? component.short_name
    }
  }

  const nameStr = displayNameToString(place.displayName)

  return {
    lat,
    lng,
    formatted_address: place.formattedAddress,
    postal_code: addressComponents.postal_code || '',
    country: addressComponents.country || '',
    country_iso: addressComponents.country_code || null,
    state: addressComponents.administrative_area_level_1 || '',
    city: addressComponents.locality || addressComponents.administrative_area_level_2 || '',
    name: nameStr
  }
}
