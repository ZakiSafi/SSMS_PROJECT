<template>
    <div class="sidebar-container">
        <!-- Logo/User Info -->
        <div class="d-flex pl-4 align-center">
            <v-list-item>
                <img
                    src="../../../public/assets/IMG_20230530_185847_332.jpg"
                    alt=""
                    class="w-[2.9rem] h-[2.9rem] rounded-full object-cover transition-all duration-300"
                />
            </v-list-item>
            <h3>{{ user.name || "Guest" }}</h3>
        </div>

        <v-divider :thickness="1" class="border-opacity-100 full"></v-divider>

        <!-- Sidebar Menu -->
        <div class="menu-section">
            <v-list density="compact" nav>
                <!-- Main Items -->
                <v-list-item
                    v-if="hasPermission('dashboard.view')"
                    class="menu-item"
                    :title="$t('menu.dashboard')"
                    prepend-icon="mdi-home-lightning-bolt-outline"
                    to="/dashboard"
                    value="dashboard"
                    :class="{
                        'v-list-item--active': route.path === '/dashboard',
                    }"
                />
                <v-list-item
                    v-if="hasPermission('provinces.view')"
                    class="menu-item"
                    prepend-icon="mdi-map-marker-outline"
                    :title="$t('menu.provinces')"
                    to="/provinces"
                    value="provinces"
                    :class="{
                        'v-list-item--active': route.path === '/provinces',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    v-if="hasPermission('university.view')"
                    prepend-icon="mdi-school-outline"
                    :title="$t('menu.university')"
                    to="/university"
                    value="university"
                    :class="{
                        'v-list-item--active': route.path === '/university',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    v-if="hasPermission('faculties.view')"
                    prepend-icon="mdi-home-city-outline"
                    :title="$t('menu.faculties')"
                    to="/faculties"
                    value="faculties"
                    :class="{
                        'v-list-item--active': route.path === '/faculties',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    prepend-icon="mdi-office-building-outline"
                    v-if="hasPermission('departments.view')"
                    :title="$t('menu.departments')"
                    to="/departments"
                    value="departments"
                    :class="{
                        'v-list-item--active': route.path === '/departments',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    prepend-icon="mdi-office-building-outline"
                    v-if="hasPermission('teachers.view')"
                    :title="$t('menu.teachers')"
                    to="/teachers"
                    value="teachers"
                    :class="{
                        'v-list-item--active': route.path === '/teachers',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    prepend-icon="mdi-account-group-outline"
                    :title="$t('menu.student_statistic')"
                    v-if="hasPermission('student_statistic.view')"
                    to="/student-statistic"
                    value="system"
                    :class="{
                        'v-list-item--active':
                            route.path === '/student-statistic',
                    }"
                />

                <!-- Reports Group -->
                <v-list-group>
                    <template #activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :title="$t('menu.currentStudents')"
                            v-if="hasPermission('current_students.view')"
                            prepend-icon="mdi-file-chart-outline"
                            class="menu-item"
                        />
                    </template>

                    <!-- Submenu Items -->
                    <v-list-item
                        v-for="(item, index) in currentStudents"
                        :key="index"
                        :to="item.to"
                        :title="$t(`menu.${item.translationKey}`)"
                        class="submenu-item"
                        :value="item.value"
                        :prepend-icon="item.icon"
                        :class="{
                            'v-list-item--active': route.path === item.to,
                        }"
                    />

                    <!-- Graduated Students -->
                </v-list-group>
                <v-list-group>
                    <template #activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            v-if="hasPermission('graduated_students.view')"
                            :title="$t('menu.graduated_students')"
                            prepend-icon="mdi-file-chart-outline"
                            class="menu-item"
                        />
                    </template>

                    <!-- Submenu Items -->
                    <v-list-item
                        v-for="(item, index) in graduatedStudents"
                        :key="index"
                        :to="item.to"
                        :title="$t(`menu.${item.translationKey}`)"
                        class="submenu-item"
                        :value="item.value"
                        :prepend-icon="item.icon"
                        :class="{
                            'v-list-item--active': route.path === item.to,
                        }"
                    />
                </v-list-group>

                <!-- Settings Group -->
                <v-list-group>
                    <template #activator="{ props }">
                        <v-list-item
                            v-bind="props"
                            :title="$t('menu.settings')"
                            v-if="hasPermission('settings.view')"
                            prepend-icon="mdi-cog-outline"
                            class="menu-item"
                        />
                    </template>

                    <!-- Submenu Items -->
                    <v-list-item
                        v-for="(item, index) in settingItems"
                        :key="index"
                        :to="item.to"
                        :title="$t(`menu.${item.translationKey}`)"
                        class="submenu-item"
                        :value="item.value"
                        :prepend-icon="item.icon"
                        :class="{
                            'v-list-item--active': route.path === item.to,
                        }"
                    />
                </v-list-group>
            </v-list>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import { useAuthRepository } from "../store/AuthRepository";
const AuthRepository = useAuthRepository();

