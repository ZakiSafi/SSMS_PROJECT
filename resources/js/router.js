import { createRouter, createWebHistory } from "vue-router";

import Home from "./Pages/Home.vue";
import Navigation from "./components/NavigationDrawer.vue";
import About from "./Pages/About.vue";
import Department from "./Pages/Department.vue";
import University from "./Pages/university/University.vue";
import Provinces from "./Pages/province/Province.vue";

const routes = [
    // {psth: "/", name: "home", component: Home },
    { path: "/about", name: "about", component: About },
    { path: "/department", name: "department", component: Department },
    {path: "/university", name: "university", component: University },
    {path: "/provinces", name: "provinces", component: Provinces },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
