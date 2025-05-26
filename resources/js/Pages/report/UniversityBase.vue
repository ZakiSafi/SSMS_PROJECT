<template>
    <AppBar
        :pageTitle="`Statistical Data of Students in Public Higher Education Institutions – Day and Night Shifts, Year ${ReportRepository.date}`"
    />
    <v-divider :thickness="1" class="border-opacity-100" />

    <div class="w-25 pt-6 pb-6">
         <v-combobox
      v-model="ReportRepository.date"
      :items="yearRange"
      label="Select or Type Year"
      variant="outlined"
      density="compact"
      @update:modelValue="onDateChange"
      :rules="[validateYearInput]"
    ></v-combobox>
    </div>

    <v-data-table-server
        v-model:items-per-page="ReportRepository.itemsPerPage"
        :headers="headers"
        :items-length="ReportRepository.totalItems"
        :items="ReportRepository.universities"
        :loading="ReportRepository.loading"
        :search="ReportRepository.search"
        @update:options="
            (options) =>
                ReportRepository.fetchUniversity(options, ReportRepository.date)
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
import  persianDate  from "persian-date";


const getCurrentPersianYear = () => {
  return new persianDate().year().toString(); 
}

const currentYear = ref(getCurrentPersianYear());

const yearRange = computed(() => {
  const years = [];
  const startYear = parseInt(currentYear.value) - 10; 
  const endYear = parseInt(currentYear.value) + 10;
  
  for (let i = startYear; i <= endYear; i++) {
    years.push(i.toString()); 
  }
  return years;
})

const onDateChange = (date) => {
    ReportRepository.date = date;
    ReportRepository.fetchUniversity(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        date
    );
};

const validateYearInput = (value) => {
  if (!value) return "Year is required";
  const yearNum = parseInt(value);
  if (isNaN(yearNum)) return "Must be a valid year";
  const minYear = parseInt(currentYear.value) - 10;
  const maxYear = parseInt(currentYear.value) + 10;
  if (yearNum < minYear || yearNum > maxYear) {
    return `Year must be between ${minYear} and ${maxYear}`;
  }
  return true;
};

const headers = [
    { title: "University", key: "university", align: "start", sortable: false },
    { title: "Total Males", key: "Total_Males", align: "center" },
    { title: "Total Females", key: "Total_Females", align: "center" },
    { title: "Total Students", key: "Total_Students", align: "center" },
    { title: "Male Percantage", key: "Male_Percentage", align: "center" },
    { title: "Female Percantage", key: "Female_Percentage", align: "center" },
    
];
</script>

<style scoped>
.v-data-table {
    --primary-color: none;
    --row-spacing: 10px;
}
</style>
