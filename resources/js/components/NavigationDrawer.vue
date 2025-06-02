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
            <h3>{{ $t('user.name') }}</h3>
        </div>

        <v-divider :thickness="1" class="border-opacity-100 full"></v-divider>

        <!-- Sidebar Menu -->
        <div class="menu-section">
            <v-list density="compact" nav>
                <!-- Main Items -->
                <v-list-item
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
                    prepend-icon="mdi-home-city-outline"
                    :title="$t('menu.faculty')"
                    to="/faculties"
                    value="faculties"
                    :class="{
                        'v-list-item--active': route.path === '/faculties',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    prepend-icon="mdi-office-building-outline"
                    :title="$t('menu.departments')"
                    to="/departments"
                    value="departments"
                    :class="{
                        'v-list-item--active': route.path === '/departments',
                    }"
                />
                <v-list-item
                    class="menu-item"
                    prepend-icon="mdi-account-group-outline"
                    :title="$t('menu.student_statistic')"
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
                            :title="$t('menu.reports')"
                            prepend-icon="mdi-file-chart-outline"
                            class="menu-item"
                        />
                    </template>

                    <!-- Submenu Items -->
                    <v-list-item
                        v-for="(item, index) in reportItems"
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
import { useRoute } from "vue-router";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const route = useRoute();

const settingItems = [
    {
        to: "/users",
        translationKey: "users",
        icon: "mdi-circle-medium",
        value: "users",
    },
    {
        to: "/role-permissions",
        translationKey: "role_permissions",
        icon: "mdi-circle-medium",
        value: "roles",
    },
];

const reportItems = [
    {
        to: "/university-base-report",
        translationKey: "university_report",
        icon: "mdi-circle-medium",
        value: "university-base-report",
    },
    {
        to: "/university-graduation-report",
        translationKey: "university_graduation_report",
        icon: "mdi-circle-medium",
        value: "university-graduation-report",
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
        to: "/jawad",
        translationKey: "jawad",
        icon: "mdi-circle-medium",
        value: "university-classes",
    },
    {
        to:"/fawad",
        translationKey:"fawad",
        icon:"mdi-circular-medium",
        value:"fawad"
    }
];
</script>

<style scoped>
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

.submenu-item {
    padding-left: 32px !important;
    margin: 0 !important;
    min-height: 32px !important;
}

:deep(.v-list-group__items .v-list-item) {
    padding-left: 16px !important;
    min-height: 32px !important;
}

:deep(.v-list-group__items) {
    padding-top: 10px !important;
    padding-bottom: 0 !important;
}
</style>