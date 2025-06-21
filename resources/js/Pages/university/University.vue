<template>
  <CreateUniversity v-if="UniversityRepository.createDialog" />

    <div :dir="dir">
      <AppBar pageTitle="Universities"  />
      <!-- Divider between AppBar and content -->
      <v-divider :thickness="1" class="border-opacity-100" ></v-divider>
      <!-- Search & Buttons Section -->
      <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field
            :loading="loading"
            color="primary"
            density="compact"
           variant="outlined"
            :label="t('search')"
            append-inner-icon="mdi-magnify"
            hide-details
            v-model="UniversityRepository.uniSearch"
            class="search-field"
          ></v-text-field>
        </div>

        <div class="btn">
          &nbsp;
          <v-btn @click="CreateDialogShow" color="primary" variant="flat" class="px-6">
            {{ $t('create') }}
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
      
      <template #bottom>
                <div class="d-flex align-center justify-end pa-2" >
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="UniversityRepository.itemsPerPage"
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
        <template v-slot:item.action="{ item }">
          <v-menu>
            <template v-slot:activator="{ props }">
              <v-btn icon="mdi-dots-vertical" v-bind="props" variant="text" />
            </template>
            <v-list>
              <v-list-item>
                <v-list-item-title @click="edit(item)" class="cursor-pointer d-flex gap-3 pb-3">
                  <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                  {{$t('Edit')}}

                </v-list-item-title>
                <v-list-item-title @click="deleteItem(item)" class="cursor-pointer d-flex gap-3">
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
import CreateUniversity from "./CreateUniversity.vue";
import { computed } from "vue";
import { useUniversityRepository } from "@/store/UniversityRepository";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});

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

const headers = computed(() => [
  { title: t("Name"), key: "name", align: "start", sortable: false },
  { title: t("Province"), key: "province.name", align: "center", sortable: false },
  { title: t("University Type"), key: "type", align: "center", sortable: false },
  { title: t("Action"), key: "action", align: "end", sortable: false },
]);
</script>

<style scoped>

</style>

