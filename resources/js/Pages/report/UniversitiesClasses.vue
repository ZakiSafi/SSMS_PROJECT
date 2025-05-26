<template>
  <AppBar pageTitle="University Classes" />
  <v-divider :thickness="1" class="border-opacity-100"></v-divider>

  <div class="w-25 pt-6">
    <v-combobox
      v-model="selectedYear"
      :items="availableYears"
      label="Select Year"
      variant="outlined"
      density="compact"
    ></v-combobox>
  </div>

  <div class="table-container">
    <table class="gender-stats-table">
      <thead>
        <tr>
          <th rowspan="2">Educational Institution</th>
          <th colspan="3">POS</th>
          <th colspan="3">Similar 1</th>
          <th colspan="3">Similar 4</th>
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
        </tr>
      </thead>
      <tbody>
        <tr v-for="(institution, index) in institutions" :key="index">
          <td>{{ institution.name }}</td>
          
          <!-- POS Values -->
          <td class="male">{{ formatNumber(institution.male.pos) }}</td>
          <td class="female">{{ formatNumber(institution.female.pos) }}</td>
          <td class="total">{{ formatNumber(institution.total.pos) }}</td>
          
          <!-- Similar 1 Values -->
          <td class="male">{{ formatNumber(institution.male.similar1) }}</td>
          <td class="female">{{ formatNumber(institution.female.similar1) }}</td>
          <td class="total">{{ formatNumber(institution.total.similar1) }}</td>
          
          <!-- Similar 4 Values -->
          <td class="male">{{ formatNumber(institution.male.similar4) }}</td>
          <td class="female">{{ formatNumber(institution.female.similar4) }}</td>
          <td class="total">{{ formatNumber(institution.total.similar4) }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref } from 'vue';

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
  padding:8px;
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