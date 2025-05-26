<template>
    <AppBar pageTitle="University Base Graduation Report" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

   <div class="w-[24rem] pt-6 pb-6  d-flex align-center">
  <v-combobox
    class="mr-4"
    v-model="ReportRepository.date"
    :items="yearRange"
    label="Select or Type Year"
    variant="outlined"
    density="compact"
    hide-details
    @update:modelValue="onDateChange"
  ></v-combobox>
  
  <v-select 
    v-model="ReportRepository.season"
    :items="['winter','spring']"
    label="Select Session"
    variant="outlined"
    hide-details
    density="compact"
    @update:modelValue="onDateChange"
  ></v-select>
</div>
    
    <v-data-table-server
        v-model:items-per-page="ReportRepository.itemsPerPage"
        :headers="headers"
        :items-length="ReportRepository.totalItems"
        :items="ReportRepository.universityBaseGraduation"
        :loading="ReportRepository.loading"
        :search="ReportRepository.search"
        @update:options="
            (options) =>
                ReportRepository.fetchUniversityBaseGraduation(options, ReportRepository.date, ReportRepository.season)
        "
        class="w-100 mx-auto"
        hover
    ></v-data-table-server>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
const ReportRepository = useReportRepository();
import persianDate from "persian-date";

const getCurrentPersianYear = () => {
  return new persianDate().year(); 
}

const currentYear = ref(getCurrentPersianYear());
const yearRange = computed(() => {
  const years = [];
  const startYear = currentYear.value - 10; 
  const endYear = currentYear.value + 10;
  
  for (let i = startYear; i <= endYear; i++) {
    years.push(i); 
  }
  return years;
})

const onDateChange = () => {
    ReportRepository.fetchUniversityBaseGraduation(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.session
    );
};

const headers = [
    { title: "University", key: "university", align: "start", sortable: false },
    { title: "Total Males", key: "Total_Males", align: "center" },
    { title: "Total Females", key: "Total_Females", align: "center" },
    { title: "Total Students", key: "Total_Students", align: "center" },
    { title: "Male Percentage", key: "Male_Percentage", align: "center" },
    { title: "Female Percentage", key: "Female_Percentage", align: "center" },
];
</script>

<style scoped>

</style>