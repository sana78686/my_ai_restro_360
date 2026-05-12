<template>
  <div class="language-switcher" :class="{ 'language-switcher--flag': flagOnly }">
    <div class="dropdown">
      <button
        class="btn btn-link dropdown-toggle d-flex align-items-center"
        :class="{ 'language-switcher__btn--flag': flagOnly }"
        type="button"
        id="languageDropdown"
        data-bs-toggle="dropdown"
        :aria-label="currentLanguageLabel"
      >
        <span v-if="flagOnly" class="language-switcher__flag" aria-hidden="true">{{ flagEmoji }}</span>
        <template v-else>
          <i class="fas fa-globe language-switcher__globe me-1"></i>
          {{ currentLanguageLabel }}
        </template>
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
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { setLanguage } from '../i18n';

export default {
  name: 'LanguageSwitcher',
  props: {
    /** Compact trigger: flag only (e.g. Eat Desk–style header) */
    flagOnly: {
      type: Boolean,
      default: false
    }
  },
  setup(props) {
    const { locale } = useI18n();
    const currentLanguage = computed(() => locale.value);

    const languages = [
      { code: 'en', label: 'English' },
      { code: 'ar', label: 'العربية' }
    ];

    const currentLanguageLabel = computed(() => {
      const lang = languages.find(l => l.code === currentLanguage.value);
      return lang ? lang.label : 'English';
    });

    const flagEmoji = computed(() => {
      const code = currentLanguage.value;
      if (code === 'ar') return '🇸🇦';
      return '🇬🇧';
    });

    const changeLanguage = (langCode) => {
      setLanguage(langCode);
    };

    return {
      currentLanguage,
      currentLanguageLabel,
      languages,
      changeLanguage,
      flagEmoji
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

.language-switcher__globe {
  color: #00844d;
}

.language-switcher--flag .dropdown-toggle::after {
  margin-left: 0.15rem;
  vertical-align: 0.15em;
}

.language-switcher__btn--flag {
  padding: 0.35rem 0.45rem !important;
  border-radius: 10px;
}

.language-switcher__btn--flag:hover {
  background: color-mix(in srgb, #00844d 8%, #fff);
}

.language-switcher__flag {
  font-size: 1.35rem;
  line-height: 1;
}

.btn-link:hover {
  color: var(--primary-color);
}

.dropdown-item.active {
  background-color: var(--primary-color);
  color: white;
}
</style> 