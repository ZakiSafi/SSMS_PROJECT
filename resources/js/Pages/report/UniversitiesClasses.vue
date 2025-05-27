<template>
  <AppBar pageTitle="University Classes" />
  <v-divider :thickness="1" class="border-opacity-100"></v-divider>

  <div class="w-25 pt-6">
    <v-combobox v-model="ReportRepository.date" :items="yearRange" label="Select Year" variant="outlined"
      density="compact" @update:modelValue="onDateChange"></v-combobox>
  </div>

  <div class="table-container">
    <table class="gender-stats-table">
      <thead>
        <tr>
          <th rowspan="2">University</th>
          <th colspan="3">Class 1</th>
          <th colspan="3">Class 2</th>
          <th colspan="3">Class 3</th>
          <th colspan="3">Class 4</th>
          <th colspan="3">Class 5</th>
          <th colspan="3">Class 6</th>
          <!-- Add other metrics as needed -->
        </tr>
        <tr>
          <!-- POS subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>

          <!-- Similar 1 subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>

          <!-- Similar 4 subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>

          <!-- Similar 5 subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>
          <!-- Similar 6 subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>
          <!-- Similar 7 subheaders -->
          <th>Male</th>
          <th>Female</th>
          <th>Total</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(institution, index) in ReportRepository.universityClasses" :key="index">
          <td>{{ institution.university_name }}</td>

          <template v-for="classIndex in 6">
            <td class="male">
              {{ institution.classes['Class ' + classIndex]?.Total_males || '' }}
            </td>
            <td class="female">
              {{ institution.classes['Class ' + classIndex]?.Total_Females || '' }}
            </td>
            <td class="total">
              {{ institution.classes['Class ' + classIndex]?.Total_Students || '' }}
            </td>
          </template>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref, computed, onMounted } from 'vue';
import { useReportRepository } from "../../store/ReportRepository";

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
  ReportRepository.fetchUniversityClasses(
    { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
    ReportRepository.date,
  );
};

onMounted(() => {
  ReportRepository.fetchUniversityClasses({ page: 1, itemsPerPage: ReportRepository.itemsPerPage },
    ReportRepository.date, ReportRepository.shift);
});

const selectedYear = ref(1403);
const availableYears = [1403, 1402, 1401];

const institutions = ref([
  {
    name: 'Group 3',
    male: { pos: 1010, similar1: 1021, similar4: 1022 },
    female: { pos: 1010, similar1: 1000, similar4: 1005 },
    total: { pos: 2020, similar1: 2021, similar4: 2027 }
  },
  // Add more institutions as needed
]);

const formatNumber = (num) => {
  return num.toLocaleString();
};

ReportRepository.fetchUniversityClasses();
</script>

<style scoped>
.table-container {
  overflow-x: auto;
}

.gender-stats-table {
  width: 100%;
  border-collapse: collapse;
  text-align: center;
}

.gender-stats-table th {
  background-color: #E7F2F5;
  padding: 8px;
  border: 1px solid #ddd;
}

.gender-stats-table td {
  padding: 10px 8px;
  border: 1px solid #ddd;
}

.male {
  color: #1976D2;
  font-weight: 500;
}

.female {
  color: #E91E63;
  font-weight: 500;
}

.total {
  font-weight: bold;
  background-color: #f9f9f9;
}

.gender-stats-table tr:nth-child(even) {
  background-color: #f8f8f8;
}
</style>