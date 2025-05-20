import { createRouter, createWebHistory } from "vue-router";

// Pages
import Home from "./Home.vue";
import Login from "./Pages/login/login.vue";
import About from "./Pages/About.vue";
import University from "./Pages/university/University.vue";
import Provinces from "./Pages/province/Province.vue";
import Departments from "./Pages/department/Departments.vue";
import Faculties from "./Pages/faculty/Faculties.vue";
import StudentStatistics from "./Pages/student/StudentStatistics.vue";

const routes = [
    // Show login first
    { path: "/", name: "Login", component: Login },

    // All inner pages under /home
    {
        path: "/home",
        component: Home,
        children: [
            { path: "/about", name: "about", component: About },
            { path: "/university", name: "university", component: University },
            { path: "/provinces", name: "provinces", component: Provinces },
            { path: "/departments", name: "departments", component: Departments },
            { path: "/faculties", name: "faculties", component: Faculties },
            { path: "/student-statistic", name: "student-statistic", component: StudentStatistics },
        ],
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
