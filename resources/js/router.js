import { createRouter, createWebHistory } from "vue-router";

import Home from "./Pages/Home.vue";
import About from "./Pages/About.vue";
import University from "./Pages/university/University.vue";
import Provinces from "./Pages/province/Province.vue";
import Departments from "./Pages/department/Departments.vue";
import Faculties from "./Pages/faculty/Faculties.vue";

const routes = [
    // {psth: "/", name: "home", component: Home },
    { path: "/about", name: "about", component: About },
    {path: "/university", name: "university", component: University },
    {path: "/provinces", name: "provinces", component: Provinces },
    {path: "/departments", name: "departments", component: Departments },
    {path:"/faculties", name:"faculties" , component:Faculties}
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
