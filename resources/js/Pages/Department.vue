<template>
  <div class="d-flex">

    <!-- <NavigationDrawer /> -->

    <!-- Main Content -->
    <div class="flex-grow-1">
      <v-data-table-server v-model:items-per-page="itemsPerPage"
       :headers="headers" 
       :items="serverItems"
       :items-length="totalItems"
       :loading="loading" :search="search" item-value="name" @update:options="loadItems" />
    </div>
  </div>
</template>

<script setup>
import NavigationDrawer from '../components/NavigationDrawer.vue';
</script>