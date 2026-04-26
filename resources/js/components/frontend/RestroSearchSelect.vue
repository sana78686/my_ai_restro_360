<template>
  <div class="restro-sel" ref="rootRef">
    <div v-if="label" class="restro-sel__label-row">
      <label class="restro-sel__label" :for="id">
        <span>{{ label }}</span>
        <span v-if="requiredMark" class="restro-sel__req" aria-hidden="true">*</span>
      </label>
      <RestroInfoTip v-if="infoTooltip" :text="infoTooltip" />
    </div>
    <div class="restro-sel__control" :class="{ 'restro-sel__control--open': open, 'restro-sel__control--disabled': disabled }">
      <button
        :id="id"
        type="button"
        class="restro-sel__btn"
        :disabled="disabled"
        :aria-expanded="open"
        :aria-controls="id + '-listbox'"
        @click="toggle"
      >
        <span class="restro-sel__value" :class="{ 'restro-sel__value--empty': !displayLabel }">{{ displayLabel || placeholderSelect }}</span>
        <span class="restro-sel__chev" aria-hidden="true" />
      </button>
      <Transition name="restro-sel-fade">
        <div v-show="open" class="restro-sel__panel" :id="id + '-panel'">
          <div class="restro-sel__search-wrap">
            <input
              ref="searchInputRef"
              v-model="query"
              type="text"
              class="restro-sel__search"
              :placeholder="searchPlaceholder"
              @keydown.esc="close"
            />
          </div>
          <ul
            :id="id + '-listbox'"
            class="restro-sel__list"
            role="listbox"
          >
            <li
              v-for="it in filtered"
              :key="it.value"
              class="restro-sel__item"
              role="option"
              :aria-selected="modelValue === it.value"
              @mousedown.prevent="choose(it.value)"
            >
              {{ it.label }}
            </li>
            <li v-if="filtered.length === 0" class="restro-sel__empty">
              {{ emptyText }}
            </li>
          </ul>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import RestroInfoTip from './RestroInfoTip.vue'

const props = defineProps({
  modelValue: { type: String, default: '' },
  options: { type: Array, default: () => [] },
  id: { type: String, required: true },
  label: { type: String, default: '' },
  requiredMark: { type: Boolean, default: false },
  infoTooltip: { type: String, default: '' },
  required: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  placeholderSelect: { type: String, default: 'Select' },
  searchPlaceholder: { type: String, default: 'Search' },
  emptyText: { type: String, default: 'No results' }
})

const emit = defineEmits(['update:modelValue'])

const open = ref(false)
const query = ref('')
const rootRef = ref(null)
const searchInputRef = ref(null)

const list = computed(() => {
  return props.options.map((o) => (typeof o === 'string' ? { value: o, label: o } : o))
})

const displayLabel = computed(() => {
  const v = list.value.find((x) => x.value === props.modelValue)
  return v ? v.label : ''
})

const filtered = computed(() => {
  const q = query.value.trim().toLowerCase()
  if (!q) return list.value
  return list.value.filter((x) => x.label.toLowerCase().includes(q))
})

function choose (v) {
  emit('update:modelValue', v)
  open.value = false
  query.value = ''
}

function close () {
  open.value = false
  query.value = ''
}

function toggle () {
  if (props.disabled) return
  open.value = !open.value
}

watch(open, (v) => {
  if (v) {
    setTimeout(() => {
      searchInputRef.value?.focus()
    }, 0)
  } else {
    query.value = ''
  }
})

function onDoc (e) {
  if (rootRef.value && !rootRef.value.contains(e.target)) {
    close()
  }
}

onMounted(() => {
  document.addEventListener('click', onDoc)
})
onUnmounted(() => {
  document.removeEventListener('click', onDoc)
})
</script>

<style scoped>
.restro-sel {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.restro-sel__label-row {
  display: flex;
  align-items: center;
  gap: 0.35rem;
}
.restro-sel__label {
  font-size: 0.8rem;
  font-weight: 600;
  color: #1a1a1a;
  margin: 0;
}
.restro-sel__req {
  color: #c41e1e;
  font-weight: 700;
  margin-left: 0.1rem;
}

.restro-sel__control {
  position: relative;
}

.restro-sel__btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  min-height: 2.4rem;
  padding: 0.45rem 0.7rem;
  text-align: left;
  background: #fff;
  color: #111;
  border: 1px solid #d9d9d9;
  border-radius: 8px;
  font: inherit;
  font-size: 0.95rem;
  cursor: pointer;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.restro-sel__btn:hover:not(:disabled) {
  border-color: #00844d;
}
.restro-sel__control--open .restro-sel__btn {
  border-color: #00844d;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 22%, #fff);
}

.restro-sel__value--empty {
  color: #888;
}
.restro-sel__chev {
  width: 0.45rem;
  height: 0.45rem;
  border-right: 2px solid #666;
  border-bottom: 2px solid #666;
  transform: rotate(45deg);
  margin-top: -0.2rem;
  flex-shrink: 0;
  transition: transform 0.2s;
}
.restro-sel__control--open .restro-sel__chev {
  transform: rotate(225deg);
  margin-top: 0.1rem;
}

.restro-sel__panel {
  position: absolute;
  z-index: 50;
  left: 0;
  right: 0;
  top: calc(100% + 4px);
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  box-shadow: 0 10px 28px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  max-height: 280px;
  display: flex;
  flex-direction: column;
}

.restro-sel__search-wrap {
  padding: 0.5rem 0.5rem 0.25rem;
  border-bottom: 1px solid #f0f0f0;
}
.restro-sel__search {
  width: 100%;
  box-sizing: border-box;
  padding: 0.5rem 0.6rem;
  font-size: 0.9rem;
  border: 1px solid #e4e4e4;
  border-radius: 6px;
  outline: none;
}
.restro-sel__search:focus {
  border-color: #00844d;
  box-shadow: 0 0 0 2px color-mix(in srgb, #00844d 12%, #fff);
}

.restro-sel__list {
  list-style: none;
  margin: 0;
  padding: 0.25rem 0;
  overflow-y: auto;
  max-height: 200px;
}
.restro-sel__item {
  padding: 0.55rem 0.75rem;
  font-size: 0.9rem;
  cursor: pointer;
  color: #222;
  transition: background 0.12s;
}
.restro-sel__item:hover {
  background: #f0fdf7;
  color: #0a5d45;
}
.restro-sel__empty {
  padding: 0.75rem;
  font-size: 0.85rem;
  color: #888;
  text-align: center;
}
.restro-sel__control--disabled .restro-sel__btn {
  opacity: 0.55;
  cursor: not-allowed;
}
.restro-sel-fade-enter-active,
.restro-sel-fade-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}
.restro-sel-fade-enter-from,
.restro-sel-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>
