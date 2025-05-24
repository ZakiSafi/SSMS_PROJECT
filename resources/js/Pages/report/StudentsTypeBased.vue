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
    <!-- Heading always visible -->
    <div class="main-heading">
      Statistical Report of Graduates - Semester 2, {{ ReportRepository.date }} Educational Institutions
    </div>

    <!-- Table always rendered -->
    <table class="styled-table">
      <thead>
        <tr>
          <th>No.</th>
          <th>Higher Education Institution</th>
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>
          <th>Percentage Of Male</th>
          <th>Percentage Of Female</th>
          <th>Considerations</th>
        </tr>
      </thead>
      <tbody>
        <!-- If data is available, show rows -->
        <tr v-if="departments.length" v-for="(item, index) in departments" :key="index">
          <td>{{ index + 1 }}</td>
          <td>{{ item.university || 'University Name' }}</td>
          <td>{{ item.Total_Males }}</td>
          <td>{{ item.Total_Females }}</td>
          <td>{{ item.Total_Students }}</td>
          <td>{{ ((item.Total_Males / item.Total_Students) * 100).toFixed(2) }}%</td>
          <td>{{ ((item.Total_Females / item.Total_Students) * 100).toFixed(2) }}%</td>
          <td></td>
        </tr>

        <!-- If no data, show "no data" message inside table -->
        <tr v-else>
          <td colspan="8" style="text-align: center; color: #888; padding: 20px;">
            🚫 No data found for this year.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>


<script setup>
import { onMounted, computed } from "vue";
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import {LocaleConfigs} from "../../LocaleConfigs";

const ReportRepository = useReportRepository();

onMounted(() => {
  ReportRepository.fetchJawad();
});

const departments = computed(() => ReportRepository.departments);

const onDateChange = (date) => {
    console.log('Date range changed:', date);
    ReportRepository.date=date;
    
    ReportRepository.fetchJawad(date);

  
};

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