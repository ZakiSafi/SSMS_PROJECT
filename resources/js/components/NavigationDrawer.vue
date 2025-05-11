<template>
  <div class="sidebar-container">
    <!-- Top menu -->
    <div class="menu-section">
      <v-list density="compact" nav>
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-map-marker-radius"
          title="Provinces"
          to="/provinces"
        />
        <v-list-item
          class="menu-item"
          prepend-icon="mdi-account"
          title="My Account"
          to="/account"
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
  background-color: #F8F8F8;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding-top: 16px;
}

.menu-section {
  flex-grow: 1;
}

.menu-item {
  border-radius: 8px;
  transition: all 0.3s ease;
  padding-left: 16px;
  margin: 4px 8px;
  color: #333;
}

.menu-item:hover {
  background-color: #04529D !important;
  color: #fff !important;
}

.menu-item:hover .v-icon {
  color: #fff !important;
}

.menu-item.v-list-item--active {
  background-color: #04529D !important;
  color: white !important;
}

.menu-item.v-list-item--active .v-icon {
  color: white !important;
}

.logout-section {
  padding-bottom: 16px;
}

.logout-item {
  padding-left: 16px;
  margin: 4px 8px;
  cursor: pointer;
}

.logout-item:hover {
  background-color: rgba(0, 0, 0, 0.04);
}
</style>