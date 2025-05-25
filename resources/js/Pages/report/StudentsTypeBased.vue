<template>
  <AppBar :pageTitle="`Statistical Report of Graduates - Semester 2, ${ReportRepository.date} Educational Institutions`" />
  <v-divider :thickness="1" class="border-opacity-100" />

  <div class="w-25 pt-6 pb-6">
    <date-picker
      mode="single"
      :column="1"
      v-model="ReportRepository.search"
      :styles="styles"
      @update:modelValue="onDateChange"
      locale="fa"
      type="date"
      :locale-config="LocaleConfigs"
      input-format="jYYYY"
      format="YYYY"
    />
  </div>

 
    <v-data-table-server v-model:items-per-page="ReportRepository.itemsPerPage" :headers="headers"
        :items-length="ReportRepository.totalItems" :items="ReportRepository.departments"
        :loading="ReportRepository.loading" :search="ReportRepository.search"
        @update:options="(options) => ReportRepository.fetchJawad(options, ReportRepository.date)"
 class="w-100 mx-auto" hover></v-data-table-server>
</template>



<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import {LocaleConfigs} from "../../LocaleConfigs";

const ReportRepository = useReportRepository();




const onDateChange = (date) => {
  ReportRepository.date = date;
  ReportRepository.fetchJawad({ page: 1, itemsPerPage: ReportRepository.itemsPerPage }, date);

}


const headers = [
  { title: "University", key: "university", align: "start", sortable: false },
  { title: "Total Males", key: "Total_Males",align: "center" },
  { title: "Total Females", key: "Total_Females",align: "center" },
  { title: "Total Students", key: "Total_Students",align: "center" },
  { title: "Total Students", key: "Total_Males/Total_Students" * 100,align: "center" },
];


</script>

<style scoped>
.v-data-table {
  --primary-color: none;
  --row-spacing: 10px;
}



</style>