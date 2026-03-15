<template>
  <div class="language-switcher">
    <div class="dropdown">
      <button class="btn btn-link dropdown-toggle" type="button" id="languageDropdown" data-bs-toggle="dropdown">
        <i class="fas fa-globe text-danger"></i>
        {{ currentLanguageLabel }}
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li v-for="lang in languages" :key="lang.code">
          <a 
            class="dropdown-item" 
            href="#" 
            @click.prevent="changeLanguage(lang.code)"
            :class="{ active: currentLanguage === lang.code }"
          >
            {{ lang.label }}
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

export default {
  name: 'LanguageSwitcher',
  setup() {
    const { locale } = useI18n();
    const currentLanguage = computed(() => locale.value);

    const languages = [
      { code: 'en', label: 'English' },
      { code: 'de', label: 'Deutsch' }
    ];

    const currentLanguageLabel = computed(() => {
      const lang = languages.find(l => l.code === currentLanguage.value);
      return lang ? lang.label : 'English';
    });

    const changeLanguage = (langCode) => {
      locale.value = langCode;
      localStorage.setItem('language', langCode);
      localStorage.setItem('language', langCode);
      document.documentElement.lang = langCode;
    };

    return {
      currentLanguage,
      currentLanguageLabel,
      languages,
      changeLanguage
    };
  }
};
</script>

<style scoped>
.language-switcher {
  display: inline-block;
}

.btn-link {
  color: inherit;
  text-decoration: none;
  padding: 0.5rem;
}

.btn-link:hover {
  color: var(--primary-color);
}

.dropdown-item.active {
  background-color: var(--primary-color);
  color: white;
}
</style> 