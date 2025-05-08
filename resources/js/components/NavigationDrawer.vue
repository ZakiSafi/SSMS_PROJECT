<template >



<div class="d-flex flex-column justify-space-between h-100">
  <div>
            <v-list-item>
              <template v-slot:append>
                <v-btn icon="mdi-chevron-left" variant="text" @click.stop="rail = !rail"></v-btn>
              </template>
            </v-list-item>

            <v-list density="compact" nav>
              <v-list-item class="menu-item" prepend-icon="mdi-home-city" title="Home" value="home" to="/department" />
              <v-list-item class="menu-item" prepend-icon="mdi-account" title="My Account" value="account" />
              <v-list-item class="menu-item" prepend-icon="mdi-account-group-outline" title="Users" value="users" />
              <v-list-item class="menu-item" prepend-icon="mdi-account-group-outline" title="university" to="/university" />
            </v-list>
          </div>

          <!-- 👇Logout-->
          <div>
            <v-divider />
            <v-list-item
              prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
              title="Logout"
              value="logout"
              nav
            />
          </div>
        </div>
    



</template>

  <script setup>
  import { ref } from 'vue'

  const drawer = ref(true)
  const rail = ref(false)
</script>
  <style scoped>



  .scrollable-content {
    background-color: #fff;
    border-right: 1px solid #e0e0e0;
    padding: 2px;
    box-shadow: 2px 0 6px rgba(0, 0, 0, 0.04);
    height: 70vh;

    /* Hide scrollbar across browsers */
    overflow-y: scroll;
    scrollbar-width: none;
    -ms-overflow-style: none;
  }

  .scrollable-content::-webkit-scrollbar {
    display: none;
  }

  .scrollable-content::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .scrollable-content::-webkit-scrollbar-thumb {
    background: #b1b1b1;
    border-radius: 4px;
  }

  .scrollable-content::-webkit-scrollbar-thumb:hover {
    background: #a6a5a5;
  }

  .menu-item {
    border-radius: 8px;
    transition: background-color 0.3s ease, color 0.3s ease;
    padding-left: 16px;
  }

  .menu-item:hover {
    background-color: #695CFE !important;
    color: #fff !important;
  }

  .menu-item:hover .v-icon {
    color: #695CFE !important;
  }

  .menu-item.v-list-item--active {
    background-color: #695CFE !important;
    color: white !important;
  }

  .menu-item.v-list-item--active .v-icon {
    color: white !important;
  }
</style>