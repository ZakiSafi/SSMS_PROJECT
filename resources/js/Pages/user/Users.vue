<template>
  <CreateUser v-if="UserRepository.createDialog" />
    <div>
    <AppBar pageTitle="users" />
    <!-- Divider between AppBar and content -->
    <v-divider :thickness="1" class="border-opacity-100 "></v-divider>
    <!-- Search and Create Button -->
    <div class="btn-search d-flex justify-space-between pt-12 pb-6">
      <div class="text-field w-25">
        <v-text-field
          color="primary"
          density="compact"
         variant="filled"
          label="Search"
          append-inner-icon="mdi-magnify"
          hide-details
          v-model="UserRepository.userSearch"
          class="search-field"
        />
      </div>
      <div>
        <v-btn @click="createDialogShow" color="primary" variant="flat" class="px-6">Create</v-btn>
      </div>
    </div>

    <!-- Data Table -->
    <v-data-table-server
      v-model:items-per-page="UserRepository.itemsPerPage"
      :headers="headers"
      :items-length="UserRepository.totalItems"
      :items="UserRepository.users"
      :loading="UserRepository.loading"
      :search="UserRepository.userSearch"
      @update:options="UserRepository.fetchUsers"
      class="w-100 mx-auto"
      hover
    >
      <!-- Action Slot -->
      <template v-slot:item.action="{ item }">
        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn icon="mdi-dots-vertical" v-bind="props" variant="text" />
          </template>
          <v-list>
            <v-list-item>
              <v-list-item-title @click="edit(item)" class="cursor-pointer pb-3">
                <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                Edit
              </v-list-item-title>
              <v-list-item-title @click="deleteItem(item)" class="cursor-pointer pb-3">
                <v-icon color="error">mdi-delete-outline</v-icon>
                Delete
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-data-table-server>
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import CreateUser from "./CreateUser.vue";
import { useUserRepository } from "@/store/UserRepository";

const UserRepository = useUserRepository();

const createDialogShow = () => {
  UserRepository.department = {};
  UserRepository.isEditMode = false;
  UserRepository.createDialog = true;
};

const edit = (item) => {
  UserRepository.isEditMode = true;
  UserRepository.user = {};
  UserRepository.fetchUser(item.id)
    .then(() => {
      UserRepository.createDialog = true;
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
};

const deleteItem = async (item) => {
  await UserRepository.deleteUser(item.id);
};

const headers = [
  { title: "Name", key: "name", align: "start", sortable: false },
  { title: "Email", key: "email", align: "center", sortable: false },
  { title: "University", key: "university.name", align: "center", sortable: false },
  { title: "Action", key: "action", align: "end", sortable: false },
];
</script>

<style scoped></style>
