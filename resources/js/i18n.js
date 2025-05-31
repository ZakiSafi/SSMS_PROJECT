import { createI18n } from 'vue-i18n';
import EN from './locales/en.json';
import DARI from './locales/fa.json';
import PASHTO from './locales/ps.json';

const messages = {
    en: EN,
    fa: DARI,
    pa:PASHTO

};

const i18n = createI18n({
    legacy: false, 
    locale: "en", 
    fallbackLocale: "en",
    messages,
});

export default i18n;
