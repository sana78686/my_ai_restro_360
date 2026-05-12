import { createI18n } from 'vue-i18n';
import en from './locales/en';
import arPatch from './locales/ar';

/**
 * Deep-merge locale patch onto base (e.g. Arabic over English for translated keys only).
 */
function deepMerge (base, patch) {
    if (base === undefined || base === null) {
        return patch
    }
    if (patch === undefined || patch === null) {
        return base
    }
    if (Array.isArray(base) || Array.isArray(patch)) {
        return patch
    }
    if (typeof base !== 'object' || typeof patch !== 'object') {
        return patch
    }
    const out = { ...base }
    for (const k of Object.keys(patch)) {
        out[k] = deepMerge(base[k], patch[k])
    }
    return out
}

let storedLang = localStorage.getItem('language') || 'en'
// Legacy: German locale removed — migrate saved preference to English
if (storedLang === 'de') {
    storedLang = 'en'
    localStorage.setItem('language', 'en')
}

const ar = deepMerge(en, arPatch)

export function applyDocumentLocale (lang) {
    if (typeof document === 'undefined') {
        return
    }
    document.documentElement.lang = lang
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr')
}

const i18n = createI18n({
    legacy: false,
    locale: storedLang,
    fallbackLocale: 'en',
    messages: {
        en,
        ar
    }
})

applyDocumentLocale(storedLang)

export default i18n;

export const setLanguage = (lang) => {
    i18n.global.locale.value = lang;
    localStorage.setItem('language', lang);
    applyDocumentLocale(lang);

    if (typeof axios !== 'undefined') {
        axios.post('/set-locale', { locale: lang })
            .then(response => {
                console.log('Locale updated in Laravel');
            })
            .catch(error => {
                console.error('Error updating locale in Laravel:', error);
            });
    }
};