const { t } = useI18n();
const route = useRoute();

const user = ref({
    name: "",
    email: "",
    photo: "",
});

const permissions = ref([]);

onMounted(() => {
    const storedPermissions = sessionStorage.getItem("permissions");
    if (storedPermissions) {
        permissions.value = JSON.parse(storedPermissions);
    }

    const storedUser = sessionStorage.getItem("user");
    if (storedUser) {
        const parsed = JSON.parse(storedUser);
        user.value.name = parsed.name;
        user.value.email = parsed.email;
        user.value.photo = parsed.photo;
    }
});

// Utility to check permission
const hasPermission = (permission) => {
    return permissions.value.includes(permission);
};

const settingItems = [
    {
        to: "/users",
        translationKey: "users",
        icon: "mdi-circle-medium",
        value: "users",
    },
    {
        to: "/role-permission",
        translationKey: "role_permissions",
        icon: "mdi-circle-medium",
        value: "roles",
    },
    {
        to: "/logs",
        translationKey: "logs",
        icon: "mdi-circle-medium",
        value: "logs",
    },
];

const currentStudents = [
    {
        to: "/university-base-report",
        translationKey: "university_report",
        icon: "mdi-circle-medium",
        value: "university-base-report",
    },
    {
        to: "/student-teacher-ratio",
        translationKey: "student_teacher_ratio",
        icon: "mdi-circle-medium",
        value: "student-teacher-ratio",
    },
    {
        to: "/university-classes",
        translationKey: "university_classes",
        icon: "mdi-circle-medium",
        value: "university-classes",
    },
    {
        to: "/faculty_base",
        translationKey: "faculty_base",
        icon: "mdi-circle-medium",
        value: "Faculty Base Report",
    },
    {
        to: "/deparment_base",
        translationKey: "deparment_base",
        icon: "mdi-circle-medium",
        value: "fawad",
    },
    {
        to: "students-type", // Frontend route
        translationKey: "student_type_report",
        icon: "mdi-circle-medium",
        value: "students-type",
    },
];
const graduatedStudents = [
    {
        to: "/university-graduation-report",
        translationKey: "university_graduation_report",
        icon: "mdi-circle-medium",
        value: "university-graduation-report",
    },

    {
        to: "/faculty-graduation",
        translationKey: "faculty_graduation",
        icon: "mdi-circle-medium",
        value: "faculty-graduation",
    },
    {
        to: "/department-base-graduation",
        translationKey: "department_base_graduation",
        icon: "mdi-circle-medium",
        value: "department-base-graduation",
    },
];
</script>

<style scoped>
/* Add RTL specific styles */
[dir="rtl"] .submenu-item {
    padding-right: 32px !important;
}

[dir="rtl"] :deep(.v-list-group__items) {
    margin-right: 0 !important;
    margin-left: 16px !important;
}
.sidebar-container {
    height: 100vh;
    background-color: #f8f9fa;
    display: flex;
    flex-direction: column;
    padding: 16px 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.menu-section {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    min-height: 0; /* Crucial for flex scrolling */
}

.scrollable-list {
    height: 100%;
    overflow-y: auto;
    overflow-x: hidden;
}

:deep(.v-list-item__content) {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: initial !important;
}

.menu-item {
    transition: all 0.3s ease;
    margin: 4px 8px;
    color: #5f6368;
}

.menu-item:hover {
    background-color: #f5f5f5;
}

.menu-item.v-list-item--active {
    color: #009ee2 !important;
    background-color: #e7f2f5;
    font-weight: 500;
    border-left: 4px solid #009ee2;
}

.menu-item.v-list-item--active .v-icon {
    color: #009ee2 !important;
}

[dir="rtl"] .menu-item.v-list-item--active {
    border-left: none;
    border-right: 4px solid #009ee2;
}

:deep(.v-list-item--active) {
    --v-theme-primary: #009ee2;
}

:deep(.v-list-group__header .v-list-item__append i) {
    display: none !important;
}

/* .submenu-item > :nth-child(1) > :nth-child(1){
    margin: 0 !important;
   
    display: flex;
    justify-content: flex-end;
    background-color: red;
} */

.v-list-group__items .v-list-item--nav.v-list-item {
    padding-left: 16px !important; /* or 8px */
    min-height: 32px !important;
}

.v-list-group__items .v-list-item__prepend {
    margin-inline-start: 0px !important;
}

.v-list-group__items .v-list-item__content {
    margin-left: 4px; /* Adjust spacing between icon and text */
}

:deep(.v-list-group__items) {
    padding-top: 10px !important;
    padding-bottom: 0 !important;
}

/* Specifically target the bullet points in submenus */
:deep(.v-list-group__items .v-list-item .v-list-item__prepend) {
    width: 0px !important;
    min-width: 20px !important;
    margin-inline-end: 4px !important;
}

/* .submenu-item > :nth-child(1){
    background-color: red;
    display: flex;
    justify-content: flex-start;
} */
</style>
