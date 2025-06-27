<template>
  <div :dir="dir">
    <AppBar :pageTitle="$t('University Classes')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

<div class="w-full d-flex justify-space-between pt-6 pb-6 ">
  <!-- Left side: Combobox -->
  <div class="w-1/5">
    <v-combobox
        v-model="ReportRepository.date"
        :items="yearRange"
        :label="$t('Select or Type Year')"
        variant="outlined"
        density="compact"
        hide-details
        @update:modelValue="onDateChange"
      ></v-combobox>

  </div>

  <!-- Right side: Two selects side by side -->
  <div class="w-1/3 flex">
    <div class="w-2/3">
      <v-select
  class="mx-4"
  v-model="ReportRepository.season"
  :items="[
    { title: $t('spring'), value: 'spring' },
    { title: $t('autumn'), value: 'autumn' }
  ]"
  item-title="title"
  item-value="value"
  :label="$t('select_season')"
  variant="outlined"
  hide-details
  density="compact"
  @update:modelValue="onDateChange"
></v-select>
    </div>
    <div class="w-3/4 ml-4">

 <v-combobox
  v-model="ReportRepository.shift"
   :items="[
    { title: $t('day'), value: 'day' },
    { title: $t('night'), value: 'night' }
  ]"
  item-title="title"
  item-value="value"
  :label="$t('shift')"
  variant="outlined"
  hide-details
  density="compact"
  @update:modelValue="onDateChange"
/>

    </div>
  </div>
</div>

    <div class="table-container">
      <table class="gender-stats-table">
        <thead>
          <tr>
            <th >{{ $t('University') }}</th>
            <th>{{ $t('Faculty') }}</th>
            <th>{{ $t('Male') }}</th>
            <th>{{ $t('Female') }}</th>
            <th>{{ $t('Total') }}</th>
          </tr>
        </thead>

        <!-- Loading bar -->
        <tr v-if="ReportRepository.loading" class="loading-row">
          <td colspan="5">
            <v-progress-linear
           :reverse="dir === 'rtl'"
              indeterminate
              color="primary"
              height="4"
              class="ma-0"

            ></v-progress-linear>
          </td>
        </tr>

        <tbody v-if="ReportRepository.facultyBaseGraduation.length">
          <template
            v-for="(institution, index) in ReportRepository.facultyBaseGraduation"
            :key="'uni-'+index"
          >
            <template
              v-for="(faculty, fIndex) in institution.faculties"
              :key="'fac-'+fIndex"
            >
              <tr>
                <template v-if="fIndex === 0">
                  <td :rowspan="institution.faculties.length">
                    {{ institution.university }}
                  </td>
                </template>
                <td>{{ faculty.faculty }}</td>

                  <td>
                    {{ faculty.Total_Males || 0 }}
                  </td>
                  <td>
                    {{ faculty.Total_Females || 0 }}
                  </td>
                  <td>
                    {{ faculty.Total_Students || 0 }}
                  </td>
              </tr>
            </template>
          </template>
        </tbody>
        <tbody v-else>
          <tr>
            <td colspan="5" class="text-center py-4">
              {{ $t('No data available') }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>


<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref, computed, onMounted } from "vue";
import { useReportRepository } from "../../store/ReportRepository";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});

const ReportRepository = useReportRepository();
import persianDate from "persian-date";

const getCurrentPersianYear = () => {
    return new persianDate().year();
};

const currentYear = ref(getCurrentPersianYear());
const yearRange = computed(() => {
    const years = [];
    const startYear = currentYear.value - 10;
    const endYear = currentYear.value + 10;

    for (let i = startYear; i <= endYear; i++) {
        years.push(i);
    }
    return years;
});

const onDateChange = () => {
    ReportRepository.fetchFacultyBaseGraduation(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.season,
        ReportRepository.shift
    );
};

onMounted(() => {

    ReportRepository.fetchFacultyBaseGraduation(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.season,
        ReportRepository.shift,
    );
});
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
    background-color: #e7f2f5;
    padding: 8px;
    border: 1px solid #ddd;
}

.gender-stats-table td {
    padding: 10px 8px;
    border: 1px solid #ddd;
}

.total {
    font-size: small;
    font-weight: bold;
    color: #333;
    background-color: #f9f9f9;
}

th {
    font-size: small;
    color: #333;
}

.loading-row td {
    padding: 0 !important;
    border: none !important;
}

.v-progress-linear {
    margin: 0;
}
</style>
