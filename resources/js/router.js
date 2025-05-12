import { createRouter, createWebHistory } from "vue-router";

import Home from "./Pages/Home.vue";
import Navigation from "./components/NavigationDrawer.vue";
import About from "./Pages/About.vue";
import University from "./Pages/university/University.vue";
import Provinces from "./Pages/province/Province.vue";
import Departments from "./Pages/department/Departments.vue";

const routes = [
    // {psth: "/", name: "home", component: Home },
    { path: "/about", name: "about", component: About },
    {path: "/university", name: "university", component: University },
    {path: "/provinces", name: "provinces", component: Provinces },
    {path: "/departments", name: "departments", component: Departments },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
