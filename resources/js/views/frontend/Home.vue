<template>
  <div class="restro-landing" :class="{ 'restro-landing--building': isBuilding || showPostRegisterPending }">
    <div v-if="isBuilding" class="restro-building" role="status" aria-live="polite" aria-busy="true">
      <div class="restro-building__inner">
        <div class="restro-building__spinner" aria-hidden="true" />
        <h2 class="restro-building__title">{{ $t('home.landing.buildingTitle') }}</h2>
        <p class="restro-building__text">{{ $t('home.landing.buildingText') }}</p>
      </div>
    </div>
    <div v-else-if="showPostRegisterPending" class="restro-building restro-building--pending" role="status">
      <div class="restro-building__inner">
        <h2 class="restro-building__title">{{ $t('home.landing.postRegisterTitle') }}</h2>
        <p class="restro-building__text">{{ $t('home.landing.postRegisterText') }}</p>
        <a
          v-if="postRegisterLoginUrl"
          class="restro-building__cta"
          :href="postRegisterLoginUrl"
        >{{ $t('home.landing.postRegisterCta') }}</a>
      </div>
    </div>
    <div class="restro-landing__wrap">
      <main class="restro-landing__grid" aria-label="Registration">
        <div class="restro-landing__col restro-landing__col--form">
          <div class="restro-landing__form-scroller">
            <div id="restro-lead-anch" class="restro-landing__form-card" tabindex="-1">
            <form class="restro-form" @submit.prevent="onSubmit" novalidate>
              <div class="restro-form__row restro-form__row--2">
                <div class="restro-form__field">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-fn">{{ $t('home.landing.firstName') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="tip('tipFirst')" />
                  </div>
                  <input
                    id="restro-fn"
                    v-model.trim="form.firstName"
                    class="restro-form__input"
                    type="text"
                    autocomplete="given-name"
                    :placeholder="$t('home.landing.phFirst')"
                    :title="$t('home.landing.phFirst')"
                    required
                  />
                </div>
                <div class="restro-form__field">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-ln">{{ $t('home.landing.lastName') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="tip('tipLast')" />
                  </div>
                  <input
                    id="restro-ln"
                    v-model.trim="form.lastName"
                    class="restro-form__input"
                    type="text"
                    autocomplete="family-name"
                    :placeholder="$t('home.landing.phLast')"
                    :title="$t('home.landing.phLast')"
                    required
                  />
                </div>
              </div>
              <div class="restro-form__row restro-form__row--2">
                <div class="restro-form__field">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-em">{{ $t('home.landing.email') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="tip('tipEmail')" />
                  </div>
                  <input
                    id="restro-em"
                    v-model.trim="form.email"
                    class="restro-form__input"
                    type="email"
                    autocomplete="email"
                    :placeholder="$t('home.landing.phEmail')"
                    :title="$t('home.landing.phEmail')"
                    required
                  />
                </div>
                <div class="restro-form__field">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-pw">{{ $t('home.landing.password') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="tip('tipPassword')" />
                  </div>
                  <p v-if="passwordFirstHint" class="restro-pw-suggest" role="status">{{ passwordFirstHint }}</p>
                  <input
                    id="restro-pw"
                    v-model="form.password"
                    class="restro-form__input"
                    type="password"
                    autocomplete="new-password"
                    :placeholder="$t('home.landing.phPassword')"
                    :title="$t('home.landing.phPassword')"
                    :required="true"
                    :aria-invalid="!passwordIsValid && form.password.length > 0"
                  />
                </div>
              </div>
              <div class="restro-form__row restro-form__row--2">
                <div class="restro-form__field restro-form__field--ac">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-rn">{{ $t('home.landing.restaurantName') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="tip('tipRestaurant')" />
                  </div>
                  <div class="restro-form__ac-wrap" ref="restaurantAcWrap">
                    <input
                      id="restro-rn"
                      v-model="restaurantName"
                      class="restro-form__input restro-form__input--ac"
                      type="text"
                      required
                      autocomplete="organization"
                      @input="onRestaurantInput"
                      @blur="onRestaurantBlur"
                      :placeholder="placePlaceholder"
                      :title="placePlaceholder"
                    />
                    <Transition name="restro-ac">
                      <ul
                        v-show="showPlaceAc && placePredictions.length > 0"
                        class="restro-form__ac-list"
                        role="listbox"
                      >
                        <li
                          v-for="p in placePredictions"
                          :key="p.place_id"
                          class="restro-form__ac-item"
                          role="option"
                          @mousedown.prevent="onSelectPlace(p)"
                        >
                          {{ p.description }}
                        </li>
                      </ul>
                    </Transition>
                  </div>
                </div>
                <div class="restro-form__field">
                  <div class="restro-form__label-row">
                    <label class="restro-form__label" for="restro-sub">{{ $t('home.landing.subdomain') }}<span class="restro-form__req" aria-hidden="true">*</span></label>
                    <RestroInfoTip :text="$t('home.landing.subdomainHelp')" />
                  </div>
                  <div class="restro-form__subdomain">
                    <input
                      id="restro-sub"
                      v-model.trim="form.subdomain"
                      class="restro-form__input restro-form__input--sub"
                      type="text"
                      autocomplete="off"
                      spellcheck="false"
                      autocapitalize="none"
                      :placeholder="$t('home.landing.phSubdomain')"
                      :title="$t('home.landing.phSubdomain')"
                      required
                      @input="onSubdomainInput"
                    />
                    <span class="restro-form__subdomain-suffix">.{{ publicTenantDomain }}</span>
                  </div>
                </div>
              </div>
              <div class="restro-form__row restro-form__row--2 restro-form__row--phone-country">
                <div class="restro-form__field restro-form__field--phone">
                  <RestroPhoneField
                    v-model:dial-code="form.dial"
                    v-model:local-number="form.phone"
                    :label="$t('home.landing.phone')"
                    :placeholder="$t('home.landing.enterPhone')"
                    :tooltip="tip('tipPhone')"
                    :suggest-iso2="suggestedPhoneIso2"
                  />
                </div>
                <div class="restro-form__field">
                  <RestroCountryCardField
                    v-model="form.country"
                    :options="countryOptionList"
                    :label-text="$t('home.landing.country')"
                    :change-cta-text="$t('home.landing.countryChangeCta')"
                    :select-country-text="$t('home.landing.countrySelectPrompt')"
                    :search-placeholder="$t('home.landing.typeToSearch')"
                    :empty-text="$t('home.landing.noMatches')"
                    :detecting="countryDetecting"
                    :detecting-text="$t('home.landing.countryDetecting')"
                    :info-tooltip="tip('tipCountry')"
                    :name-override="countryNameOverride"
                    :close-text="$t('home.landing.countryModalClose')"
                    @user-picked="onCountryUserPicked"
                  />
                </div>
              </div>

              <div class="restro-form__field">
                <div class="restro-form__legend-row">
                  <p class="restro-form__legend restro-form__legend--inline">{{ $t('home.landing.numLocations') }}<span class="restro-form__req" aria-hidden="true">*</span></p>
                  <RestroInfoTip :text="tip('tipLocations')" />
                </div>
                <div class="restro-pill-group" role="group" :aria-label="$t('home.landing.numLocations')">
                  <button
                    v-for="k in locKeys"
                    :key="k"
                    type="button"
                    class="restro-pill"
                    :class="{ 'restro-pill--active': form.locations === k }"
                    @click="form.locations = k"
                  >
                    {{ $t('home.landing.loc.' + k) }}
                  </button>
                </div>
              </div>

              <div class="restro-form__field">
                <div class="restro-form__legend-row">
                  <p class="restro-form__legend restro-form__legend--inline">{{ $t('home.landing.products') }}<span class="restro-form__req" aria-hidden="true">*</span></p>
                  <RestroInfoTip :text="tip('tipProducts')" />
                </div>
                <div
                  id="restro-pills-products"
                  class="restro-pill-group restro-pill-group--wrap"
                  role="group"
                  :aria-label="$t('home.landing.products')"
                >
                  <button
                    v-for="k in prodKeys"
                    :key="k"
                    type="button"
                    class="restro-pill"
                    :class="{ 'restro-pill--active': form.products.includes(k) }"
                    @click="toggleProd(k)"
                  >
                    {{ $t('home.landing.prod.' + k) }}
                  </button>
                </div>
              </div>

              <div class="restro-form__field">
                <RestroSearchSelect
                  id="restro-hear"
                  v-model="form.hearAbout"
                  :label="$t('home.landing.hearAbout')"
                  required-mark
                  :info-tooltip="tip('tipHear')"
                  :options="hearOptionList"
                  :placeholder-select="$t('home.landing.hearAboutPlaceholder')"
                  :search-placeholder="$t('home.landing.typeToSearch')"
                  :empty-text="$t('home.landing.noMatches')"
                />
              </div>

              <div class="restro-form__checks">
                <label class="restro-form__check">
                  <input v-model="form.marketingOk" class="restro-form__checkbox" type="checkbox" />
                  <span class="restro-form__check-text">{{ $t('home.landing.marketing') }}</span>
                </label>
                <label class="restro-form__check">
                  <input
                    id="restro-cb-privacy"
                    v-model="form.privacyOk"
                    class="restro-form__checkbox"
                    type="checkbox"
                    required
                  />
                  <span class="restro-form__check-text">
                    {{ $t('home.landing.privacy') }}
                    <a
                      href="https://airestro360.com/legal/terms"
                      class="restro-form__link"
                      target="_blank"
                      rel="noopener noreferrer"
                    >{{ $t('home.landing.termsLink') }}</a>
                    {{ $t('home.landing.privacyAnd') }}
                    <a
                      href="https://airestro360.com/legal/privacy-policy"
                      class="restro-form__link"
                      target="_blank"
                      rel="noopener noreferrer"
                    >{{ $t('home.landing.privacyLink') }}</a>.
                  </span>
                </label>
              </div>

              <p
                v-if="submitInfo"
                class="restro-form__msg restro-form__msg--success"
                role="status"
              >
                {{ submitInfo }}
              </p>
              <p
                v-if="submitError"
                id="restro-form-server-msg"
                class="restro-form__msg restro-form__msg--error"
                role="alert"
              >
                {{ submitError }}
              </p>

              <button type="submit" class="restro-form__submit" :disabled="submitting">
                {{ submitting ? '…' : $t('home.landing.getStarted') }}
              </button>
            </form>
          </div>
          </div>
        </div>
        <aside class="restro-landing__col restro-landing__col--content" aria-label="About AiRestro 360">
          <h1 class="restro-landing__headline restro-landing__headline--split">
            <span class="restro-landing__headline-line restro-landing__headline-line--ink">
              {{ $t('home.landing.headlineBeforeBrand') }}
            </span>
            <span class="restro-landing__headline-line restro-landing__headline-line--brand">
              {{ $t('home.landing.headlineBrand') }}
            </span>
          </h1>
          <ul class="restro-landing__bullets">
            <li v-for="key in bulletKeys" :key="key" class="restro-landing__bullet">
              {{ $t('home.landing.' + key) }}
            </li>
          </ul>
          <p class="restro-landing__copy">{{ $t('home.landing.copy1') }}</p>
          <p class="restro-landing__copy restro-landing__copy--lead">
            <strong>{{ $t('home.landing.copyLead') }}</strong>
          </p>
          <p class="restro-landing__partners-intro">{{ $t('home.landing.partnersLine') }}</p>
          <div class="restro-landing__logos" role="list">
            <div v-for="(name, i) in partnerNames" :key="i" class="restro-landing__logo-pill" role="listitem">
              {{ name }}
            </div>
          </div>
        </aside>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, defineAsyncComponent, nextTick, reactive, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { useGoogleRestaurantField } from '../../composables/useGoogleRestaurantField.js'
