<template>
  <div class="language-switcher">
    <div class="dropdown">
      <button
        class="btn dropdown-toggle language-btn"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-auto-close="true"
        aria-expanded="false"
      >
        <i class="fas fa-globe language-btn__globe" aria-hidden="true" />
        {{ getCurrentLanguageName }}
        <i class="fas fa-chevron-down language-btn__chev" aria-hidden="true" />
      </button>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
        <li v-for="lang in languages" :key="lang.code">
          <button
            class="dropdown-item"
            :class="{ active: currentLanguage === lang.code }"
            @click="changeLanguage(lang.code)"
          >
            <i class="fas fa-check me-2" v-if="currentLanguage === lang.code"></i>
            <span class="me-2" v-else></span>
            {{ lang.name }}
          </button>
        </li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { setLanguage } from '../../i18n'

const { locale } = useI18n()

const languages = [
  { code: 'en', name: 'English' },
  { code: 'ar', name: 'العربية' }
]

const currentLanguage = computed(() => locale.value)

const getCurrentLanguageName = computed(() => {
  const lang = languages.find(l => l.code === currentLanguage.value)
  return lang ? lang.name : 'English'
})

const changeLanguage = (lang) => {
  setLanguage(lang)
}

onMounted(() => {
  if (typeof bootstrap !== 'undefined') {
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
    dropdownElementList.forEach(dropdownToggleEl => {
      new bootstrap.Dropdown(dropdownToggleEl)
    })
  }
})
</script>

<style scoped>
.language-switcher {
  display: inline-block;
  position: relative;
}

.dropdown {
  position: relative;
}

.language-btn {
  --lang-accent: #00844d;
  color: #1a1a1a;
  background-color: #fff;
  border: 1px solid color-mix(in srgb, var(--lang-accent) 35%, #d0d0d0);
  border-radius: 999px;
  padding: 0.45rem 0.95rem;
  font-size: 0.88rem;
  font-weight: 600;
  transition: background 0.15s, border-color 0.15s, color 0.15s, box-shadow 0.15s;
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.language-btn::after {
  display: none;
}

.language-btn:hover,
.language-btn:focus,
.language-btn.show {
  background-color: color-mix(in srgb, var(--lang-accent) 6%, #fff);
  border-color: var(--lang-accent);
  color: #111;
  box-shadow: 0 0 0 2px color-mix(in srgb, var(--lang-accent) 18%, transparent);
}

.language-btn__globe {
  color: var(--lang-accent);
  font-size: 0.95rem;
}

.language-btn__chev {
  font-size: 0.72rem;
  opacity: 0.75;
  color: #444;
}

.dropdown-menu {
  position: absolute;
  min-width: 11rem;
  margin-top: 0.4rem;
  background: #fff;
  border: 1px solid #e2e2e2;
  border-radius: 12px;
  box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
  padding: 0.4rem 0;
  z-index: 1050;
  right: 0;
  left: auto;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-item {
  padding: 0.55rem 1rem;
  font-size: 0.88rem;
  color: #333;
  transition: background 0.15s, color 0.15s;
  display: flex;
  align-items: center;
  width: 100%;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
  font-family: inherit;
}

.dropdown-item .fa-check {
  color: #00844d;
  font-size: 0.8rem;
}

.dropdown-item span {
  width: 14px;
  display: inline-block;
}

.dropdown-item:hover,
.dropdown-item:focus {
  background-color: color-mix(in srgb, #00844d 8%, #fff);
  color: #0d3d28;
}

.dropdown-item.active {
  background-color: color-mix(in srgb, #00844d 10%, #fff);
  color: #00844d;
  font-weight: 600;
}

@media (max-width: 768px) {
  .language-btn {
    margin: 0.5rem auto;
  }

  .dropdown-menu {
    position: absolute;
    transform: none;
    right: 0;
    left: auto;
  }
}
</style>
