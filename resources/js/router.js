import { createRouter, createWebHistory } from "vue-router";

import Home from "./Pages/Home.vue";
import About from "./Pages/About.vue";

const routes = [
    { path: "/", name: "home", component: Home },
    { path: "/about", name: "about", component: About },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
