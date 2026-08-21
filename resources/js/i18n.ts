import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import en from '@/locales/en';

i18n.use(initReactI18next).init({
    lng: 'en',
    fallbackLng: 'en',
    defaultNS: 'common',
    ns: ['common', 'clients'],
    resources: {
        en: {
            common: en.common,
            clients: en.clients,
        },
    },
    interpolation: {
        escapeValue: false,
    },
});

export default i18n;
