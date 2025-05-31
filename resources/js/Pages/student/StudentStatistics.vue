<template>
    <CreateStudentStatistic v-if="StudentStatisticRepository.createDialog" />

    <div>
      <AppBar pageTitle="Student Statistics" />

      <v-divider :thickness="1" class="border-opacity-100" />

      <!-- Search & Create Button -->
      <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field
            color="primary"
            density="compact"
            variant="outlined"
            label="Search"
            append-inner-icon="mdi-magnify"
            hide-details
            v-model="StudentStatisticRepository.departmentSearch"
          />
        </div>

        <v-btn @click="showCreateDialog" color="primary" variant="flat" class="px-6">
          Create
        </v-btn>
      </div>

      <!-- Data Table -->
      <v-data-table-server
      v-model:items-per-page="StudentStatisticRepository.itemsPerPage"
      :headers="headers"
      :items-length="StudentStatisticRepository.totalItems"
      :items="StudentStatisticRepository.statistics"
      :loading="StudentStatisticRepository.loading"
      :search="StudentStatisticRepository.departmentSearch"
      @update:options="StudentStatisticRepository.fetchStatistics"
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
  import CreateStudentStatistic from "./CreateStudentStatistic.vue";
  import { useStudentStatisticRepository } from "../../store/StudentStatisticRepository"

  const StudentStatisticRepository = useStudentStatisticRepository();

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

  const headers = [
    { title: "Academic Year", key: "academic_year", align: "start", sortable: false },
    { title: "University", key: "university.name" },
    { title: "Faculty", key: "faculty.name" },
    { title: "Department", key: "department.name" },
    { title: "Classroom", key: "classroom" },
    { title: "Shift", key: "shift" },
    { title: "Season", key: "season" },
    { title: "Semester", key: "semester_number" },
    { title: "Male", key: "male_total" },
    { title: "Female", key: "female_total" },
    { title: "Type", key: "student_type" },
    { title: "Action", key: "action", align: "end", sortable: false },
  ];
  </script>

  <style scoped></style>
