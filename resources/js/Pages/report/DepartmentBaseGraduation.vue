<template>
    <div :dir="dir">
      <AppBar :pageTitle="$t('DBGR')" />
      <v-divider :thickness="1" class="border-opacity-100"></v-divider>

      <div class="w-full d-flex justify-space-between pt-6 pb-6">
        <!-- Left side: Year Combobox -->
        <div class="w-1/5">
          <v-combobox
            v-model="ReportRepository.date"
            :items="yearRange"
            :label="$t('Select or Type Year')"
            variant="outlined"
            density="compact"
            hide-details
            @update:modelValue="onDateChange"
          />
        </div>

        <!-- Right side: Season and University selects -->
        <div class="w-1/2 flex">
          <div class="flex-1 mx-2">
            <v-select
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
            />
          </div>

          <div class="flex-1 mx-2">
            <v-combobox
  v-model="ReportRepository.shift"
   :items="[
    { title: $t('day'), value: 'day' },
    { title: $t('night'), value: 'shift' }
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

      <!-- Table -->
      <div class="table-container">
        <table class="gender-stats-table">
          <thead>
            <tr>
              <th rowspan="2">{{ $t('University') }}</th>
              <th rowspan="2">{{ $t('Faculty') }}</th>
              <th rowspan="2">{{ $t('Department') }}</th>
              <th>{{ $t('Male') }}</th>
                <th>{{ $t('Female') }}</th>
                <th>{{ $t('Total') }}</th>
            </tr>
          </thead>

          <!-- Loading State -->
          <tr v-if="ReportRepository.loading" class="loading-row">
            <td colspan="21">
              <v-progress-linear
                :reverse="dir === 'rtl'"
                indeterminate
                color="primary"
                height="4"
                class="ma-0"
              />
            </td>
          </tr>

          <!-- Data Rows -->
          <tbody v-if="ReportRepository.departmentBaseGraduation.length">
            <template v-for="(institution, index) in ReportRepository.departmentBaseGraduation" :key="'uni-' + index">
              <template v-for="(faculty, fIndex) in institution.faculties" :key="'fac-' + fIndex">
                <template v-for="(department, dIndex) in faculty.departments" :key="'dep-' + dIndex">
                  <tr>
                    <template v-if="fIndex === 0 && dIndex === 0">
                      <td :rowspan="institution.faculties.reduce((acc, fac) => acc + fac.departments.length, 0)">
                        {{ institution.university }}
                      </td>
                    </template>
                    <template v-if="dIndex === 0">
                      <td :rowspan="faculty.departments.length">
                        {{ faculty.faculty }}
                      </td>
                    </template>
                    <td>{{ department.department }}</td>

                    <template v-for="classIndex in 6" :key="'class-' + classIndex">
                      <td>
                        {{ department.classes?.['Class ' + classIndex]?.Total_males || 0 }}
                      </td>
                      <td>
                        {{ department.classes?.['Class ' + classIndex]?.Total_Females || 0 }}
                      </td>
                      <td>
                        {{ department.classes?.['Class ' + classIndex]?.Total_Students || 0 }}
                      </td>
                    </template>
                  </tr>
                </template>
              </template>
            </template>
          </tbody>

          <!-- No Data -->
          <tbody v-else>
            <tr>
              <td colspan="21" class="text-center py-4">
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
  import persianDate from "persian-date";

  const { t, locale } = useI18n();
  const dir = computed(() => (locale.value === "fa" ? "rtl" : "ltr"));

  const ReportRepository = useReportRepository();

  const getCurrentPersianYear = () => new persianDate().year();
  const currentYear = ref(getCurrentPersianYear());

  const yearRange = computed(() => {
    const years = [];
    for (let i = currentYear.value - 10; i <= currentYear.value + 10; i++) {
      years.push(i);
    }
    return years;
  });

  const onDateChange = () => {
    
    ReportRepository.fetchDepartmentBaseGraduation(
      { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
      ReportRepository.date,
      ReportRepository.season,
        ReportRepository.shift
    );
  };

  onMounted(() => {
    ReportRepository.fetchDepartmentBaseGraduation(
      { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
      ReportRepository.date,
      ReportRepository.season,
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



  <!-- | Logic                          | Effect
  | ------------------------------ | ---------------------------------------------------------- |
  | `fIndex === 0 && dIndex === 0` | Show university name only on very first department         |
  | `dIndex === 0`                 | Show faculty name only on first department of each faculty |
  | `rowspan`                      | Merge cells vertically for neat, non-repeating display     |
  | `v-for="classIndex in 6"`      | Dynamically loop through 6 classes (reduced to 2 here)     |
  | `?.['Class ' + classIndex]`    | Safe access to dynamic class keys like "Class 1"           |
  | \`                             |                                                            |
   -->
