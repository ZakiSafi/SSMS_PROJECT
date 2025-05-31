import { createApp } from "vue";
import "./bootstrap.js";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import { createPinia } from "pinia";
import i18n from './i18n.js';
const app = createApp(App);
const pinia = createPinia();



//
// app.component("DatePicker", DatePicker);
app.use(vuetify);
app.use(pinia);
app.use(i18n);
app.use(router);
app.mount("#app");