import RestroCountryCardField from '../../components/frontend/RestroCountryCardField.vue'
import { PHONE_COUNTRIES } from '../../data/phoneCountries.js'

const RestroSearchSelect = defineAsyncComponent(() => import('../../components/frontend/RestroSearchSelect.vue'))
const RestroPhoneField = defineAsyncComponent(() => import('../../components/frontend/RestroPhoneField.vue'))
const RestroInfoTip = defineAsyncComponent(() => import('../../components/frontend/RestroInfoTip.vue'))

const { t, tm } = useI18n()
const submitting = ref(false)
const isBuilding = ref(false)
const showPostRegisterPending = ref(false)
const postRegisterLoginUrl = ref('')
const submitError = ref('')
const submitInfo = ref('')

function scrollIntoFormAndFocus (el) {
  if (!el) return
  el.scrollIntoView({ behavior: 'smooth', block: 'center' })
  if (typeof el.focus === 'function') {
    try {
      el.focus({ preventScroll: true })
    } catch {
      el.focus()
    }
  }
}

function focusById (id) {
  nextTick(() => {
    const el = document.getElementById(id)
    scrollIntoFormAndFocus(el)
  })
}

function focusFirstProductPill () {
  nextTick(() => {
    const g = document.getElementById('restro-pills-products')
    const btn = g?.querySelector('button.restro-pill')
    if (btn) {
      scrollIntoFormAndFocus(btn)
    }
  })
}

