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
import StudentTypeReport from "./Pages/report/StudentTypeReport.vue";
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
import UpdateRolePermission from "./Pages/role/UpdateRole.vue";
import Logs from "./Pages/log/log.vue";

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
                meta: { authentication: true, permissions: ["dashboard.view"] },
            },
            {
                path: "/about",
                name: "about",
                component: About,
                meta: { authentication: true },
            },
            {
                path: "/university",
                name: "university",
                component: University,
                meta: {
                    authentication: true,
                    permissions: ["university.view"],
                },
            },
            {
                path: "/users",
                name: "users",
                component: Users,
                meta: { authentication: true, permissions: ["settings.view"] },
            },
            {
                path: "/logs",
                name: "logs",
                component: Logs,
                meta: { authentication: true, permissions: ["settings.view"] },
            },
            {
                path: "/provinces",
                name: "provinces",
                component: Provinces,
                meta: { authentication: true, permissions: ["provinces.view"] },
            },
            {
                path: "/departments",
                name: "departments",
                component: Departments,
                meta: {
                    authentication: true,
                    permissions: ["departments.view"],
                },
            },
            {
                path: "/faculties",
                name: "faculties",
                component: Faculties,
                meta: { authentication: true, permissions: ["faculties.view"] },
            },
            {
                path: "/student-statistic",
                name: "student-statistic",
                component: StudentStatistics,
                meta: {
                    authentication: true,
                    permissions: ["student_statistic.view"],
                },
            },
            {
                path: "/university-base-report",
                name: "university-base-report",
                component: UniversityBaseReport,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/university-graduation-report",
                name: "university-graduation-report",
                component: UniversityBaseGraduation,
                meta: {
                    authentication: true,
                    permissions: ["graduated_students.view"],
                },
            },
            {
                path: "students-type",
                name: "students-type",
                component: StudentTypeReport,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/student-teacher-ratio",
                name: "student-teacher-ratio",
                component: StudentTeacherRatio,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/university-classes",
                name: "university-classes",
                component: UniversitiesClasses,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/faculty_base",
                name: "jawad",
                component: FacultyBase,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/deparment_base",
                name: "fawad",
                component: DepartmentBase,
                meta: {
                    authentication: true,
                    permissions: ["current_students.view"],
                },
            },
            {
                path: "/faculty-graduation",
                name: "faculty-graduation",
                component: FacultyBaseGraduation,
                meta: {
                    authentication: true,
                    permissions: ["graduated_students.view"],
                },
            },
            {
                path: "/department-base-graduation",
                name: "department-base-graduation",
                component: DepartmentBaseGraduation,
                meta: {
                    authentication: true,
                    permissions: ["graduated_students.view"],
                },
            },
            {
                path: "/teachers",
                name: "teachers",
                component: Teachers,
                meta: { authentication: true, permissions: ["teachers.view"] },
            },
            {
                path: "/role-permission",
                name: "role-permission",
                component: rolePermission,
                meta: { authentication: true, permissions: ["settings.view"] },
            },
            {
                path: "/CreateRole",
                name: "create-permission",
                component: CreateRolePermission,
                meta: {
                    authentication: true,
                    permissions: ["settings.create"],
                },
            },
            {
                path: "/UpdateRole/:id",
                props: true,
                name: "update-role",
                component: UpdateRolePermission,
                meta: { authentication: true, permissions: ["settings.edit"] },
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
    const userPermissions = JSON.parse(
        sessionStorage.getItem("permissions") || "[]"
    );

    // Route requires login
    if (to.meta.authentication && !token) {
        next("/");
    }
    // User is logged in and tries to visit login page
    else if (to.path === "/" && token) {
        next("/home");
    }
    // Route requires specific permissions
    else if (to.meta.permissions) {
        const hasPermission = to.meta.permissions.every((p) =>
            userPermissions.includes(p)
        );
        if (hasPermission) {
            next();
        } else {
            next("/unauthorized"); // You can show a page or redirect
        }
    } else {
        next();
    }
});

export default router;
