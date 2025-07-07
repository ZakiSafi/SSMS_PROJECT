<template>
  <div :dir="dir">
    <AppBar :pageTitle="$t('DBGR')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

    <div class="w-full flex items-center justify-between pt-6 pb-6">
  <!-- Left side: Year Picker -->
  <div class="w-1/5">
    <v-col cols="6">
      <DatePicker
        v-model="selectedYear"
        format="jYYYY"
        type="year"
        :placeholder="$t('Select or Type Year')"
        rounded
        :rules="[rules.required]"
      />
    </v-col>
  </div>

  <!-- Center: Season and Shift selects -->
  <div class="w-2/5 flex">
    <div class="flex-1 mx-2">
      <v-select
        v-model="ReportRepository.season"
        :items="[
          { title: $t('spring'), value: 'spring' },
          { title: $t('autumn'), value: 'autumn' },
        ]"
        item-title="title"
        item-value="value"
        :label="$t('select_season')"
        variant="outlined"
        hide-details
        density="compact"
      />
    </div>

    <div class="flex-1 mx-2">
      <v-combobox
        v-model="ReportRepository.shift"
        :items="[
          { title: $t('day'), value: 'day' },
          { title: $t('night'), value: 'night' },
        ]"
        item-title="title"
        item-value="value"
        :label="$t('shift')"
        variant="outlined"
        hide-details
        density="compact"
      />
    </div>
    <div class="w-auto">
    <v-btn color="primary" class="ml-4" @click="printTable">
      {{ $t("print_report") }}
    </v-btn>
  </div>
  </div>
  
</div>


    <!-- Table -->
    <div class="table-container">
      <table class="gender-stats-table">
        <thead>
          <tr>
            <th rowspan="2">{{ $t("University") }}</th>
            <th rowspan="2">{{ $t("Faculty") }}</th>
            <th rowspan="2">{{ $t("Department") }}</th>
            <th>{{ $t("Male") }}</th>
            <th>{{ $t("Female") }}</th>
            <th>{{ $t("Total") }}</th>
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
          <template
            v-for="(institution, index) in ReportRepository.departmentBaseGraduation"
            :key="'uni-' + index"
          >
            <template
              v-for="(faculty, fIndex) in institution.faculties"
              :key="'fac-' + fIndex"
            >
              <template
                v-for="(department, dIndex) in faculty.departments"
                :key="'dep-' + dIndex"
              >
                <tr>
                  <template v-if="fIndex === 0 && dIndex === 0">
                    <td
                      :rowspan="
                        institution.faculties.reduce(
                          (acc, fac) => acc + fac.departments.length,
                          0
                        )
                      "
                    >
                      {{ institution.university }}
                    </td>
                  </template>
                  <template v-if="dIndex === 0">
                    <td :rowspan="faculty.departments.length">
                      {{ faculty.faculty }}
                    </td>
                  </template>
                  <td>{{ department.department }}</td>

                  <td>{{ department?.Total_Males || 0 }}</td>
                  <td>{{ department?.Total_Females || 0 }}</td>
                  <td>{{ department?.Total_Students || 0 }}</td>
                </tr>
              </template>
            </template>
          </template>
        </tbody>

        <!-- No Data -->
        <tbody v-else>
          <tr>
            <td colspan="21" class="text-center py-4">
              {{ $t("No data available") }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref, computed, watch, onMounted } from "vue";
import { useReportRepository } from "../../store/ReportRepository";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";
import DatePicker from "vue3-persian-datetime-picker";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

const ReportRepository = useReportRepository();

// Initialize selectedYear with current Persian year or repository date if set
const selectedYear = ref(ReportRepository.date || new persianDate().year());

const rules = {
  required: (v) => !!v || t("validation.required"),
  positiveNumber: (v) => v >= 0 || t("validation.positive_number"),
};

const fetchData = () => {
  console.log(
    "Fetching data for:",
    ReportRepository.date,
    ReportRepository.season,
    ReportRepository.shift
  );
  ReportRepository.fetchDepartmentBaseGraduation(
    { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
    ReportRepository.date,
    ReportRepository.season,
    ReportRepository.shift
  );
};

// Watch for changes on selectedYear, update store and fetch
watch(selectedYear, (newYear) => {
  console.log("Year changed to:", newYear);
  ReportRepository.date = newYear;
  fetchData();
});

// Watch season and shift in store and fetch when they change
watch(
  () => ReportRepository.season,
  (newSeason) => {
    console.log("Season changed to:", newSeason);
    fetchData();
  }
);
watch(
  () => ReportRepository.shift,
  (newShift) => {
    console.log("Shift changed to:", newShift);
    fetchData();
  }
);

const printTable = () => {
  const content = document.querySelector(".gender-stats-table");
  if (!content) return;

  const tableHtml = content.outerHTML;
  const head = `
    <style>
      body {
        font-family: Arial, sans-serif;
        padding: 20px;
        direction: ${dir.value};
      }
      table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
        margin-top: 20px;
      }
      th, td {
        border: 1px solid #ddd;
        padding: 10px 8px;
      }
      th {
        background-color: #e7f2f5;
      }
      .title {
        text-align: center;
        font-size: 20px;
        margin-bottom: 10px;
        font-weight: bold;
      }
    </style>
  `;

  const printWindow = window.open("", "_blank");
  printWindow.document.write(`
    <html>
      <head>
        <title>${t("DBGR")}</title>
        ${head}
      </head>
      <body>
        <div class="title">${t("DBGR")} - ${ReportRepository.date} (${t(ReportRepository.season)}, ${t(ReportRepository.shift)})</div>
        ${tableHtml}
      </body>
    </html>
  `);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
};


onMounted(() => {
  // Initialize store date if not set
  if (!ReportRepository.date) {
    ReportRepository.date = selectedYear.value;
  }
  fetchData();
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
