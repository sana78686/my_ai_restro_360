import { createI18n } from 'vue-i18n';
import en from './locales/en';
import de from './locales/de';

// Get the stored language or default to English
const storedLang = localStorage.getItem('language') || 'en';

const i18n = createI18n({
    legacy: false,
    locale: storedLang,
    fallbackLocale: 'en',
    messages: {
        en,
        de
    }
});

export default i18n;

// Helper function to change language
// export const setLanguage = (lang) => {
//     i18n.global.locale.value = lang;
//     localStorage.setItem('language', lang);
//     document.documentElement.lang = lang;

    
// }; 

export const setLanguage = (lang) => {
    i18n.global.locale.value = lang;
    localStorage.setItem('language', lang);
    document.documentElement.lang = lang;

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