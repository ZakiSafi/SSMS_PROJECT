<template>
  <CreateProvince v-if="ProvinceRepository.createDialog" />

    <div :dir="dir">
      <AppBar pageTitle="provinces" />

      <!-- Divider between AppBar and content -->
       
      <v-divider :thickness="1" class="border-opacity-100 "></v-divider>

      <!-- Search & Buttons Section -->
      <div class="pt-6 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field color="primary" density="compact" variant="outlined" :label="t('search')"
            append-inner-icon="mdi-magnify" hide-details v-model="ProvinceRepository.provinceSearch"
            class="search-field"></v-text-field>
        </div>

        <div>
          &nbsp;
          <v-btn @click="CreateDialogShow" color="primary" variant="flat" class="px-6">
            {{ $t('create') }}
          </v-btn>
        </div>
      </div>

      <!-- Data Table Section -->
      <v-data-table-server 
      :dir="dir"
      v-model:items-per-page="ProvinceRepository.itemsPerPage" :headers="headers"
        :items-length="ProvinceRepository.totalItems" :items="ProvinceRepository.provinces"
        :loading="ProvinceRepository.loading" :search="ProvinceRepository.provinceSearch"
        @update:options="ProvinceRepository.FetchProvinces" class="w-100 mx-auto" hover>
        <template v-slot:item.action="{ item }">

          <v-card flat fluid >
            <button @click="edit(item)" class="cursor-pointer  pb-3"> <v-icon
                color="tealColor">mdi-square-edit-outline</v-icon>
            </button>
            <button @click="deleteItem(item)" class="cursor-pointer">
              <v-icon color="error">mdi-delete-outline</v-icon>

            </button>
          </v-card>
          <!-- <v-list>
            <v-list-item>
              <v-list-item-title @click="edit(item)" class="cursor-pointer  pb-3">
                <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                Edit
              </v-list-item-title>
              <v-list-item-title @click="deleteItem(item)" class="cursor-pointer">
                <v-icon color="error">mdi-delete-outline</v-icon>
                Delete
              </v-list-item-title>
            </v-list-item>
          </v-list> -->
        </template>
      </v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import {computed } from "vue";
import { useProvinceRepository } from "@/store/ProvinceRepository";
import CreateProvince from "./CreateProvince.vue";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});

const ProvinceRepository = useProvinceRepository();

const CreateDialogShow = () => {
  ProvinceRepository.province = {};
  ProvinceRepository.isEditMode = false;
  ProvinceRepository.createDialog = true;
};

const edit = (item) => {
  ProvinceRepository.isEditMode = true;
  ProvinceRepository.province = {};
  ProvinceRepository.FetchProvince(item.id)
    .then(() => {
      ProvinceRepository.createDialog = true;
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
};

const deleteItem = async (item) => {
  await ProvinceRepository.DeleteProvince(item.id);
};

const headers = [
  { title: "Name", key: "name", align: "start", sortable: false },
  { title: "Action", key: "action", align: "end", sortable: false },
];
</script>

<style scoped></style>
