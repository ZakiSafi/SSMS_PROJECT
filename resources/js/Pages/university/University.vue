<template>
  <CreateUniversity v-if="UniversityRepository.createDialog" />

  <div class="all-expense rounded-xl">
    <div class="card rounded-xl">
      <AppBar mainTitle="University" subTitle="jawad" />
      
      <!-- Divider between AppBar and content -->
      <v-divider :thickness="1" class="border-opacity-100"></v-divider>

      <!-- Search & Buttons Section -->
      <div class="btn-search pt-12 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field
            :loading="loading"
            color="primary"
            density="compact"
            variant="outlined"
            label="Search"
            append-inner-icon="mdi-magnify"
            hide-details
            v-model="UniversityRepository.uniSearch"
            class="search-field"
          ></v-text-field>
        </div>

        <div class="btn">
          <v-btn variant="outlined" color="primary" class="px-6">Filter</v-btn>
          &nbsp;
          <v-btn @click="CreateDialogShow" color="primary" variant="flat" class="px-6">
            Create
          </v-btn>
        </div>
      </div>

      <!-- Data Table Section -->
      <v-data-table-server
        :dir="dir"
        v-model:items-per-page="UniversityRepository.itemsPerPage"
        :headers="headers"
        :items-length="UniversityRepository.totalItems"
        :items="UniversityRepository.universities"
        :loading="UniversityRepository.loading"
        :search="UniversityRepository.uniSearch"
        @update:options="UniversityRepository.FetchUniversities"
        class="w-100 mx-auto"
        hover
      >
        <template v-slot:item.action="{ item }">
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn icon="mdi-dots-vertical" v-bind="props" variant="text" />
            </template>
            <v-list>
              <v-list-item>
                <v-list-item-title @click="edit(item)" class="cursor-pointer d-flex gap-3 pb-3">
                  <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                  Edit
                </v-list-item-title>
                <v-list-item-title @click="deleteItem(item)" class="cursor-pointer d-flex gap-3">
                  <v-icon color="error">mdi-delete-outline</v-icon>
                  Delete
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table-server>
    </div>
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import CreateUniversity from "./CreateUniversity.vue";
import { ref } from "vue";
import { useUniversityRepository } from "@/store/UniversityRepository";

const UniversityRepository = useUniversityRepository();

const CreateDialogShow = () => {
  UniversityRepository.university = {};
  UniversityRepository.isEditMode = false;
  UniversityRepository.createDialog = true;
};

const edit = (item) => {
  UniversityRepository.isEditMode = true;
  UniversityRepository.university = {};
  UniversityRepository.FetchUniversity(item.id)
    .then(() => {
      UniversityRepository.createDialog = true;
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
};

const deleteItem = async (item) => {
  await UniversityRepository.DeleteUniversity(item.id);
};

const headers = [
  { title: "Name", key: "name", align: "start", sortable: false },
  { title: "Province", key: "province.name", align: "center", sortable: false },
  { title: "Action", key: "action", align: "end", sortable: false },
];
</script>

<style scoped>

</style>

