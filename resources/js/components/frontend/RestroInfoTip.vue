<template>
  <span
    ref="rootRef"
    class="restro-infotip"
    @mouseenter="onEnter"
    @mouseleave="onLeave"
    @focusin="onEnter"
    @focusout="onLeave"
  >
    <button
      :id="btnId"
      ref="btnRef"
      type="button"
      class="restro-infotip__btn"
      :aria-label="text"
      :aria-describedby="open ? popId : undefined"
    >
      <i class="fas fa-info-circle" aria-hidden="true" />
    </button>
    <Teleport to="body">
      <div
        v-show="open"
        :id="popId"
        class="restro-infotip__pop"
        :style="popStyle"
        role="tooltip"
      >
        {{ text }}
      </div>
    </Teleport>
  </span>
</template>

<script setup>
import { nextTick, onUnmounted, ref } from 'vue'

defineProps({
  text: { type: String, required: true }
})

const base = `r-${Math.random().toString(36).slice(2, 9)}`
const btnId = `${base}-b`
const popId = `${base}-p`

const rootRef = ref(null)
const btnRef = ref(null)
const open = ref(false)
const popStyle = ref({})

function place () {
  const el = btnRef.value
  if (!el) {
    return
  }
  const r = el.getBoundingClientRect()
  const maxW = 280
  const vw = window.innerWidth
  const w = Math.min(maxW, vw - 16)
  let left = r.left
  if (left + w > vw - 8) {
    left = Math.max(8, vw - 8 - w)
  }
  if (left < 8) {
    left = 8
  }
  const top = r.bottom + 8
  popStyle.value = {
    position: 'fixed',
    top: `${top}px`,
    left: `${left}px`,
    width: `${w}px`,
    zIndex: 2147483000
  }
}

let scrollParent = null
const DOC_SCROLL = { capture: true, passive: true }

function bindScroll () {
  unbindScroll()
  let p = rootRef.value?.parentElement
  while (p && p !== document.body) {
    const oy = getComputedStyle(p).overflowY
    if (oy === 'auto' || oy === 'scroll') {
      scrollParent = p
      p.addEventListener('scroll', place, { passive: true })
      return
    }
    p = p.parentElement
  }
  document.addEventListener('scroll', place, DOC_SCROLL)
}

function unbindScroll () {
  if (scrollParent) {
    scrollParent.removeEventListener('scroll', place)
    scrollParent = null
  } else {
    document.removeEventListener('scroll', place, DOC_SCROLL)
  }
  window.removeEventListener('resize', place)
}

function onEnter () {
  open.value = true
  nextTick(() => {
    place()
    bindScroll()
    window.addEventListener('resize', place, { passive: true })
  })
}

function onLeave () {
  open.value = false
  unbindScroll()
  window.removeEventListener('resize', place)
}

onUnmounted(() => {
  unbindScroll()
  window.removeEventListener('resize', place)
})
</script>

<style scoped>
.restro-infotip {
  position: relative;
  display: inline-flex;
  align-items: center;
  flex-shrink: 0;
  vertical-align: middle;
  outline: none;
  z-index: 3;
}

.restro-infotip__btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.4rem;
  height: 1.4rem;
  margin: 0;
  padding: 0;
  border: none;
  border-radius: 50%;
  background: color-mix(in srgb, #00844d 8%, #fff);
  color: #00844d;
  font-size: 0.8rem;
  line-height: 1;
  cursor: help;
  transition: background 0.15s, color 0.15s, box-shadow 0.15s;
}

.restro-infotip__btn:hover {
  background: color-mix(in srgb, #00844d 14%, #fff);
  color: #006a3e;
}

.restro-infotip__btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 2px #fff, 0 0 0 4px color-mix(in srgb, #00844d 45%, #fff);
}
</style>

<style>
/* Unscoped: teleported to body */
.restro-infotip__pop {
  position: fixed;
  padding: 0.55rem 0.7rem;
  font-size: 0.75rem;
  font-weight: 400;
  line-height: 1.5;
  letter-spacing: 0.01em;
  text-align: left;
  color: #2a2a2a;
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.14);
  box-sizing: border-box;
  overflow: visible;
  max-width: min(17.5rem, calc(100vw - 16px));
  pointer-events: none;
  word-wrap: break-word;
  overflow-wrap: break-word;
}
</style>
