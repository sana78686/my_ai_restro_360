<template>
  <!-- Banner/Breadcrumb Section (Hero) -->
  <section
    class="mt-5 register-banner-section position-relative d-flex align-items-center justify-content-center"
    style="min-height: 320px; width: 100vw; left: 50%; right: 50%; margin-left: -50vw; margin-right: -50vw; background: url('/Mox_files/homepage.jpg') center center/cover no-repeat;"
  >
    <div
      class="position-absolute top-0 start-0 w-100 h-100"
      style="background: rgba(0,0,0,0.55); z-index: 1;"
    ></div>
    <div class="container position-relative text-center" style="z-index: 2;">
      <h1 class="text-white fw-bold mb-2" style="font-size: 2.8rem;">
        {{ $t('auth.register.bannerTitle') }}
      </h1>
      <div
        class="text-white mb-2"
        style="font-size: 1.1rem; font-weight: 500; letter-spacing: 0.5px;"
      >
        {{ $t('auth.register.bannerSubtitle') }}
      </div>
      <!-- Breadcrumbs -->
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb justify-content-center mb-0" style="background: transparent;">
          <li class="breadcrumb-item">
            <router-link to="/" class="text-white-50">{{ $t('auth.register.pageTitle') }}</router-link>
          </li>
          <li class="breadcrumb-item active text-white" aria-current="page">{{ $t('auth.register.register') }}</li>
        </ol>
      </nav>
    </div>
  </section>

  <div
    class="tenant-register py-5"
    style="background: #fafafa; min-height: 100vh; margin-top: -60px;"
  >
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
          <div
            class="card shadow-lg border-0 bg-white"
            style="border-radius: 0 0 16px 16px; background: #fff !important;"
          >
            <div class="card-top-red-line"></div>
            <div class="card-body p-4 p-md-5">
              <div class="text-center mb-3" style="font-size: 1.1rem; color: #222;">
                {{  $t('auth.register.intro') }}
                <br>

                <!-- Location Status -->
                <div class="mb-3">
                  <div v-if="locationLoading" class="text-primary">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    <small>{{  $t('auth.register.locationGetting') }}</small>
                  </div>
                  <div v-else-if="userLocation" class="text-success">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <small>
                      {{  $t('auth.register.locationText') }}
                      ({{ userLocation.lat.toFixed(4) }}, {{ userLocation.lng.toFixed(4) }})
                    </small>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-primary ms-2"
                      @click="getUserLocation"
                      :title="$t('common.refreshLocation')"
                    >
                      <i class="fas fa-sync-alt"></i>
                    </button>
                  </div>
                  <div v-else-if="locationError" class="text-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>{{ locationError }}</small>
                    <button
                      type="button"
                      class="btn btn-sm btn-outline-warning ms-2"
                      @click="getUserLocation"
                      :title="$t('common.tryAgain')"
                    >
                      <i class="fas fa-redo"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Selected Plan Info Banner -->
              <div v-if="selectedPlanInfo" class="alert alert-info mb-4" style="background: #e3f2fd; border-left: 4px solid #c62828; border-radius: 4px;">
                <div class="d-flex align-items-center">
                  <i class="fas fa-info-circle me-2" style="font-size: 1.2rem; color: #c62828;"></i>
                  <div class="flex-grow-1">
                    <strong style="color: #c62828;">Selected Plan: {{ selectedPlanInfo.name }}</strong>
                    <p class="mb-0 mt-1" style="font-size: 0.9rem; color: #666;">
                     Register your restaurant and complete payment to activate your subscription.
                    </p>
                  </div>
                </div>
              </div>

              <form @submit.prevent="handleSubmit">
                <div class="row g-3">
                  <!-- Business Name -->
                  <div class="col-md-6">
                    <label for="businessName" class="form-label fw-semibold">{{  $t('auth.register.nameTitle') }}*</label>
                    <div class="position-relative">
                      <input
                        type="text"
                        class="form-control"
                        id="businessName"
                        v-model="form.businessName"
                        ref="businessNameInput"
                        required
                        @input="handleInput"
                        @blur="handleBusinessNameBlur"
                        :placeholder="userLocation ?  $t('auth.register.searchNearRestaurant') :  $t('auth.register.searchYourRestaurant')"
                      >
                      <!-- Autocomplete Results -->
                      <div v-if="showAutocomplete && predictions.length > 0" class="autocomplete-results">
                        <div
                          v-for="prediction in predictions"
                          :key="prediction.place_id"
                          class="autocomplete-item"
                          @click="selectPlace(prediction)"
                        >
                          {{ prediction.description }}
                        </div>
                      </div>
                    </div>
                    <small class="text-muted">{{  $t('auth.register.smallPlaceholder') }}</small>
                  </div>

                  <!-- Subdomain -->
                  <div class="col-md-6">
                    <label for="subdomain" class="form-label fw-semibold">{{  $t('auth.register.subdomainTitle') }}*</label>
                    <div class="input-group">
                      <input
                        type="text"
                        class="form-control"
                        id="subdomain"
                        v-model="form.subdomain"
                        required
                        pattern="[a-z0-9-]+"
                        :title=" $t('auth.register.subdomainTitle')"
                        :placeholder=" $t('auth.register.subdomainPlaceholder')"
                      >
                      <span class="input-group-text">.apimstec.com</span>
                    </div>
                    <small class="text-muted">{{  $t('auth.register.subdomainSmallPlaceholder') }}</small>
                  </div>

                  <!-- Owner Name -->
                  <div class="col-md-6">
                    <label for="ownerName" class="form-label fw-semibold">{{  $t('auth.register.ownerTitle') }}*</label>
                    <input
                      type="text"
                      class="form-control"
                      id="ownerName"
                      v-model="form.ownerName"
                      required
                      :placeholder=" $t('auth.register.ownerPlaceholder')"
                    >
                  </div>

                  <!-- Email -->
                  <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">{{  $t('auth.register.emailTitle') }}*</label>
                    <input
                      type="email"
                      class="form-control"
                      id="email"
                      v-model="form.public_email"
                      required
                      pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                      :title=" $t('auth.register.emailTitle')"
                      :placeholder=" $t('auth.register.emailPlaceholder')"
                    >
                    <small class="text-muted">{{  $t('auth.register.emailSmallPlaceholder') }}</small>
                  </div>

                  <!-- Phone -->
                  <div class="col-md-7">
                    <label for="phone" class="form-label fw-semibold">{{  $t('auth.register.phoneTitle') }}*</label>
                    <div class="phone-input-container d-flex align-items-stretch gap-2">
                      <div
                        class="country-selector d-flex align-items-center px-2 py-1 border rounded bg-white me-2"
                        @click="showCountryModal = true"
                        style="cursor:pointer; min-width:100px;"
                      >
                        <div v-if="countryDetecting" class="d-flex align-items-center">
                          <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                          <small class="text-muted">{{  $t('auth.register.phoneDetecting') }}</small>
                        </div>
                        <div v-else class="d-flex align-items-center">
                          <img :src="selectedCountryFlag" :alt="selectedCountry.name" class="country-flag me-2" v-if="selectedCountryFlag">
                          <span class="country-code">{{ selectedCountry.dialCode || '+1' }}</span>
                          <i class="fas fa-chevron-down ms-1"></i>
                        </div>
                      </div>
                      <input
                        type="tel"
                        class="form-control phone-number-input"
                        v-model="form.phoneNumber"
                        :placeholder=" $t('auth.register.phonePlaceholder')"
                        required
                        @input="handlePhoneInput"
                      >
                    </div>

                    <!-- Country Modal -->
                    <div class="country-modal" v-if="showCountryModal" @click.self="showCountryModal = false">
                      <div class="country-modal-content">
                        <div class="country-modal-header d-flex justify-content-between align-items-center">
                          <h5 class="mb-0">{{  $t('auth.register.countryTitle') }}</h5>
                          <button type="button" class="btn-close" @click="showCountryModal = false"></button>
                        </div>
                        <div class="country-search p-2 border-bottom">
                          <div class="input-group">
                            <span class="input-group-text bg-white">
                              <i class="fas fa-search"></i>
                            </span>
                            <input
                              type="text"
                              class="form-control"
                              v-model="countrySearch"
                              :placeholder=" $t('auth.register.countryPlaceholder')"
                              autofocus
                            >
                          </div>
                        </div>
                        <div class="country-list" style="max-height: 300px; overflow-y: auto;">
                          <div
                            v-for="country in filteredCountries"
                            :key="country.code"
                            class="country-item d-flex align-items-center px-2 py-2 border-bottom"
                            @click="selectCountry(country)"
                            style="cursor:pointer;"
                          >
                            <img :src="country.flag" :alt="country.name" class="country-flag me-2">
                            <div class="country-info flex-grow-1">
                              <span class="country-name">{{ country.name }}</span>
                              <span class="country-dial-code ms-2 text-muted">{{ country.dialCode }}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Password -->
                  <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">{{  $t('auth.register.passwordTitle') }}*</label>
                    <input
                      type="password"
                      class="form-control"
                      id="password"
                      v-model="form.password"
                      required
                      minlength="8"
                      :placeholder=" $t('auth.register.passwordPlaceholder')"
                    >
                    <small class="text-muted">{{  $t('auth.register.passwordSmallPlaceholder') }}</small>
                  </div>

                  <!-- Confirm Password -->
                  <div class="col-md-6">
                    <label for="passwordConfirmation" class="form-label fw-semibold">{{  $t('auth.register.confirmPassword') }}*</label>
                    <input
                      type="password"
                      class="form-control"
                      id="passwordConfirmation"
                      v-model="form.passwordConfirmation"
                      required
                      :placeholder=" $t('auth.register.passwordConfirmPlaceholder')"
                    >
                  </div>

                  <!-- Message -->
                  <div class="col-12">
                    <label for="message" class="form-label fw-semibold">{{ $t('auth.register.messageOptional') }}</label>
                    <textarea class="form-control" id="message" v-model="form.message" :placeholder="$t('auth.register.messageOptionalPlaceholder')" rows="3"></textarea>
                  </div>

                  <!-- Terms -->
                  <div class="col-12">
                    <div class="form-check">
                      <input
                        type="checkbox"
                        class="form-check-input"
                        id="terms"
                        v-model="form.acceptTerms"
                        required
                      >
                      <label class="form-check-label" for="terms">
                        {{ $t('auth.register.agreeTerms') }}
                        <a href="#" target="_blank">{{ $t('auth.register.termsAndConditions') }}</a>
                      </label>
                    </div>
                  </div>

                  <!-- Submit -->
                  <div class="col-12 text-center mt-3">
                    <button
                      type="submit"
                      class="btn btn-danger px-5 py-2 fw-bold"
                      style="border-radius: 6px; min-width: 200px;"
                      :disabled="loading"
                    >
                      <template v-if="loading">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        {{ $t('common.processing') }}
                      </template>
                      <template v-else>
                        {{ $t('auth.register.registerBtn') }}
                      </template>
                    </button>
                  </div>
                </div>
              </form>

              <!-- Contact -->
              <div class="text-center mt-4" style="color: #c62828; font-size: 1rem;">
                {{ $t('auth.register.contactInfo') }}
                <b>{{ contactPhone }}</b>
                {{ $t('auth.register.contactInfoSuffix') }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import { ref, onMounted, onUnmounted, computed, watch }
from 'vue'
// import { useI18n } from 'vue-i18n'
// const { $t} = useI18n()
import { useRouter } from 'vue-router'
import Swal from 'sweetalert2'
import { Tooltip } from 'bootstrap'
import { VueTelInput } from 'vue-tel-input'

export default {
  name: 'TenantRegister',
  components: {
    VueTelInput
  },

  setup() {
    const contactPhone = import.meta.env.VITE_CONTACT_PHONE;
    const router = useRouter()
    const loading = ref(false)
    const predictions = ref([])
    const showAutocomplete = ref(false)
    const businessNameInput = ref(null)
    const autocompleteService = ref(null)
    const placesService = ref(null)
    let debounceTimer = null
    let tooltips = []
    const showCountryModal = ref(false)
    const countrySearch = ref('')

    // Selected plan info from sessionStorage (if user came from pricing page)
    const selectedPlanInfo = ref(null)

    const selectedCountry = ref({
      name: 'United States',
      code: 'US',
      dialCode: '+1',
      flag: 'https://flagcdn.com/us.svg'
    })

    const countries = ref([
  { name: 'United States', code: 'US', dialCode: '+1', flag: 'https://flagcdn.com/us.svg' },
  { name: 'United Kingdom', code: 'GB', dialCode: '+44', flag: 'https://flagcdn.com/gb.svg' },
  { name: 'Canada', code: 'CA', dialCode: '+1', flag: 'https://flagcdn.com/ca.svg' },
  { name: 'Australia', code: 'AU', dialCode: '+61', flag: 'https://flagcdn.com/au.svg' },
  { name: 'India', code: 'IN', dialCode: '+91', flag: 'https://flagcdn.com/in.svg' },
  { name: 'Pakistan', code: 'PK', dialCode: '+92', flag: 'https://flagcdn.com/pk.svg' },
  { name: 'Germany', code: 'DE', dialCode: '+49', flag: 'https://flagcdn.com/de.svg' },
  { name: 'France', code: 'FR', dialCode: '+33', flag: 'https://flagcdn.com/fr.svg' },
  { name: 'Italy', code: 'IT', dialCode: '+39', flag: 'https://flagcdn.com/it.svg' },
  { name: 'Spain', code: 'ES', dialCode: '+34', flag: 'https://flagcdn.com/es.svg' },
  { name: 'China', code: 'CN', dialCode: '+86', flag: 'https://flagcdn.com/cn.svg' },
  { name: 'Japan', code: 'JP', dialCode: '+81', flag: 'https://flagcdn.com/jp.svg' },
  { name: 'South Korea', code: 'KR', dialCode: '+82', flag: 'https://flagcdn.com/kr.svg' },
  { name: 'Brazil', code: 'BR', dialCode: '+55', flag: 'https://flagcdn.com/br.svg' },
  { name: 'Mexico', code: 'MX', dialCode: '+52', flag: 'https://flagcdn.com/mx.svg' },
  { name: 'Russia', code: 'RU', dialCode: '+7', flag: 'https://flagcdn.com/ru.svg' },
  { name: 'Netherlands', code: 'NL', dialCode: '+31', flag: 'https://flagcdn.com/nl.svg' },
  { name: 'Switzerland', code: 'CH', dialCode: '+41', flag: 'https://flagcdn.com/ch.svg' },
  { name: 'Sweden', code: 'SE', dialCode: '+46', flag: 'https://flagcdn.com/se.svg' },
  { name: 'Norway', code: 'NO', dialCode: '+47', flag: 'https://flagcdn.com/no.svg' },
  { name: 'Denmark', code: 'DK', dialCode: '+45', flag: 'https://flagcdn.com/dk.svg' },
  { name: 'Finland', code: 'FI', dialCode: '+358', flag: 'https://flagcdn.com/fi.svg' },
  { name: 'Belgium', code: 'BE', dialCode: '+32', flag: 'https://flagcdn.com/be.svg' },
  { name: 'Austria', code: 'AT', dialCode: '+43', flag: 'https://flagcdn.com/at.svg' },
  { name: 'New Zealand', code: 'NZ', dialCode: '+64', flag: 'https://flagcdn.com/nz.svg' },
  { name: 'Saudi Arabia', code: 'SA', dialCode: '+966', flag: 'https://flagcdn.com/sa.svg' },
  { name: 'United Arab Emirates', code: 'AE', dialCode: '+971', flag: 'https://flagcdn.com/ae.svg' },
  { name: 'Qatar', code: 'QA', dialCode: '+974', flag: 'https://flagcdn.com/qa.svg' },
  { name: 'Turkey', code: 'TR', dialCode: '+90', flag: 'https://flagcdn.com/tr.svg' },
  { name: 'South Africa', code: 'ZA', dialCode: '+27', flag: 'https://flagcdn.com/za.svg' },
  { name: 'Egypt', code: 'EG', dialCode: '+20', flag: 'https://flagcdn.com/eg.svg' },
  { name: 'Nigeria', code: 'NG', dialCode: '+234', flag: 'https://flagcdn.com/ng.svg' },
  { name: 'Kenya', code: 'KE', dialCode: '+254', flag: 'https://flagcdn.com/ke.svg' },
  { name: 'Argentina', code: 'AR', dialCode: '+54', flag: 'https://flagcdn.com/ar.svg' },
  { name: 'Chile', code: 'CL', dialCode: '+56', flag: 'https://flagcdn.com/cl.svg' },
  { name: 'Colombia', code: 'CO', dialCode: '+57', flag: 'https://flagcdn.com/co.svg' },
  { name: 'Philippines', code: 'PH', dialCode: '+63', flag: 'https://flagcdn.com/ph.svg' },
  { name: 'Malaysia', code: 'MY', dialCode: '+60', flag: 'https://flagcdn.com/my.svg' },
  { name: 'Singapore', code: 'SG', dialCode: '+65', flag: 'https://flagcdn.com/sg.svg' },
  { name: 'Thailand', code: 'TH', dialCode: '+66', flag: 'https://flagcdn.com/th.svg' },
  { name: 'Vietnam', code: 'VN', dialCode: '+84', flag: 'https://flagcdn.com/vn.svg' },
  { name: 'Indonesia', code: 'ID', dialCode: '+62', flag: 'https://flagcdn.com/id.svg' },
  { name: 'Bangladesh', code: 'BD', dialCode: '+880', flag: 'https://flagcdn.com/bd.svg' },
  { name: 'Sri Lanka', code: 'LK', dialCode: '+94', flag: 'https://flagcdn.com/lk.svg' },
  { name: 'Portugal', code: 'PT', dialCode: '+351', flag: 'https://flagcdn.com/pt.svg' },
  { name: 'Greece', code: 'GR', dialCode: '+30', flag: 'https://flagcdn.com/gr.svg' },
  { name: 'Ireland', code: 'IE', dialCode: '+353', flag: 'https://flagcdn.com/ie.svg' },
//   { name: 'Israel', code: 'IL', dialCode: '+972', flag: 'https://flagcdn.com/il.svg' }
])


    const filteredCountries = computed(() => {
      if (!countrySearch.value) return countries.value
      const search = countrySearch.value.toLowerCase()
      return countries.value.filter(country =>
        country.name.toLowerCase().includes(search) ||
        country.dialCode.includes(search)
      )
    })

    const form = ref({
      businessName: '',
      subdomain: '',
      ownerName: '',
      public_email: '',
      public_phone: '',
      phoneNumber: '',
      phoneCountry: '',
      password: '',
      passwordConfirmation: '',
      acceptTerms: false,
      placeId: null,
      location: null,
      isCustomLocation: false
    })

    // User location state
    const userLocation = ref(null)
    const locationLoading = ref(false)
    const locationError = ref(null)
    const countryDetecting = ref(false)

    // Update public_phone with country code and formatted number (must be defined before watch — const is not hoisted)
    const updatePublicPhone = () => {
      const dial = selectedCountry.value?.dialCode ?? ''
      const cleanedNumber = (form.value.phoneNumber || '').replace(/\D/g, '')
      if (cleanedNumber && dial) {
        form.value.public_phone = dial + cleanedNumber
      } else {
        form.value.public_phone = ''
      }
    }

    // Watch for phone number changes to update public_phone
    watch([() => form.value.phoneNumber, selectedCountry], () => {
      updatePublicPhone()
    }, { immediate: true })

    // Initialize tooltips
    const initTooltips = () => {
      tooltips.forEach(tooltip => tooltip.dispose())
      tooltips = []

      document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        tooltips.push(new Tooltip(el))
      })
    }

    const initGooglePlaces = () => {
      if (window.google && window.google.maps && window.google.maps.places) {
        autocompleteService.value = new window.google.maps.places.AutocompleteService()
        placesService.value = new window.google.maps.places.PlacesService(document.createElement('div'))
      }
    }

    const loadGooglePlacesScript = () => {
      const script = document.createElement('script')
      script.src = `https://maps.google.com/maps/api/js?key=AIzaSyCZDgTTb7vm0co-2yHGinkgSs_yDTNtbSo&libraries=places`
      script.async = true
      script.defer = true
      script.onload = initGooglePlaces
      document.head.appendChild(script)
    }

    // Get user's current location
    const getUserLocation = () => {
      locationLoading.value = true
      locationError.value = null

      if (!navigator.geolocation) {
        locationError.value = 'Geolocation is not supported by this browser.'
        locationLoading.value = false
        return
      }

      navigator.geolocation.getCurrentPosition(
        (position) => {
          userLocation.value = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
          }
          locationLoading.value = false
          console.log('User location obtained:', userLocation.value)

          // Get country from coordinates and set phone country
          getCountryFromCoordinates(userLocation.value.lat, userLocation.value.lng)
        },
        (error) => {
          locationError.value = 'Unable to get your location. Please enable location services.'
          locationLoading.value = false
          console.error('Location error:', error)
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 300000
        }
      )
    }

    // Get country from coordinates using reverse geocoding
    const getCountryFromCoordinates = (lat, lng) => {
      countryDetecting.value = true

      if (window.google && window.google.maps) {
        const geocoder = new window.google.maps.Geocoder()
        geocoder.geocode(
          { location: { lat, lng } },
          (results, status) => {
            countryDetecting.value = false
            if (status === 'OK' && results[0]) {
              const addressComponents = results[0].address_components
              let countryCode = null

              for (const component of addressComponents) {
                if (component.types.includes('country')) {
                  countryCode = component.short_name
                  break
                }
              }

              if (countryCode) {
                console.log('Country detected via Google Maps:', countryCode)
                setCountryByCode(countryCode)
                return
              }
            }
            fallbackGeocoding(lat, lng)
          }
        )
      } else {
        console.log('Google Maps not loaded, using fallback geocoding...')
        fallbackGeocoding(lat, lng)
      }
    }

    // Fallback geocoding using free service
    const fallbackGeocoding = async (lat, lng) => {
      try {
        const response = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=en`)
        const data = await response.json()

        if (data.countryCode) {
          console.log('Country detected via fallback service:', data.countryCode)
          setCountryByCode(data.countryCode)
        }
      } catch (error) {
        console.error('Fallback geocoding failed:', error)
      } finally {
        countryDetecting.value = false
      }
    }

    // Set country by country code
    const setCountryByCode = (countryCode) => {
      const country = countries.value.find(c => c.code === countryCode)
      if (country) {
        selectedCountry.value = country
        form.value.phoneCountry = country.code
        console.log('Country set to:', country.name, 'with dial code:', country.dialCode)
      } else {
        console.log('Country not found for code:', countryCode)
      }
    }

    const handleInput = () => {
      if (debounceTimer) clearTimeout(debounceTimer)

      if (!form.value.businessName) {
        predictions.value = []
        showAutocomplete.value = false
        return
      }

      debounceTimer = setTimeout(() => {
        if (autocompleteService.value) {
          const request = {
            input: form.value.businessName,
            types: ['establishment'],
            componentRestrictions: { country: 'us' }
          }

          if (userLocation.value) {
            request.location = new window.google.maps.LatLng(userLocation.value.lat, userLocation.value.lng)
            request.radius = 50000
            delete request.componentRestrictions
          }

          autocompleteService.value.getPlacePredictions(
            request,
            (results, status) => {
              if (status === window.google.maps.places.PlacesServiceStatus.OK) {
                predictions.value = results
                showAutocomplete.value = true
              }
            }
          )
        }
      }, 300)
    }

    // Handle when user clicks away from business name input
    const handleBusinessNameBlur = () => {
      // Small delay to allow for place selection
      setTimeout(() => {
        showAutocomplete.value = false
        // If no place was selected and business name is not empty, mark as custom input
        if (form.value.businessName && !form.value.placeId) {
          form.value.isCustomLocation = true
          // Auto-generate subdomain from custom business name
          form.value.subdomain = form.value.businessName
            .toLowerCase()
            .replace(/[^a-z0-9]/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '')
        }
      }, 200)
    }

    const selectPlace = (prediction) => {
      form.value.businessName = prediction.description
      form.value.placeId = prediction.place_id
      form.value.isCustomLocation = false
      showAutocomplete.value = false

      // Get additional place details
      if (placesService.value) {
        placesService.value.getDetails(
          {
            placeId: prediction.place_id,
            fields: [
              'name',
              'geometry',
              'formatted_address',
              'address_components',
              'place_id'
            ]
          },
          (place, status) => {
            if (status === window.google.maps.places.PlacesServiceStatus.OK) {
              const addressComponents = {}
              place.address_components.forEach(component => {
                const type = component.types[0]
                addressComponents[type] = component.long_name
                if (type === 'country') {
                  addressComponents.country_code = component.short_name
                }
              })

              form.value.location = {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng(),
                formatted_address: place.formatted_address,
                postal_code: addressComponents.postal_code || '',
                country: addressComponents.country || '',
                state: addressComponents.administrative_area_level_1 || '',
                city: addressComponents.locality || addressComponents.administrative_area_level_2 || ''
              }

              // Auto-generate subdomain from business name
              form.value.subdomain = place.name
                .toLowerCase()
                .replace(/[^a-z0-9]/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-|-$/g, '')
            }
          }
        )
      }
    }

    // Close autocomplete when clicking outside
    const handleClickOutside = (event) => {
      if (businessNameInput.value && !businessNameInput.value.contains(event.target)) {
        showAutocomplete.value = false
      }
    }

    const formatPhoneNumber = (number) => {
      const cleaned = number.replace(/\D/g, '')

      if (selectedCountry.value.code === 'US' || selectedCountry.value.code === 'CA') {
        if (cleaned.length <= 3) return cleaned
        if (cleaned.length <= 6) return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3)}`
        return `(${cleaned.slice(0, 3)}) ${cleaned.slice(3, 6)}-${cleaned.slice(6, 10)}`
      }

      return cleaned.replace(/(\d{3})(?=\d)/g, '$1 ').trim()
    }

    const handlePhoneInput = (event) => {
      let value = event.target.value.replace(/\D/g, '')
      form.value.phoneNumber = formatPhoneNumber(value)
      // public_phone is automatically updated via the watcher
    }

    const selectCountry = (country) => {
      selectedCountry.value = country
      form.value.phoneCountry = country.code
      showCountryModal.value = false
      // public_phone is automatically updated via the watcher
    }

    const selectedCountryFlag = computed(() => selectedCountry.value?.flag)

    onMounted(() => {
      loadGooglePlacesScript()
      document.addEventListener('click', handleClickOutside)
      initTooltips()
      getUserLocation()

      // Check if user came from pricing page with a selected plan
      const selectedPlanId = sessionStorage.getItem('selectedPlanId')
      const selectedPlanName = sessionStorage.getItem('selectedPlanName')
      const selectedPlanPrice = sessionStorage.getItem('selectedPlanPrice')
      const registrationSource = sessionStorage.getItem('registrationSource')

      if (selectedPlanId && registrationSource === 'pricing-plan-selected') {
        // Fetch plan details to get interval
        window.axios.get('/plans').then(response => {
          const plans = response.data.plans || response.data.data || []
          const plan = plans.find(p => p.id == selectedPlanId)
          if (plan) {
            selectedPlanInfo.value = {
              id: plan.id,
              name: plan.name || selectedPlanName,
              price: plan.price || selectedPlanPrice,
              interval: plan.interval || 'month'
            }
          } else {
            // Fallback if plan not found
            selectedPlanInfo.value = {
              id: selectedPlanId,
              name: selectedPlanName || 'Selected Plan',
              price: selectedPlanPrice || 0,
              interval: 'month'
            }
          }
        }).catch(() => {
          // Fallback if API fails
          selectedPlanInfo.value = {
            id: selectedPlanId,
            name: selectedPlanName || 'Selected Plan',
            price: selectedPlanPrice || 0,
            interval: 'month'
          }
        })
      }
    })

    onUnmounted(() => {
      document.removeEventListener('click', handleClickOutside)
      tooltips.forEach(tooltip => tooltip.dispose())
    })

    const handleSubmit = async () => {
      if (loading.value) return;

      // Validate passwords match
      if (form.value.password !== form.value.passwordConfirmation) {
        Swal.fire({
          icon: 'error',
          title: 'Password Mismatch',
          text: 'Password and confirmation do not match'
        });
        return;
      }

      // Validate required fields
      if (!form.value.businessName || !form.value.subdomain || !form.value.ownerName ||
          !form.value.public_email || !form.value.public_phone || !form.value.password || !form.value.acceptTerms) {
        Swal.fire({
          icon: 'error',
          title: 'Missing Information',
          text: 'Please fill in all required fields and accept terms & conditions'
        });
        return;
      }

      // Validate phone number
      if (form.value.public_phone.replace(/\D/g, '').length < 7) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid Phone Number',
          text: 'Please enter a valid phone number'
        });
        return;
      }

      loading.value = true;

      try {
        // Prepare the data for submission
        const submitData = {
          business_name: form.value.businessName,
          subdomain: form.value.subdomain,
          owner_name: form.value.ownerName,
          owner_email: form.value.public_email,
          owner_phone: form.value.public_phone,
          password: form.value.password,
          password_confirmation: form.value.passwordConfirmation,
          message: form.value.message || '',
          is_custom_location: form.value.isCustomLocation
        }

        // Include plan_id if user selected a plan from pricing page
        const selectedPlanId = sessionStorage.getItem('selectedPlanId')
        if (selectedPlanId) {
          submitData.plan_id = parseInt(selectedPlanId)
        }

        // Only include location data if it's from Google Places
        if (form.value.location && !form.value.isCustomLocation) {
          submitData.location = {
            address: form.value.location.formatted_address,
            latitude: form.value.location.lat,
            longitude: form.value.location.lng,
            country: form.value.location.country,
            state: form.value.location.state,
            city: form.value.location.city,
            postal_code: form.value.location.postal_code,
            place_id: form.value.placeId
          }
        }

        console.log('Submitting data:', submitData);

        const response = await window.axios.post('/tenants/register', submitData);

        if (response.data.success) {
          // Clear sessionStorage after successful registration
          sessionStorage.removeItem('selectedPlanId')
          sessionStorage.removeItem('selectedPlanName')
          sessionStorage.removeItem('selectedPlanPrice')
          sessionStorage.removeItem('registrationSource')

          // Show success message
          const successMessage = selectedPlanInfo.value
            ? `Your restaurant has been registered successfully with the ${selectedPlanInfo.value.name} plan. After login, you'll be able to complete payment and activate your subscription.`
            : 'Your restaurant has been registered successfully. You will be redirected to login.'

          await Swal.fire({
            icon: 'success',
            title: 'Registration Successful',
            html: successMessage,
            timer: selectedPlanInfo.value ? 4000 : 2000,
            showConfirmButton: true,
            confirmButtonText: 'Go to Login',
            confirmButtonColor: '#c62828'
          });

          // Redirect to the tenant's login page
          const domain = response.data.domain;
          window.location.href = `${window.location.protocol}//${domain}/login`;
        }
      } catch (error) {
        console.error('Registration error:', error);
        const message = error.response?.data?.message || 'An error occurred during registration';
        Swal.fire({
          icon: 'error',
          title: 'Registration Failed: ' + message,
          text: message
        });
      } finally {
        loading.value = false;
      }
    };

    return {
      form,
      selectedPlanInfo,
      loading,
      handleSubmit,
      predictions,
      showAutocomplete,
      businessNameInput,
      handleInput,
      handleBusinessNameBlur,
      selectPlace,
      handlePhoneInput,
      showCountryModal,
      countrySearch,
      selectedCountry,
      filteredCountries,
      selectCountry,
      selectedCountryFlag,
      userLocation,
      locationLoading,
      locationError,
      countryDetecting,
      getUserLocation,
      contactPhone,
    }
  }
}
</script>