const API_ERR_ORDER = [
  'email',
  'owner_email',
  'password',
  'subdomain',
  'business_name',
  'owner_name',
  'owner_phone',
  'location',
  'country'
]

const API_ERR_TO_ID = {
  email: 'restro-em',
  owner_email: 'restro-em',
  password: 'restro-pw',
  subdomain: 'restro-sub',
  business_name: 'restro-rn',
  owner_name: 'restro-fn',
  owner_phone: 'restro-phone-local',
  location: 'restro-rn',
  country: 'restro-country-card-btn'
}

function firstApiErrorEntry (errors) {
  for (const key of API_ERR_ORDER) {
    if (errors[key] == null) continue
    const v = errors[key]
    const line = Array.isArray(v) ? v[0] : v
    if (line) {
      return { key, text: String(line) }
    }
  }
  for (const key of Object.keys(errors)) {
    const v = errors[key]
    if (v == null) continue
    const line = Array.isArray(v) ? v[0] : v
    if (line) {
      return { key, text: String(line) }
    }
  }
  return null
}

function focusFromApiErrors (errors) {
  const pick = firstApiErrorEntry(errors)
  if (!pick) {
    return false
  }
  submitError.value = pick.text
  const id = API_ERR_TO_ID[pick.key]
  focusById(id || 'restro-em')
  return true
}
const bulletKeys = ['bullet1', 'bullet2', 'bullet3']

function tip (key) {
  return t('home.landing.' + key)
}

