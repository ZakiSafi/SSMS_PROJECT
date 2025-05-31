<template>
  <div  :dir="dir">
    <AppBar pageTitle="StudentTeacherRatio" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

   <div class="w-25 pt-6 ">
  <v-combobox
    class="mr-4"
    v-model="ReportRepository.date"
    :items="yearRange"
    label="Select or Type Year"
    variant="outlined"
    density="compact"
    @update:modelValue="onDateChange"
  ></v-combobox>
</div>
    
    <v-data-table-server
        v-model:items-per-page="ReportRepository.itemsPerPage"
        :headers="headers"
        :items-length="ReportRepository.totalItems"
        :items="ReportRepository.studentTeacher"
        :loading="ReportRepository.loading"
        :search="ReportRepository.search"
        @update:options="
            (options) =>
                ReportRepository.fetchStudentTeacherRatio(options, ReportRepository.date)
        "
        class="w-100 mx-auto"
        hover
    ></v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
const ReportRepository = useReportRepository();
import persianDate from "persian-date";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});


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
    ReportRepository.fetchStudentTeacherRatio(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
    );
};

const headers = [
    { title: "University", key: "university", align: "start", sortable: false },
    { title: "Teacher", key: "teachers", align: "start" },
    { title: "Total Student", key: "Total_Students", align: "center" },
    { title: "ST Ratio ", key: "Students_Per_Teacher_Ratio", align: "center" },
    
];
</script>

<style scoped>

</style>