<style scoped>
.tenant-register {
  background: #fafafa;
}
.card {
  border-radius: 0 0 16px 16px;
}
.card-top-red-line {
  height: 4px;
  background: #c62828;
  border-top-left-radius: 0;
  border-top-right-radius: 0;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.input-group-text {
  background: #fff;
  border-right: 0;
}
.input-group .form-control {
  border-left: 0;
}
.btn-danger {
  background: #c62828;
  border: none;
}
.btn-danger:hover {
  background: #b71c1c;
}
.btn-danger:disabled {
  background: #e57373;
  border-color: #e57373;
  opacity: 0.7;
}
.country-flag {
  width: 24px;
  height: 16px;
  object-fit: cover;
  border-radius: 2px;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}
.country-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}
.country-modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}
.country-modal-header h5 {
  margin: 0;
  font-size: 1.1rem;
  font-weight: 500;
}
.country-item:hover {
  background-color: #f8f9fa;
}
@media (max-width: 991.98px) {
  .card-body {
    padding: 2rem 1rem !important;
  }
}
.autocomplete-results {
  position: absolute;
  z-index: 1000;
  width: 100%;
  max-height: 260px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 0 0 4px 4px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Location status styles */
.location-status {
  font-size: 0.875rem;
  padding: 0.5rem;
  border-radius: 4px;
  margin-bottom: 0.5rem;
}

.location-status .btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.75rem;
}

.autocomplete-item {
  padding: 12px 18px;
  cursor: pointer;
  border-bottom: 1px solid #f2f2f2;
  transition: background 0.15s;
  color: #222;
}
.autocomplete-item:last-child {
  border-bottom: none;
}
.autocomplete-item:hover {
  background: #f8f9fa;
  color: #c62828;
}

/* Loading spinner for submit button */
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}
</style>