const {
  name: restaurantName,
  placeId: restaurantPlaceId,
  placeDetails: restaurantPlaceDetails,
  isCustom: isCustomRestaurant,
  predictions: placePredictions,
  showAutocomplete: showPlaceAc,
  userLocation: userGeo,
  geoCountryIso2,
  countryDetecting,
  wrapRef: restaurantAcWrap,
  handleInput: onRestaurantInput,
  selectPlace: onSelectPlace,
  onBlur: onRestaurantBlur
} = useGoogleRestaurantField()

/** Unmapped region from Google: show name from phone list, store “other” in form.country */
const formCountryDisplayIso2 = ref(null)
const countryPickedByUser = ref(false)

const suggestedPhoneIso2 = computed(() => {
  if (countryPickedByUser.value) {
    return ''
  }
  const p = restaurantPlaceDetails.value
  if (p?.country_iso) {
    return String(p.country_iso)
  }
  if (geoCountryIso2.value) {
    return String(geoCountryIso2.value)
  }
  return ''
})

const placePlaceholder = computed(() =>
  userGeo.value ? t('home.landing.phRestaurantNear') : t('home.landing.phRestaurant')
)

const locKeys = ['l1', 'l2', 'l3', 'l4', 'l5']
const prodKeys = ['p1', 'p2', 'p3', 'p4', 'p5', 'p6']

/** Shown next to the subdomain field (public site); not read from host. */
const publicTenantDomain = 'airestro360.com'

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  password: '',
  dial: '+1',
  phone: '',
  country: '',
  subdomain: '',
  locations: 'l1',
  products: [],
  hearAbout: '',
  marketingOk: false,
  privacyOk: false,
})

const subdomainUserEdited = ref(false)


const passwordRuleItems = computed(() => {
  const p = form.password
  return [
    { id: 'len', met: p.length >= 8, label: t('home.landing.pwRuleLen'), abbr: t('home.landing.pwAbbrLen') },
    { id: 'up', met: /[A-Z]/.test(p), label: t('home.landing.pwRuleUpper'), abbr: t('home.landing.pwAbbrUpper') },
    { id: 'lo', met: /[a-z]/.test(p), label: t('home.landing.pwRuleLower'), abbr: t('home.landing.pwAbbrLower') },
    { id: 'num', met: /[0-9]/.test(p), label: t('home.landing.pwRuleNumber'), abbr: t('home.landing.pwAbbrNum') },
    { id: 'sym', met: /[^A-Za-z0-9]/.test(p), label: t('home.landing.pwRuleSymbol'), abbr: t('home.landing.pwAbbrSym') }
  ]
})

const passwordIsValid = computed(() => passwordRuleItems.value.every((r) => r.met))

const passwordFirstHint = computed(() => {
  const p = form.password
  if (!p) return ''
  if (p.length < 8) return t('home.landing.pwHintLen')
  if (!/[A-Z]/.test(p)) return t('home.landing.pwHintUpper')
  if (!/[a-z]/.test(p)) return t('home.landing.pwHintLower')
  if (!/[0-9]/.test(p)) return t('home.landing.pwHintNumber')
  if (!/[^A-Za-z0-9]/.test(p)) return t('home.landing.pwHintSymbol')
  return ''
})

function onSubdomainInput () {
  subdomainUserEdited.value = true
  form.subdomain = String(form.subdomain).toLowerCase().replace(/[^a-z0-9-]/g, '')
}

function slugToSubdomain (name) {
  let s = String(name || '').toLowerCase()
  s = s.replace(/[^a-z0-9]+/g, '-').replace(/-+/g, '-').replace(/^-+|-+$/g, '')
  if (s.length < 2) {
    s = s ? s + 'go' : 'go'
  }
  if (s.length === 1) s = s + '0'
  if (!/^[a-z0-9]/.test(s)) s = 'a' + s
  if (!/[a-z0-9]$/.test(s)) s = s + '0'
  return s.slice(0, 48)
}

const SUBDOMAIN_OK = /^[a-z0-9](?:[a-z0-9-]*[a-z0-9])?$/

function buildLeadMessage () {
  const products = (form.products || [])
    .map((k) => t('home.landing.prod.' + k))
    .join(', ')
  const hm = hearMap.value || {}
  const hearLabel = form.hearAbout ? (hm[form.hearAbout] || form.hearAbout) : '—'
  return [
    `${t('home.landing.numLocations')}: ${t('home.landing.loc.' + form.locations)}`,
    `${t('home.landing.products')}: ${products || '—'}`,
    `${t('home.landing.hearAbout')}: ${hearLabel}`,
    `${t('home.landing.marketing')}: ${form.marketingOk ? 'yes' : 'no'}`
  ].join('\n')
}

const countryOptions = computed(() => tm('home.landing.countries') || {})
const hearMap = computed(() => tm('home.landing.hearOptions') || {})

const countryOptionList = computed(() => {
  const o = countryOptions.value
  return Object.keys(o).map((k) => ({ value: k, label: o[k] }))
})
const hearOptionList = computed(() => {
  const o = hearMap.value
  return Object.keys(o).map((k) => ({ value: k, label: o[k] }))
})

