<template>
  <AppBar pageTitle="Report" />
  <v-divider :thickness="1" class="border-opacity-100" />

  <div class="w-25 pt-4">
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

  <div class="table-container">
    <div class="main-heading">
      Statistical Report of Graduates - Semester 2, {{ ReportRepository.date }} Educational Institutions
    </div>
    <v-data-table-server v-model:items-per-page="ReportRepository.itemsPerPage" :headers="headers"
        :items-length="ReportRepository.totalItems" :items="ReportRepository.departments"
        :loading="ReportRepository.loading" :search="ReportRepository.search"
        @update:options="ReportRepository.fetchJawad" class="w-100 mx-auto" hover></v-data-table-server>
        </div>
</template>



<script setup>
import { onMounted, computed } from "vue";
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import {LocaleConfigs} from "../../LocaleConfigs";

const ReportRepository = useReportRepository();




const onDateChange = (date) => {
  ReportRepository.date = date;
  ReportRepository.fetchJawad({ page, itemsPerPage },date);
}


const headers = [
  { title: "University", key: "university", align: "start", sortable: false },
  { title: "Action", key: "action", align: "end", sortable: false },
];

</script>

<style scoped>



.table-container {
  overflow-x: auto;

}

.main-heading {
  font-size: 1.3rem;
  font-weight: bold;
  text-align: center;
  padding: 15px;
  background-color: #f5f5f5;
  border: 1px solid #ddd;
  border-bottom: none;
  border-radius: 5px 5px 0 0;
}

.styled-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95em;
  min-width: 400px;
  border: 1px solid #ddd;
}

.styled-table thead tr {
  background-color: #009EE2;
  color: white;
  text-align: center;
}

.styled-table th,
.styled-table td {
  padding: 12px 15px;
  border: 1px solid #ddd;
  text-align: center;
}

.styled-table tbody tr {
  border-bottom: 1px solid #ddd;
}

.styled-table tbody tr:nth-of-type(even) {
  background-color: #f9f9f9;
}

/* .styled-table tbody tr:last-of-type {
  border-bottom: 2px solid #009EE2;
} */
</style>