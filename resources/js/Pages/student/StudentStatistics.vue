<template>
    <CreateStudentStatistic v-if="StudentStatisticRepository.createDialog" />
  
    <div :dir="dir">
      <AppBar :pageTitle="t('student_statistic')" />
  
      <v-divider :thickness="1" class="border-opacity-100" />
  
      <!-- Search & Create Button -->
      <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field color="primary" density="compact" variant="outlined" :label="t('search')"
            append-inner-icon="mdi-magnify" hide-details v-model="StudentStatisticRepository.departmentSearch" />
        </div>
  
        <v-btn @click="showCreateDialog" color="primary" variant="flat" class="px-6">
          {{ t('create') }}
        </v-btn>
      </div>
  
      <!-- Data Table -->
      <v-data-table-server :dir="dir" v-model:items-per-page="StudentStatisticRepository.itemsPerPage"
        :headers="headers" :items-length="StudentStatisticRepository.totalItems"
        :items="StudentStatisticRepository.statistics" :loading="StudentStatisticRepository.loading"
        :search="StudentStatisticRepository.departmentSearch" @update:options="StudentStatisticRepository.fetchStatistics"
        class="w-100 mx-auto" hover>
        
        <template #bottom>
          <div class="d-flex align-center justify-end pa-2">
            <span class="mx-2">{{ t("pagination.items_per_page") }}</span>
            <v-select
              v-model="StudentStatisticRepository.itemsPerPage"
              :items="[
                { value: 5, text: '5' },
                { value: 10, text: '10' },
                { value: 25, text: '25' },
                { value: 50, text: '50' },
                { value: -1, text: t('pagination.all') },
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
                <v-list-item-title @click="edit(item)" class="cursor-pointer d-flex gap-3 pb-3">
                  <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                  {{ t('Edit') }}
                </v-list-item-title>
                <v-list-item-title @click="deleteItem(item)" class="cursor-pointer d-flex gap-3">
                  <v-icon color="error">mdi-delete-outline</v-icon>
                  {{ t('Delete') }}
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </v-data-table-server>
    </div>
  </template>
  
  <script setup>
  import { computed } from "vue";
  import AppBar from "@/components/AppBar.vue";
  import CreateStudentStatistic from "./CreateStudentStatistic.vue";
  import { useStudentStatisticRepository } from "../../store/StudentStatisticRepository"
  const StudentStatisticRepository = useStudentStatisticRepository();
  import { useI18n } from "vue-i18n";
  const { t, locale } = useI18n();
  
  const dir = computed(() => {
    return locale.value === "fa" ? "rtl" : "ltr";
  });
  
  const showCreateDialog = () => {
    StudentStatisticRepository.statistic = {};
    StudentStatisticRepository.isEditMode = false;
    StudentStatisticRepository.createDialog = true;
  };
  
  const edit = (item) => {
    StudentStatisticRepository.isEditMode = true;
    StudentStatisticRepository.statistic = {};
    StudentStatisticRepository.fetchStatistic(item.id)
      .then(() => {
        StudentStatisticRepository.createDialog = true;
      })
      .catch((error) => {
        console.error("Error fetching statistic:", error);
      });
  };
  
  const deleteItem = async (item) => {
    await StudentStatisticRepository.deleteStatistic(item.id);
  };
  
  const headers = computed(() => [
    { title: t("Academic Year"), key: "academic_year", align: "start", sortable: false },
    { title: t("University"), key: "university.name" },
    { title: t("Faculty"), key: "faculty.name" },
    { title: t("Department"), key: "department.name" },
    { title: t("Classroom"), key: "classroom" },
    { title: t("Shift"), key: "shift" },
    { title: t("Season"), key: "season" },
    { title: t("Semester"), key: "semester_number" },
    { title: t("Male"), key: "male_total" },
    { title: t("Female"), key: "female_total" },
    { title: t("Type"), key: "student_type" },
    { title: t("Action"), key: "action", align: "end", sortable: false },
  ]);
  </script>
  
  <style scoped></style>