/** Google ISO-3166-1 alpha-2 → home.landing.countries key; unknown → other + name via PHONE_COUNTRIES (nameOverride) */
const ISO_TO_LANDING = {
  US: 'us', GB: 'uk', DE: 'de', AU: 'au', AT: 'at', CH: 'ch', FR: 'fr',
  PK: 'pk', IN: 'in', CA: 'ca', AE: 'ae', SA: 'sa', IT: 'it', ES: 'es',
  BR: 'br', MX: 'mx', NL: 'nl', TR: 'tr', ZA: 'za', EG: 'eg', AR: 'ar',
  PL: 'pl', PH: 'ph', NG: 'ng', BE: 'nl', SE: 'se', IE: 'ie', GR: 'gr',
  BD: 'in', LK: 'in', ID: 'ph', TH: 'ph', VN: 'ph', MY: 'ph', SG: 'ph', NZ: 'au',
  CL: 'ar', CO: 'mx', KE: 'eg'
}
const LANDING_TO_ISO2 = {
  us: 'US', uk: 'GB', de: 'DE', au: 'AU', at: 'AT', ch: 'CH', fr: 'FR',
  pk: 'PK', in: 'IN', ca: 'CA', ae: 'AE', sa: 'SA', it: 'IT', es: 'ES',
  br: 'BR', mx: 'MX', nl: 'NL', tr: 'TR', za: 'ZA', eg: 'EG', ar: 'AR',
  pl: 'PL', ph: 'PH', se: 'SE', ng: 'NG', gr: 'GR', ie: 'IE', other: null
}
function isoToLandingKey (iso) {
  if (!iso) {
    return ''
  }
  if (ISO_TO_LANDING[iso]) {
    formCountryDisplayIso2.value = null
    return ISO_TO_LANDING[iso]
  }
  formCountryDisplayIso2.value = iso
  return 'other'
}
function applyDialForLandingKey (key) {
  const iso2 = LANDING_TO_ISO2[key]
  if (!iso2) {
    return
  }
  const c = PHONE_COUNTRIES.find((x) => x.code === iso2)
  if (c) {
    form.dial = c.dialCode
  } else if (formCountryDisplayIso2.value) {
    const c2 = PHONE_COUNTRIES.find((x) => x.code === formCountryDisplayIso2.value)
    if (c2) {
      form.dial = c2.dialCode
    }
  }
}
function onCountryUserPicked () {
  countryPickedByUser.value = true
  formCountryDisplayIso2.value = null
  if (form.country) {
    applyDialForLandingKey(form.country)
  }
}
const countryNameOverride = computed(() => {
  if (!formCountryDisplayIso2.value) {
    return ''
  }
  return PHONE_COUNTRIES.find((c) => c.code === formCountryDisplayIso2.value)?.name || ''
})
watch(geoCountryIso2, (iso) => {
  if (!iso || form.country) {
    return
  }
  form.country = isoToLandingKey(String(iso))
  applyDialForLandingKey(form.country)
})
watch(restaurantPlaceDetails, (d) => {
  if (d?.country_iso) {
    form.country = isoToLandingKey(d.country_iso)
    applyDialForLandingKey(form.country)
  }
})
watch(restaurantPlaceId, (id) => {
  if (id) {
    countryPickedByUser.value = false
  }
})

watch(restaurantName, (v) => {
  if (subdomainUserEdited.value) return
  if (!v || !String(v).trim()) return
  const s = slugToSubdomain(v)
  if (s) form.subdomain = s
})

const partnerNames = computed(() => [
  t('home.landing.brandA'),
  t('home.landing.brandB'),
  t('home.landing.brandC')
])

function toggleProd(k) {
  const i = form.products.indexOf(k)
  if (i === -1) form.products.push(k)
  else form.products.splice(i, 1)
}

