import { createI18n } from "vue-i18n";
import { ref } from "vue";

const localeMessages = import.meta.glob('../locales/messages.json', { eager: true }); //TODO:I will move the file to S3 later

const messages = localeMessages['../locales/messages.json'].default;

const i18n = createI18n({
    locale: "en",
    legacy: false,
    fallbackLocale: "en",
    messages,
});
export default i18n;
