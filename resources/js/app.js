import { createApp } from "vue";
import "./bootstrap.js";
import { createI18n } from "vue-i18n";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import { createPinia } from "pinia";
import i18n from './i18n.js';
const app = createApp(App);
const pinia = createPinia();

// Importing locale files
const messages = { en, fa, ps };

const savedLocale = localStorage.getItem("locale") || "en";

const i18n = createI18n({
    legacy: false,
    locale: savedLocale,
    fallbackLocale: "en",
    messages,
});

//
// app.component("DatePicker", DatePicker);
app.use(vuetify);
app.use(pinia);
app.use(router);
// Setting up i18n for internationalization
app.use(i18n);
app.mount("#app");
