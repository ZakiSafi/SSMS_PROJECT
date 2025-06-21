<template>
  <CreateDepartment v-if="DepartmentRepository.createDialog" />
    <div :dir="dir">
    <AppBar pageTitle="Department" />
    <!-- Divider between AppBar and content -->
    <v-divider :thickness="1" class="border-opacity-100 "></v-divider>
    <!-- Search and Create Button -->
    <div class="btn-search d-flex justify-space-between pt-12 pb-6">
      <div class="text-field w-25">
        <v-text-field
          color="primary"
          density="compact"
         variant="outlined"
          :label="t('search')"
          append-inner-icon="mdi-magnify"
          hide-details
          v-model="DepartmentRepository.departmentSearch"
          class="search-field"
        />
      </div>
      <div>
        <v-btn @click="createDialogShow" color="primary" variant="flat" class="px-6">{{$t('create')}}</v-btn>
      </div>
    </div>

    <!-- Data Table -->
    <v-data-table-server
      v-model:items-per-page="DepartmentRepository.itemsPerPage"
      :headers="headers"
      :items-length="DepartmentRepository.totalItems"
      :items="DepartmentRepository.departments"
      :loading="DepartmentRepository.loading"
      :search="DepartmentRepository.departmentSearch"
      @update:options="DepartmentRepository.FetchDepartments"
      class="w-100 mx-auto"
      hover
    >
    <!-- code for controlling the footer or item per page -->
    <template #bottom>
                <div class="d-flex align-center justify-end pa-2" >
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="DepartmentRepository.itemsPerPage"
                        :items="[
                            { value: 5, text: '5' },
                            { value: 10, text: '10' },
                            { value: 25, text: '25' },
                            { value: 50, text: '50' },
                            { value: -1, text: $t('pagination.all') },
                        ]"
                        item-title="text"
                        item-value="value"
                        density="compact"
                        variant="outlined"
                        hide-details
                        style="max-width: 100px"
                    ></v-select>
                </div>
            </template>

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
                {{$t('Edit')}}
              </v-list-item-title>
              <v-list-item-title @click="deleteItem(item)" class="cursor-pointer pb-3">
                <v-icon color="error">mdi-delete-outline</v-icon>
                {{$t('Delete')}}
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
import { computed } from "vue";
import CreateDepartment from "./CreateDepartment.vue";
import { useDepartmentRepository } from "@/store/DepartmentRepository";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});


const DepartmentRepository = useDepartmentRepository();

const createDialogShow = () => {
  DepartmentRepository.department = {};
  DepartmentRepository.isEditMode = false;
  DepartmentRepository.createDialog = true;
};

const edit = (item) => {
  DepartmentRepository.isEditMode = true;
  DepartmentRepository.department = {};
  DepartmentRepository.FetchDepartment(item.id)
    .then(() => {
      DepartmentRepository.createDialog = true;
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
};

const deleteItem = async (item) => {
  await DepartmentRepository.DeleteDepartment(item.id);
};

const headers =computed(()=> [
  { title: t("Name"), key: "name", align: "start", sortable: false },
  { title: t("Faculty"), key: "faculty.name", align: "center", sortable: false },
  { title: t("Action"), key: "action", align: "end", sortable: false },
]);
</script>

<style scoped></style>
