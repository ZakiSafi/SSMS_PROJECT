<template>
  <div class="sidebar-container">
    <!-- Logo/User Info -->
    <div class="logo d-flex pl-6 pb-3">
      <v-list-item
        prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
        title="Jawad"
      />
    </div>

    <v-divider :thickness="1" class="border-opacity-100 full"></v-divider>

    <!-- Sidebar Menu -->
    <div class="menu-section">
      <v-list density="compact" nav>
        <v-list-item 
          class="menu-item" 
          prepend-icon="mdi-map-marker" 
          title="Provinces" 
          to="/home/provinces" 
          value="provinces"
        />
        <v-list-item 
          class="menu-item" 
          prepend-icon="mdi-account" 
          title="Departments" 
          to="/home/departments" 
          value="departments"
        />
        <v-list-item 
          class="menu-item" 
          prepend-icon="mdi-account-group-outline" 
          title="Users" 
          to="/home/users" 
          value="users"
        />
        <v-list-item 
          class="menu-item" 
          prepend-icon="mdi-school" 
          title="University" 
          to="/home/university" 
          value="university"
        />
        <v-list-item 
          class="menu-item" 
          prepend-icon="mdi-domain" 
          title="Faculty" 
          to="/home/faculties" 
          value="faculties"
        />
      </v-list>
    </div>
  </div>
</template>

<script setup>
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
</style>