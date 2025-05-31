import { createApp } from "vue";
import "./bootstrap.js";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import { createPinia } from "pinia";
<<<<<<< HEAD
import i18n from './i18n.js';
=======
import en from "./locales/en.json";
import fa from "./locales/fa.json";
import ps from "./locales/ps.json";
import DatePicker from "vue3-persian-datetime-picker";
>>>>>>> ae57fb9b84c7f7dd32243eedcb7fb2d0bb2f3a90
const app = createApp(App);
const pinia = createPinia();



//
app.component("DatePicker", DatePicker);
app.use(vuetify);
app.use(pinia);
app.use(i18n);
app.use(router);
app.mount("#app");
