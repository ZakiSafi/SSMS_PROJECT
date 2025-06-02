<template>
    <div :dir="dir">
    <AppBar :pageTitle="$t('University Classes')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

    <div class="w-[24rem] pt-6 pb-6 d-flex align-center">
        <v-combobox
            class="mx-4"
            v-model="ReportRepository.date"
            :items="yearRange"
            :label="$t('Select or Type Year')"
            variant="outlined"
            density="compact"
            hide-details
            @update:modelValue="onDateChange"
        ></v-combobox>

        <v-select
            v-model="ReportRepository.shift"
            :items="[$t('day'), $t('night')]"
            :label="$t('Select Shift')"
            variant="outlined"
            hide-details
            density="compact"
            @update:modelValue="onDateChange"
        ></v-select>
    </div>

    <div class="table-container">
        <table class="gender-stats-table">
            <thead>
                <tr>
                    <th rowspan="2">{{ $t('University') }}</th>
                    <th colspan="3">{{ $t('Class') }} 1</th>
                    <th colspan="3">{{ $t('Class') }} 2</th>
                    <th colspan="3">{{ $t('Class') }} 3</th>
                    <th colspan="3">{{ $t('Class') }} 4</th>
                    <th colspan="3">{{ $t('Class') }} 5</th>
                    <th colspan="3">{{ $t('Class') }} 6</th>
                </tr>
                <tr>
                    <!-- Repeat for each class -->
                    <template v-for="n in 6" :key="n">
                        <th>{{ $t('Male') }}</th>
                        <th>{{ $t('Female') }}</th>
                        <th>{{ $t('Total') }}</th>
                    </template>
                </tr>
            </thead>

            <!-- Progress bar under thead -->
            <tr v-if="ReportRepository.loading" class="loading-row">
                <td colspan="19">
                    <v-progress-linear
                        indeterminate
                        color="primary"
                        height="4"
                        class="ma-0"
                    ></v-progress-linear>
                </td>
            </tr>

          <tbody v-if="ReportRepository.universityClasses.length">

  <tr
    v-for="(institution, index) in ReportRepository.universityClasses"
    :key="index"
  >
    <td>{{ institution.university_name }}</td>

    <template v-for="classIndex in 6" :key="classIndex">
      <td class="male">
        {{
          institution.classes["Class " + classIndex]?.Total_males || 0
        }}
      </td>
      <td class="female">
        {{
          institution.classes["Class " + classIndex]?.Total_Females || 0
        }}
      </td>
      <td class="total">
        {{
          institution.classes["Class " + classIndex]?.Total_Students || 0
        }}
      </td>
    </template>
  </tr>
</tbody>

<!-- Show message if there is no data -->
<tbody v-else>
  <tr>
    <td colspan="19" class="text-center py-4">
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
    ReportRepository.fetchUniversityClasses(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date
    );
};

onMounted(() => {
    ReportRepository.fetchUniversityClasses(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.shift
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
    border-bottom: 1px solid #ddd;
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
