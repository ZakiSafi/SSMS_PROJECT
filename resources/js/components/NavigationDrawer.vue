<template>
  <div class="sidebar-container">
    <!-- Logo/User Info -->
    <div class="d-flex pl-4 align-center ">
      <v-list-item><img src="../../../public/assets/Park.jpg" alt=""
          class="w-[2.9rem] h-[2.9rem] rounded-full object-cover transition-all duration-300"></v-list-item>
      <h3>Jawad</h3>
    </div>

    <v-divider :thickness="1" class="border-opacity-100 full"></v-divider>

    <!-- Sidebar Menu -->
    <div class="menu-section">
      <v-list density="compact" nav>
        <!-- Main Items -->
        <v-list-item class="menu-item" prepend-icon="mdi-map-marker" title="Provinces" to="/provinces" value="provinces"
          :class="{ 'v-list-item--active': route.path === '/provinces' }" />
        <v-list-item class="menu-item" prepend-icon="mdi-account" title="Departments" to="/departments"
          value="departments" :class="{ 'v-list-item--active': route.path === '/departments' }" />
        
        <v-list-item class="menu-item" prepend-icon="mdi-school" title="University" to="/university" value="university"
          :class="{ 'v-list-item--active': route.path === '/university' }" />
        <v-list-item class="menu-item" prepend-icon="mdi-domain" title="Faculty" to="/faculties" value="faculties"
          :class="{ 'v-list-item--active': route.path === '/faculties' }" />
          <v-list-item class="menu-item" prepend-icon="mdi-account-group-outline" title="Student Statictic" to="/student-statistic" value="system"
          :class="{ 'v-list-item--active': route.path === '/student-statistic' }" />

         <!-- Reports Group -->
           <v-list-group >
          <template #activator="{ props }">
            <v-list-item v-bind="props" title="Reports" prepend-icon="mdi-file-chart" class="menu-item"
               />
          </template>

          <!-- Submenu Items -->
          <v-list-item v-for="(item, index) in reportItems" :key="index" :to="item.to" :title="item.title"
            class="submenu-item" :value="item.value" :prepend-icon="item.icon"
            :class="{ 'v-list-item--active': route.path === item.to }" />
        </v-list-group>

        <!-- Settings Group -->
        <v-list-group >
          <template #activator="{ props }">
            <v-list-item v-bind="props" title="Settings" prepend-icon="mdi-cog" class="menu-item"
               />
          </template>

          <!-- Submenu Items -->
          <v-list-item v-for="(item, index) in settingItems" :key="index" :to="item.to" :title="item.title"
            class="submenu-item" :value="item.value" :prepend-icon="item.icon"
            :class="{ 'v-list-item--active': route.path === item.to }" />
        </v-list-group>
       
      </v-list>
    </div>
  </div>
</template>

<script setup>
import { useRoute } from 'vue-router';

const route = useRoute();

const settingItems = [
  {
    to: "/users",
    title: "Users",
    icon: "mdi-circle-medium",
    value: "users",
  },
  {
    to: "/role-permissions",
    title: "Role Permission",
    icon: "mdi-circle-medium",
    value: "roles",
  },
]


const reportItems=[
  {
    to: "/university-base-report",
    title: "University Report",
    icon: "mdi-circle-medium",
    value: "university-base-report",
  },
  {
    to: "/university-graduation-report",
    title: "University Graduation Report",
    icon: "mdi-circle-medium",
    value: "university-graduation-report",
  },
  {
    to: "/student-teacher-ratio",
    title: "Student Teacher Ratio",
    icon: "mdi-circle-medium",
    value: "student-teacher-ratio",
  },
  {
    to: "/university-classes",
    title: "UniversitiesClasses",
    icon: "mdi-circle-medium",
    value: "university-classes",
  },
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
}

.menu-section {
  flex-grow: 1;
  padding-top: 8px;
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
  color: #009EE2 !important;
  background-color: #E7F2F5;
  font-weight: 500;
  border-left: 4px solid #009EE2;
}

.menu-item.v-list-item--active .v-icon {
  color: #009EE2 !important;
}

[dir="rtl"] .menu-item.v-list-item--active {
  border-left: none;
  border-right: 4px solid #009EE2;
}

/* Active state with new primary color */
:deep(.v-list-item--active) {
  --v-theme-primary: #009EE2;
}

/* Remove the default dropdown arrow */
:deep(.v-list-group__header .v-list-item__append i) {
  display: none !important;
}

/* Tighter spacing for submenus */
.submenu-item {
  padding-left: 32px !important;
  margin: 0 !important;
  min-height: 32px !important;

}

/* Reduce padding for submenu items */
:deep(.v-list-group__items .v-list-item) {
  padding-left: 16px !important;
  min-height: 32px !important;
}

/* Reduce the gap between submenu items */
:deep(.v-list-group__items) {
  padding-top: 10px !important;
  padding-bottom: 0 !important;
}
</style>
