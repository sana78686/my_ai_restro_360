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
        <i class="fas fa-globe me-2"></i>
        {{ getCurrentLanguageName }}
        <i class="fas fa-chevron-down ms-2"></i>
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
  { code: 'de', name: 'Deutsch' }
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
  // Ensure Bootstrap's dropdown is initialized
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
  color: #000;
  background-color: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 4px;
  padding: 0.4rem 1rem;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
}

.language-btn::after {
  display: none;
}

.language-btn:hover,
.language-btn:focus {
  background-color: rgba(0, 0, 0, 0.2);
  border-color: rgba(255, 255, 255, 0.3);
  color: rgb(0, 0, 0);
}

.language-btn .fa-globe {
  color: #e6392a;
}

.language-btn .fa-chevron-down {
  font-size: 0.8rem;
  opacity: 0.8;
}

.dropdown-menu {
  position: absolute;
  min-width: 150px;
  margin-top: 0.5rem;
  background: #fff;
  border: none;
  border-radius: 4px;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.15);
  padding: 0.5rem 0;
  z-index: 1050;
  right: 0;
  left: auto;
}

.dropdown-menu.show {
  display: block;
}

.dropdown-item {
  padding: 0.6rem 1rem;
  font-size: 0.9rem;
  color: #333;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  width: 100%;
  border: none;
  background: none;
  text-align: left;
  cursor: pointer;
}

.dropdown-item .fa-check {
  color: #e6392a;
  font-size: 0.8rem;
}

.dropdown-item span {
  width: 14px;
  display: inline-block;
}

.dropdown-item:hover,
.dropdown-item:focus {
  background-color: #f8f9fa;
  color: #e6392a;
}

.dropdown-item.active {
  background-color: #f8f9fa;
  color: #e6392a;
  font-weight: 500;
}

@media (max-width: 768px) {
  .language-btn {
    margin: 0.5rem auto;
    display: inline-flex;
  }

  .dropdown-menu {
    position: absolute;
    transform: none;
    right: 0;
    left: auto;
  }
}
</style>
