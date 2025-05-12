<template>
  <div class="sidebar-container">
    <!-- Top menu -->
    <div class="menu-section">
      <v-list density="compact" nav>
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-map-marker"
          title="Provinces"
          to="/provinces"
        />
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-account"
          title="Departments"
          to="/departments"
        />
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-account-group-outline"
          title="Users"
          to="/users"
        />
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-account-group-outline"
          title="University"
          to="/university"
        />
      </v-list>
    </div>

    <!-- Logout -->
    <div class="logout-section">
      <v-divider />
      <v-list-item
        class="logout-item"
        prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
        title="Logout"
        value="logout"
        nav
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

const drawer = ref(true);
const rail = ref(false);
</script>

<style scoped>
.sidebar-container {
  height: 100%;
  background-color: #F1F2F7;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 16px 8px;
}

.menu-section {
  flex-grow: 1;
}

.menu-item {
  border-radius: 8px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin: 4px 0;
  color: #333;
}



.menu-item:hover {
  background-color: #04529d1a !important;
  color: #5A6ACF !important;
  transform: translateX(4px);
}

.menu-item:hover .v-icon {
  color: #fff !important;
  transform: scale(1.1);
}

.menu-item.v-list-item--active {
  /* background-color: #707FDD !important; */
  color: #5A6ACF!important;
  border-left: 4px solid #5A6ACF;
}

.menu-item.v-list-item--active .v-icon {
  color: #5A6ACF !important;
}

.logout-section {
  padding: 8px 0;
}





/* RTL support */
[dir="rtl"] .menu-item:hover {
  transform: translateX(-4px);
}

[dir="rtl"] .menu-item.v-list-item--active {
  border-left: none;
  border-right: 4px solid #04529D;
}

[dir="rtl"] .menu-item .v-icon,
[dir="rtl"] .logout-item .v-icon {
  margin-right: 0;
  margin-left: 12px;
}
</style>