async function onSubmit () {
  submitError.value = ''
  submitInfo.value = ''

  if (!form.firstName?.trim()) {
    focusById('restro-fn')
    return
  }
  if (!form.lastName?.trim()) {
    focusById('restro-ln')
    return
  }
  if (!form.email?.trim()) {
    focusById('restro-em')
    return
  }
  if (!passwordIsValid.value) {
    focusById('restro-pw')
    return
  }
  if (!String(form.subdomain || '').trim() || !SUBDOMAIN_OK.test(form.subdomain.trim())) {
    focusById('restro-sub')
    return
  }
  if (!String(restaurantName.value || '').trim()) {
    focusById('restro-rn')
    return
  }
  if (!form.country) {
    focusById('restro-country-card-btn')
    return
  }
  if (!form.hearAbout) {
    focusById('restro-hear')
    return
  }
  if (form.products.length === 0) {
    focusFirstProductPill()
    return
  }
  if (!form.privacyOk) {
    focusById('restro-cb-privacy')
    return
  }

  try {
    isBuilding.value = true
    const submitData = {
      business_name: String(restaurantName.value).trim(),
      subdomain: String(form.subdomain).trim().toLowerCase(),
      owner_name: `${form.firstName.trim()} ${form.lastName.trim()}`,
      owner_email: form.email.trim(),
      owner_phone: [form.dial, form.phone].filter(Boolean).join(' ').trim(),
      password: form.password
    }
    if (restaurantPlaceDetails.value && restaurantPlaceId.value && !isCustomRestaurant.value) {
      const d = restaurantPlaceDetails.value
      submitData.location = {
        address: d.formatted_address,
        latitude: d.lat,
        longitude: d.lng,
        country: d.country,
        state: d.state,
        city: d.city,
        postal_code: d.postal_code,
        place_id: restaurantPlaceId.value
      }
    }
    submitData.message = `${t('home.landing.leadNoteLabel')}:\n${buildLeadMessage()}`

    const { data } = await window.axios.post('/tenants/register', submitData)
    if (data.success && data.domain) {
      isBuilding.value = false
      postRegisterLoginUrl.value = `${window.location.protocol}//${data.domain}/login?registered=1`
      showPostRegisterPending.value = true
      return
    }
    isBuilding.value = false
    if (data.success && !data.domain) {
      submitError.value = ''
      submitInfo.value = t('home.landing.thankYouText')
      focusById('restro-lead-anch')
    } else {
      submitInfo.value = ''
      submitError.value = data.message || t('home.landing.registerFailed')
      focusById('restro-em')
    }
  } catch (e) {
    isBuilding.value = false
    submitInfo.value = ''
    const data = e?.response?.data
    const msg = data?.message
    const errs = data?.errors
    if (errs && typeof errs === 'object' && !Array.isArray(errs) && Object.keys(errs).length) {
      if (!focusFromApiErrors(errs)) {
        submitError.value = String(
          Object.values(errs)
            .flat()
            .filter(Boolean)[0] || msg || t('home.landing.registerFailed')
        )
        focusById('restro-em')
      }
    } else {
      submitError.value = String(msg || t('home.landing.registerFailed'))
      focusById('restro-em')
    }
  } finally {
    if (!isBuilding.value) {
      submitting.value = false
    }
  }
}
</script>

<style scoped>
/* BEM: all custom classes use the restro- prefix */
.restro-landing {
  --restro-accent: #00844d;
  --restro-ink: #111;
  --restro-border: #d0d0d0;
  --restro-body: #5e5e5e;
  position: relative;
  overflow-x: hidden;
  background: #fff;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  padding-bottom: 1.5rem;
}

.restro-landing--building {
  min-height: 100vh;
}

.restro-landing--building .restro-landing__wrap {
  pointer-events: none;
  user-select: none;
}

.restro-building {
  position: fixed;
  inset: 0;
  z-index: 200;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1.5rem;
  background: #fff;
  text-align: center;
}

.restro-building__inner {
  max-width: 26rem;
}

.restro-building__spinner {
  width: 2.75rem;
  height: 2.75rem;
  margin: 0 auto 1.25rem;
  border: 3px solid #e4e4e4;
  border-top-color: var(--restro-accent);
  border-radius: 50%;
  animation: restro-build-spin 0.75s linear infinite;
}

@keyframes restro-build-spin {
  to {
    transform: rotate(360deg);
  }
}

.restro-building__title {
  font-size: 1.25rem;
  font-weight: 800;
  color: #111;
  margin: 0 0 0.5rem;
  letter-spacing: 0.02em;
  text-transform: uppercase;
  line-height: 1.25;
}

.restro-building__text {
  margin: 0;
  font-size: 0.95rem;
  line-height: 1.55;
  color: var(--restro-body);
}

.restro-building--pending .restro-building__title {
  text-transform: none;
  letter-spacing: -0.02em;
}

.restro-building__cta {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-top: 1.25rem;
  padding: 0.72rem 1.35rem;
  border-radius: 999px;
  background: var(--restro-accent);
  color: #fff !important;
  font-weight: 700;
  font-size: 0.88rem;
  letter-spacing: 0.04em;
  text-decoration: none;
  text-transform: uppercase;
  transition: filter 0.15s;
}

.restro-building__cta:hover {
  filter: brightness(0.95);
  color: #fff !important;
}

.restro-landing__wrap {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.25rem 1.25rem 2rem;
}

/* Form (left) wider; marketing (right) — only the form column scrolls on desktop (see 992+ block) */
.restro-landing__grid {
  display: grid;
  grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
  gap: 1.5rem 2rem;
  align-items: start;
}

