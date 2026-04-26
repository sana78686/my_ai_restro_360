<template>
  <div class="restro-ctry">
    <div class="restro-ctry__label-row">
      <label class="restro-ctry__label" for="restro-country-card-btn">
        {{ labelText }}<span class="restro-ctry__req" aria-hidden="true">*</span>
      </label>
      <RestroInfoTip v-if="infoTooltip" :text="infoTooltip" />
    </div>

    <div class="restro-ctry__card" role="group" :aria-label="labelText">
      <div class="restro-ctry__icon" aria-hidden="true">
        <i class="fas fa-globe" />
      </div>
      <div class="restro-ctry__body">
        <p class="restro-ctry__mainline">
          <span
            v-if="!hasSelection"
            class="restro-ctry__name restro-ctry__name--placeholder"
          >{{ selectCountryText }}</span>
          <span v-else class="restro-ctry__name">{{ primaryName }}</span>
          <span class="restro-ctry__sep" aria-hidden="true">·</span>
          <button
            id="restro-country-card-btn"
            type="button"
            class="restro-ctry__change"
            :disabled="disabled"
            @click="open = true"
          >
            {{ changeCtaText }}
          </button>
        </p>
      </div>
    </div>

    <p v-if="detecting" class="restro-ctry__status">{{ detectingText }}</p>

    <Teleport to="body">
      <div v-if="open" class="restro-ctry__mask" @click.self="open = false">
        <div class="restro-ctry__modal" role="dialog" aria-modal="true" :aria-label="labelText">
          <div class="restro-ctry__modal-head">
            <span class="restro-ctry__modal-title">{{ labelText }}</span>
            <button type="button" class="restro-ctry__close" :aria-label="closeText" @click="open = false">
              &times;
            </button>
          </div>
          <div class="restro-ctry__search">
            <i class="fas fa-search restro-ctry__search-ic" aria-hidden="true" />
            <input
              v-model="query"
              type="search"
              class="restro-ctry__search-inp"
              :placeholder="searchPlaceholder"
              @keydown.esc="open = false"
            />
          </div>
          <ul class="restro-ctry__list" role="listbox">
            <li
              v-for="o in filteredOptions"
              :key="o.value"
              class="restro-ctry__li"
              role="option"
              :aria-selected="modelValue === o.value"
              @click="select(o.value)"
            >
              {{ o.label }}
            </li>
            <li v-if="filteredOptions.length === 0" class="restro-ctry__empty">
              {{ emptyText }}
            </li>
          </ul>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import RestroInfoTip from './RestroInfoTip.vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  labelText: { type: String, default: 'Country' },
  changeCtaText: { type: String, default: 'Change location' },
  selectCountryText: { type: String, default: 'Select country' },
  searchPlaceholder: { type: String, default: 'Search' },
  emptyText: { type: String, default: 'No matches' },
  detecting: { type: Boolean, default: false },
  detectingText: { type: String, default: 'Detecting your region…' },
  closeText: { type: String, default: 'Close' },
  infoTooltip: { type: String, default: '' },
  nameOverride: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

const emit = defineEmits(['update:modelValue', 'user-picked'])

const open = ref(false)
const query = ref('')

const hasSelection = computed(() => Boolean(String(props.modelValue || '').trim()))

const displayName = computed(() => {
  if (!props.modelValue) {
    return ''
  }
  return props.options.find((o) => o.value === props.modelValue)?.label || ''
})

const primaryName = computed(() => props.nameOverride || displayName.value)

const filteredOptions = computed(() => {
  const list = props.options
  const q = query.value.trim().toLowerCase()
  if (!q) return list
  return list.filter(
    (o) =>
      o.label.toLowerCase().includes(q) ||
      String(o.value).toLowerCase().includes(q)
  )
})

function select (value) {
  emit('update:modelValue', value)
  emit('user-picked', value)
  open.value = false
  query.value = ''
}

watch(open, (v) => {
  if (v) {
    query.value = ''
  }
})
</script>

<style scoped>
.restro-ctry {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  min-width: 0;
}
.restro-ctry__label-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
  min-height: 1.15rem;
}
.restro-ctry__label {
  font-size: 0.78rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}
.restro-ctry__req {
  color: #c41e1e;
  font-weight: 700;
  margin-left: 0.1rem;
}
.restro-ctry__status {
  margin: 0.3rem 0 0;
  font-size: 0.7rem;
  line-height: 1.25;
  color: #666;
}
.restro-ctry__card {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.6rem 0.45rem;
  min-height: 2.5rem;
  box-sizing: border-box;
  background: #fff;
  border: 1px solid #d8d8d8;
  border-radius: 8px;
  min-width: 0;
}
.restro-ctry__icon {
  flex-shrink: 0;
  width: 1.4rem;
  height: 1.4rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  background: #fff;
  color: #00844d;
  font-size: 0.78rem;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
}
.restro-ctry__body {
  min-width: 0;
  flex: 1;
}
.restro-ctry__mainline {
  display: flex;
  align-items: center;
  flex-wrap: nowrap;
  gap: 0.35rem;
  margin: 0;
  font-size: 0.82rem;
  line-height: 1.35;
  color: #1a1a1a;
  min-width: 0;
}
.restro-ctry__name {
  font-weight: 700;
  color: #111;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  flex: 0 1 auto;
  max-width: 9rem;
}
.restro-ctry__name--placeholder {
  color: #666;
  font-weight: 600;
}
.restro-ctry__sep {
  color: #999;
  font-weight: 500;
  flex-shrink: 0;
}
.restro-ctry__change {
  flex-shrink: 0;
  margin: 0;
  padding: 0;
  border: none;
  background: none;
  color: #00844d;
  font-weight: 600;
  font: inherit;
  font-size: 0.82rem;
  text-decoration: underline;
  text-underline-offset: 2px;
  cursor: pointer;
  white-space: nowrap;
}
.restro-ctry__change:hover {
  color: #006a3e;
}
.restro-ctry__change:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
.restro-ctry__mask {
  position: fixed;
  inset: 0;
  z-index: 5000;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
.restro-ctry__modal {
  width: 100%;
  max-width: 24rem;
  max-height: min(70vh, 28rem);
  display: flex;
  flex-direction: column;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
  overflow: hidden;
}
.restro-ctry__modal-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.65rem 0.9rem;
  border-bottom: 1px solid #e8e8e8;
}
.restro-ctry__modal-title {
  font-weight: 700;
  font-size: 0.95rem;
  color: #111;
}
.restro-ctry__close {
  border: none;
  background: none;
  font-size: 1.35rem;
  line-height: 1;
  color: #666;
  cursor: pointer;
  padding: 0.15rem 0.35rem;
}
.restro-ctry__search {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 0.75rem;
  border-bottom: 1px solid #e8e8e8;
}
.restro-ctry__search-ic {
  color: #888;
  font-size: 0.85rem;
}
.restro-ctry__search-inp {
  flex: 1;
  min-width: 0;
  border: 1px solid #d0d0d0;
  border-radius: 8px;
  padding: 0.4rem 0.55rem;
  font-size: 0.9rem;
}
.restro-ctry__list {
  list-style: none;
  margin: 0;
  padding: 0.35rem 0;
  overflow-y: auto;
  flex: 1;
  min-height: 0;
}
.restro-ctry__li {
  padding: 0.5rem 0.9rem;
  font-size: 0.9rem;
  cursor: pointer;
  color: #222;
}
.restro-ctry__li:hover {
  background: #f0faf5;
}
.restro-ctry__empty {
  padding: 0.75rem 0.9rem;
  color: #888;
  font-size: 0.88rem;
}
</style>
