import { createRouter, createWebHistory } from "vue-router";

// Pages
import Home from "./Home.vue";
import Login from "./Pages/login/login.vue";
import About from "./Pages/About.vue";
import University from "./Pages/university/University.vue";
import Users from "./Pages/user/Users.vue";
import Provinces from "./Pages/province/Province.vue";
import Departments from "./Pages/department/Departments.vue";
import Faculties from "./Pages/faculty/Faculties.vue";
import StudentStatistics from "./Pages/student/StudentStatistics.vue";
import UniversityBaseReport from "./Pages/report/UniversityBase.vue";
import UniversityBaseGraduation from "./Pages/report/UniversityBaseGraduation.vue";
import StudentTeacherRatio from "./Pages/report/StudentTeacherRatio.vue";
import UniversitiesClasses from "./Pages/report/UniversitiesClasses.vue";
import Dashboard from "./Pages/Dashboard.vue";
import FacultyBase from "./Pages/report/FacultyBase.vue";
import DepartmentBase from "./Pages/report/DeparmentBase.vue";
import FacultyBaseGraduation from "./Pages/report/FacultyBaseGraduation.vue";
import DepartmentBaseGraduation from "./Pages/report/DepartmentBaseGraduation.vue";
import Teachers from "./Pages/teacher/Teachers.vue";
import rolePermission from "./Pages/role/rolePermission.vue";
import CreateRolePermission from "./Pages/role/CreateRolePermission.vue";

const routes = [
    // Show login first
    { path: "/", name: "Login", component: Login, meta: "" },

    // All inner pages under /home
    {
        path: "/home",
        component: Home,
        meta: { authentication: true },
        children: [
            {
                path: "/dashboard",
                name: "dashboard",
                component: Dashboard,
                alias: "/dashboard",
            },
            { path: "/about", name: "about", component: About },
            { path: "/university", name: "university", component: University },
            { path: "/users", name: "users", component: Users },
            { path: "/provinces", name: "provinces", component: Provinces },
            {
                path: "/departments",
                name: "departments",
                component: Departments,
            },
            { path: "/faculties", name: "faculties", component: Faculties },
            {
                path: "/student-statistic",
                name: "student-statistic",
                component: StudentStatistics,
            },
            {
                path: "/university-base-report",
                name: "university-base-report",
                component: UniversityBaseReport,
            },
            {
                path: "/university-graduation-report",
                name: "university-graduation-report",
                component: UniversityBaseGraduation,
            },
            {
                path: "/student-teacher-ratio",
                name: "student-teacher-ratio",
                component: StudentTeacherRatio,
            },
            {
                path: "/university-classes",
                name: "university-classes",
                component: UniversitiesClasses,
            },
            { path: "/faculty_base", name: "jawad", component: FacultyBase },
            {
                path: "/deparment_base",
                name: "fawad",
                component: DepartmentBase,
            },
            {
                path: "/faculty-graduation",
                name: "faculty-graduation",
                component: FacultyBaseGraduation,
            },
            {
                path: "/department-base-graduation",
                name: "department-base-graduation",
                component: DepartmentBaseGraduation,
            },
            { path: "/teachers", name: "teachers", component: Teachers },
            // role
            {
                path: "/role-permission",
                name: "role-permission",
                component: rolePermission,
            },
            {
                path: "/CreateRole",
                name: "create-permission",
                component: CreateRolePermission,
            },
        ],
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const token = sessionStorage.getItem("token");

    // If the route requires authentication and the user is not logged in
    if (to.meta.authentication && !token) {
        next("/");
    }
    // If user is logged in and tries to access login page
    else if (to.path === "/" && token) {
        next("/home");
    }
    // Otherwise, allow the navigation
    else {
        next();
    }
});
export default router;