@media (min-width: 992px) {
  .restro-landing {
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .restro-landing__wrap {
    flex: 1 1 auto;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .restro-landing__grid {
    flex: 1 1 auto;
    min-height: 0;
    align-items: stretch;
    overflow: hidden;
  }

  .restro-landing__col--form {
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .restro-landing__form-scroller {
    flex: 1 1 auto;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
    padding-right: 0.2rem;
    scrollbar-gutter: stable;
  }
}

.restro-landing__form-card {
  background: #fff;
  border-radius: 12px;
  padding: 1.35rem 1.4rem 1.65rem;
  border: 1px solid #d8d8d8;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
}

.restro-form {
  display: flex;
  flex-direction: column;
  gap: 0.72rem;
}

.restro-form__row {
  display: grid;
  gap: 0.75rem 1.15rem;
}

.restro-form__row--2 {
  grid-template-columns: 1fr 1fr;
}

.restro-form__row--full {
  grid-template-columns: 1fr;
}

.restro-form__field--span2 {
  grid-column: 1 / -1;
  min-width: 0;
}

.restro-form__field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
  min-width: 0;
}

.restro-form__field--phone {
  min-width: 0;
}

.restro-form__label-row,
.restro-form__legend-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}

.restro-form__label,
.restro-form__legend {
  font-size: 0.8rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}
.restro-form__req {
  color: #c41e1e;
  font-weight: 700;
  margin-left: 0.1rem;
}
.restro-form__row--phone-country {
  align-items: flex-start;
}
.restro-form__legend--inline {
  display: inline;
}

.restro-form__input,
.restro-form__select {
  width: 100%;
  min-width: 0;
  border: 1px solid var(--restro-border);
  border-radius: 8px;
  padding: 0.55rem 0.7rem;
  font-size: 0.95rem;
  background: #fff;
  color: #111;
  box-sizing: border-box;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-form__input::placeholder {
  color: #9a9a9a;
  opacity: 1;
  font-size: 0.9rem;
}
@media (max-width: 480px) {
  .restro-landing__form-card .restro-form__input {
    font-size: 1rem;
  }
  .restro-landing__form-card .restro-form__input::placeholder {
    font-size: 0.95rem;
  }
}

.restro-form__input:focus,
.restro-form__select:focus {
  outline: none;
  box-shadow: 0 0 0 2px color-mix(in srgb, var(--restro-accent) 25%, #fff);
  border-color: var(--restro-accent);
}

.restro-pw-suggest {
  font-size: 0.72rem;
  color: #b35c00;
  margin: 0 0 0.32rem;
  line-height: 1.3;
  max-width: 100%;
}

.restro-form__subdomain {
  display: flex;
  align-items: stretch;
  max-width: 100%;
}
.restro-form__input--sub {
  flex: 1;
  min-width: 0;
  border-radius: 8px 0 0 8px;
}
.restro-form__subdomain-suffix {
  display: flex;
  align-items: center;
  padding: 0 0.5rem 0 0.55rem;
  background: #f4f4f4;
  border: 1px solid var(--restro-border);
  border-left: none;
  border-radius: 0 8px 8px 0;
  font-size: 0.72rem;
  color: #555;
  white-space: nowrap;
  flex-shrink: 0;
  max-width: min(50%, 11rem);
  overflow: hidden;
  text-overflow: ellipsis;
}
@media (min-width: 400px) {
  .restro-form__subdomain-suffix {
    font-size: 0.8rem;
    max-width: none;
  }
}

.restro-form__field--ac {
  position: relative;
}

.restro-form__ac-wrap {
  position: relative;
}

.restro-form__input--ac {
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-form__input--ac:focus {
  border-color: #00844d;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 22%, #fff);
}

.restro-form__ac-list {
  position: absolute;
  z-index: 20;
  left: 0;
  right: 0;
  top: calc(100% + 2px);
  margin: 0;
  padding: 0.25rem 0;
  list-style: none;
  max-height: 240px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #e2e2e2;
  border-radius: 10px;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.1);
}

.restro-form__ac-item {
  padding: 0.65rem 0.8rem;
  font-size: 0.9rem;
  cursor: pointer;
  border: none;
  color: #222;
  transition: background 0.12s, color 0.12s;
}

.restro-form__ac-item:hover {
  background: #f0fdf7;
  color: #00844d;
}
.restro-ac-enter-active,
.restro-ac-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.restro-ac-enter-from,
.restro-ac-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

.restro-pill-group {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.restro-pill-group--wrap {
  gap: 0.5rem;
}

.restro-pill {
  border: 1px solid var(--restro-border);
  background: #fff;
  color: #333;
  border-radius: 999px;
  padding: 0.4rem 0.75rem;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  line-height: 1.2;
  transition: background 0.15s, color 0.15s, border-color 0.15s;
}

.restro-pill:hover {
  border-color: #999;
}

.restro-pill--active {
  background: var(--restro-accent);
  color: #fff;
  border-color: var(--restro-accent);
}

.restro-form__checks {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
  font-size: 0.8rem;
  color: #333;
  line-height: 1.4;
}

.restro-form__check {
  display: flex;
  align-items: flex-start;
  gap: 0.45rem;
  cursor: pointer;
  margin: 0;
}

.restro-form__checkbox {
  margin-top: 0.2rem;
  flex-shrink: 0;
  width: 1rem;
  height: 1rem;
  accent-color: var(--restro-accent);
}

.restro-form__link {
  color: #00844d;
  font-weight: 600;
  text-decoration: underline;
}

.restro-form__link:hover {
  color: var(--restro-ink);
}

.restro-form__msg {
  margin: 0 0 0.6rem;
  font-size: 0.88rem;
  line-height: 1.45;
  padding: 0.55rem 0.7rem;
  border-radius: 8px;
}
.restro-form__msg--error {
  color: #6e1414;
  background: #fff0f0;
  border: 1px solid #e8b4b4;
}
.restro-form__msg--success {
  color: #0a5a38;
  background: #f0faf5;
  border: 1px solid #b8e0cc;
}

.restro-form__submit {
  width: 100%;
  border: none;
  border-radius: 999px;
  background: var(--restro-accent);
  color: #fff;
  font-weight: 700;
  font-size: 0.9rem;
  letter-spacing: 0.04em;
  text-transform: uppercase;
  padding: 0.72rem 1rem;
  cursor: pointer;
  margin-top: 0.25rem;
  transition: filter 0.15s, opacity 0.15s;
}
.restro-form__submit:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--restro-accent);
}

.restro-form__submit:hover:not(:disabled) {
  filter: brightness(0.95);
}

.restro-form__submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Marketing column (right) — on desktop, row height = viewport; form scrolls in left column so this stays put */
.restro-landing__col--content {
  z-index: 2;
  padding-top: 0.25rem;
  max-width: 100%;
  align-self: start;
}

.restro-landing__headline {
  font-size: clamp(1.05rem, 2.2vw, 1.4rem);
  font-weight: 800;
  letter-spacing: 0.04em;
  line-height: 1.25;
  text-transform: uppercase;
  color: #1a1a1a;
  margin: 0 0 1rem;
}
.restro-landing__headline--split {
  line-height: 1.05;
  letter-spacing: 0.02em;
  font-size: clamp(1.1rem, 2.1vw, 1.6rem);
}
.restro-landing__headline-line {
  display: block;
  margin-bottom: 0.12em;
}
.restro-landing__headline-line--ink {
  color: #111;
}
.restro-landing__headline-line--brand {
  color: #00844d;
}
.restro-landing__headline--accent {
  color: #00844d;
}

.restro-landing__bullets {
  margin: 0 0 1.1rem 1.1rem;
  padding: 0;
  color: #2d2d2d;
  font-size: 0.95rem;
  line-height: 1.55;
  list-style: disc;
}

.restro-landing__bullet {
  margin-bottom: 0.5rem;
}

.restro-landing__bullet::marker {
  color: var(--restro-accent);
}

.restro-landing__copy {
  font-size: clamp(0.92rem, 1.15vw, 1rem);
  line-height: 1.6;
  color: var(--restro-body);
  margin: 0 0 0.9rem;
  max-width: 38em;
}

.restro-landing__copy--lead {
  margin-bottom: 1.15rem;
}

.restro-landing__partners-intro {
  font-size: 0.88rem;
  color: #666;
  line-height: 1.5;
  margin: 0 0 0.75rem;
}

.restro-landing__logos {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.6rem;
  margin-bottom: 1.1rem;
}

.restro-landing__logo-pill {
  min-height: 2.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  font-size: 0.72rem;
  font-weight: 600;
  color: #555;
  background: #fff;
  border: 1px solid #e3e3e3;
  border-radius: 8px;
  padding: 0.45rem 0.4rem;
  line-height: 1.2;
}

.restro-landing__ctas {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.6rem;
}

.restro-landing__btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
  font-family: inherit;
  font-size: 0.78rem;
  font-weight: 700;
  letter-spacing: 0.05em;
  padding: 0.6rem 1.15rem;
  border-radius: 999px;
  cursor: pointer;
  border: 1px solid transparent;
  transition: background 0.15s, color 0.15s, border-color 0.15s, filter 0.15s;
  line-height: 1.2;
}

.restro-landing__btn--primary {
  background: #00844d;
  color: #fff;
  border: none;
}

.restro-landing__btn--primary:hover {
  filter: brightness(0.95);
}

.restro-landing__btn--primary:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #00844d;
}

.restro-landing__btn--ghost {
  background: #fff;
  color: #111;
  border: 1px solid #c8c8c8;
  font-weight: 700;
  text-transform: none;
  letter-spacing: 0.01em;
  font-size: 0.78rem;
}

.restro-landing__btn--ghost:hover {
  border-color: #999;
  background: #fafafa;
}

.restro-landing__btn--ghost:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px #00844d;
}

@media (max-width: 991.98px) {
  .restro-landing__form-scroller {
    overflow: visible;
    min-height: 0;
    flex: none;
    padding-right: 0;
    scrollbar-gutter: auto;
  }

  .restro-landing__grid {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  .restro-landing__col--content {
    padding-right: 0;
  }
  .restro-landing__form-card {
    max-width: 100%;
  }
  .restro-landing__logos {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 576px) {
  .restro-landing__wrap {
    padding: 1rem 0.9rem 1.5rem;
  }
  .restro-form__row--2 {
    grid-template-columns: 1fr;
  }
}
</style>
