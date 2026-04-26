<template>
  <div class="restro-phone">
    <div class="restro-phone__label-row">
      <label class="restro-phone__label" for="restro-phone-local">
        <span>{{ displayLabelText }}</span>
        <span v-if="showRequiredStar" class="restro-phone__req" aria-hidden="true">*</span>
      </label>
      <RestroInfoTip v-if="tooltip" :text="tooltip" />
    </div>
    <div class="restro-phone__row">
      <div
        class="restro-phone__dial"
        role="button"
        tabindex="0"
        @click="showModal = true"
        @keydown.enter.prevent="showModal = true"
      >
        <img v-if="selected.flag" :src="selected.flag" :alt="''" class="restro-phone__flag" width="22" height="16" />
        <span class="restro-phone__code">{{ selected.dialCode }}</span>
        <i class="fas fa-chevron-down restro-phone__chev" aria-hidden="true" />
      </div>
      <input
        id="restro-phone-local"
        class="restro-phone__input"
        type="tel"
        :value="localNumber"
        :placeholder="placeholder"
        autocomplete="tel-national"
        @input="onInput"
      />
    </div>

    <div v-if="showModal" class="restro-phone__modal" @click.self="showModal = false">
      <div class="restro-phone__modal-box">
        <div class="restro-phone__modal-head">
          <span class="restro-phone__modal-title">{{ countryPickerDefault }}</span>
          <button type="button" class="restro-phone__close" aria-label="Close" @click="showModal = false">&times;</button>
        </div>
        <div class="restro-phone__search">
          <i class="fas fa-search restro-phone__search-icon" aria-hidden="true" />
          <input v-model="search" type="text" class="restro-phone__search-inp" :placeholder="searchPhDefault" />
        </div>
        <ul class="restro-phone__list">
          <li
            v-for="c in filtered"
            :key="c.code"
            class="restro-phone__li"
            @click="pick(c)"
          >
            <img :src="c.flag" :alt="''" class="restro-phone__flag" width="22" height="16" />
            <span class="restro-phone__name">{{ c.name }}</span>
            <span class="restro-phone__dial-text">{{ c.dialCode }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useI18n } from 'vue-i18n'
import { PHONE_COUNTRIES } from '../../data/phoneCountries.js'
import RestroInfoTip from './RestroInfoTip.vue'

const props = defineProps({
  dialCode: { type: String, default: '+1' },
  localNumber: { type: String, default: '' },
  label: { type: String, default: '' },
  requiredStar: { type: Boolean, default: true },
  tooltip: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  countryPickerTitle: { type: String, default: '' },
  searchPh: { type: String, default: '' },
  suggestIso2: { type: String, default: '' }
})

const emit = defineEmits(['update:dialCode', 'update:localNumber'])

const { t } = useI18n()
const countryPickerDefault = computed(() => props.countryPickerTitle || t('home.landing.selectCountryCode'))
const searchPhDefault = computed(() => props.searchPh || t('home.landing.search'))

const countries = PHONE_COUNTRIES
const search = ref('')
const showModal = ref(false)
const displayLabelText = computed(() => props.label || '')
const showRequiredStar = computed(() => props.requiredStar && !!props.label)

const selected = ref(
  countries.find((c) => c.dialCode === props.dialCode) || countries[0]
)

watch(
  () => props.dialCode,
  (d) => {
    const c = countries.find((x) => x.dialCode === d)
    if (c) selected.value = c
  }
)

watch(
  () => props.suggestIso2,
  (iso) => {
    if (!iso) return
    const c = countries.find((x) => x.code === iso)
    if (c) {
      selected.value = c
      emit('update:dialCode', c.dialCode)
    }
  }
)

const filtered = computed(() => {
  if (!search.value.trim()) return countries
  const q = search.value.toLowerCase()
  return countries.filter(
    (c) => c.name.toLowerCase().includes(q) || c.dialCode.includes(q) || c.code.toLowerCase().includes(q)
  )
})

function pick (c) {
  selected.value = c
  emit('update:dialCode', c.dialCode)
  showModal.value = false
  search.value = ''
}

function onInput (e) {
  emit('update:localNumber', e.target.value)
}
</script>

<style scoped>
.restro-phone {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}
.restro-phone__label-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}
.restro-phone__label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}
.restro-phone__req {
  color: #c41e1e;
  font-weight: 700;
  margin-left: 0.1rem;
}
.restro-phone__row {
  display: flex;
  align-items: stretch;
  gap: 0.5rem;
}
.restro-phone__dial {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  min-width: 6.5rem;
  padding: 0.45rem 0.5rem;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  background: #fff;
  cursor: pointer;
  flex-shrink: 0;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-phone__dial:hover {
  border-color: #00844d;
}
.restro-phone__dial:focus {
  outline: none;
  border-color: #00844d;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 22%, #fff);
}
.restro-phone__flag {
  object-fit: cover;
  border-radius: 2px;
}
.restro-phone__code {
  font-size: 0.88rem;
  font-weight: 600;
  color: #111;
}
.restro-phone__chev {
  font-size: 0.6rem;
  margin-left: auto;
  color: #666;
}
.restro-phone__input {
  flex: 1;
  min-width: 0;
  border: 1px solid #d0d0d0;
  border-radius: 6px;
  padding: 0.45rem 0.6rem;
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-phone__input::placeholder {
  color: #9a9a9a;
}
.restro-phone__input:focus {
  outline: none;
  border-color: #00844d;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 22%, #fff);
}

.restro-phone__modal {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
.restro-phone__modal-box {
  width: 100%;
  max-width: 400px;
  max-height: 90vh;
  background: #fff;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
}
.restro-phone__modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.6rem 0.75rem;
  border-bottom: 1px solid #eee;
}
.restro-phone__modal-title {
  font-weight: 600;
  font-size: 0.95rem;
}
.restro-phone__close {
  border: none;
  background: none;
  font-size: 1.4rem;
  line-height: 1;
  cursor: pointer;
  color: #666;
}
.restro-phone__search {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-bottom: 1px solid #eee;
}
.restro-phone__search-icon {
  color: #999;
  font-size: 0.85rem;
}
.restro-phone__search-inp {
  flex: 1;
  border: 1px solid #e2e2e2;
  border-radius: 6px;
  padding: 0.4rem 0.5rem;
  font-size: 0.9rem;
}
.restro-phone__search-inp:focus {
  outline: none;
  border-color: #00844d;
}
.restro-phone__list {
  list-style: none;
  margin: 0;
  padding: 0;
  overflow-y: auto;
  max-height: 280px;
}
.restro-phone__li {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  cursor: pointer;
  border-bottom: 1px solid #f3f3f3;
  font-size: 0.9rem;
}
.restro-phone__li:hover {
  background: #f0fdf7;
}
.restro-phone__name {
  flex: 1;
  color: #222;
}
.restro-phone__dial-text {
  color: #666;
  font-size: 0.85rem;
}
@media (max-width: 576px) {
  .restro-phone__dial {
    min-width: 5.5rem;
    padding: 0.4rem 0.4rem;
  }
  .restro-phone__row {
    flex-direction: row;
  }
}
</style>
