<template>
  <div :dir="dir">
    <AppBar :pageTitle="$t('Faculty Base Graduation')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

    <!-- Filter & Print Section -->
    <div
      class="w-full d-flex flex-wrap align-center  pt-6 pb-6"
    >
      <!-- Year -->
      <div class="w-[200px] mx-4">
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

      <!-- Season -->
      <div class="w-[200px] mx-4">
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
          density="compact"
          hide-details
          @update:modelValue="onDateChange"
        />
      </div>

      <!-- Shift -->
      <div class="w-[200px] mx-4">
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
          density="compact"
          hide-details
          @update:modelValue="onDateChange"
        />
      </div>

      <!-- Print -->
      <div class="shrink-0 mx-4">
        <v-btn color="primary" @click="printTable">
          {{ $t('print_report') }}
        </v-btn>
      </div>
    </div>

    <!-- Table Section -->
    <div class="table-container">
      <table class="gender-stats-table">
        <thead>
          <tr>
            <th>{{ $t("University") }}</th>
            <th>{{ $t("Faculty") }}</th>
            <th>{{ $t("Male") }}</th>
            <th>{{ $t("Female") }}</th>
            <th>{{ $t("Total") }}</th>
          </tr>
        </thead>

        <tr v-if="ReportRepository.loading" class="loading-row">
          <td colspan="5">
            <v-progress-linear
              :reverse="dir === 'rtl'"
              indeterminate
              color="primary"
              height="4"
              class="ma-0"
            />
          </td>
        </tr>

        <tbody v-if="ReportRepository.facultyBaseGraduation.length">
          <template
            v-for="(institution, index) in ReportRepository.facultyBaseGraduation"
            :key="'uni-' + index"
          >
            <template
              v-for="(faculty, fIndex) in institution.faculties"
              :key="'fac-' + fIndex"
            >
              <tr>
                <template v-if="fIndex === 0">
                  <td :rowspan="institution.faculties.length">
                    {{ institution.university }}
                  </td>
                </template>
                <td>{{ faculty.faculty }}</td>
                <td>{{ faculty.Total_Males || 0 }}</td>
                <td>{{ faculty.Total_Females || 0 }}</td>
                <td>{{ faculty.Total_Students || 0 }}</td>
              </tr>
            </template>
          </template>
        </tbody>

        <tbody v-else>
          <tr>
            <td colspan="5" class="text-center py-4">
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
import { ref, computed, onMounted } from "vue";
import { useReportRepository } from "../../store/ReportRepository";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));
const ReportRepository = useReportRepository();

const currentYear = ref(new persianDate().year());
const yearRange = computed(() => {
  const range = [];
  for (let i = currentYear.value - 10; i <= currentYear.value + 10; i++) {
    range.push(i);
  }
  return range;
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
    ReportRepository.shift
  );
});

const printTable = () => {
  const data = ReportRepository.facultyBaseGraduation;
   console.log('DATA FOR PRINTING:', data);
  if (!data.length) return;

  const rows = data
    .map((institution) => {
      return institution.faculties
        .map((faculty, index) => {
          const universityCell =
            index === 0
              ? `<td rowspan="${institution.faculties.length}">${institution.university}</td>`
              : "";
          return `<tr>
            ${universityCell}
            <td>${faculty.faculty}</td>
            <td>${faculty.Total_Males || 0}</td>
            <td>${faculty.Total_Females || 0}</td>
            <td>${faculty.Total_Students || 0}</td>
          </tr>`;
        })
        .join("");
    })
    .join("");

  const html = `
    <html dir="${dir.value}">
      <head>
        <title></title>
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 20px;
            direction: ${dir.value};
          }
          .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 16px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
          }
          th, td {
            border: 1px solid #ccc;
            padding: 8px;
          }
          th {
            background-color: #e7f2f5;
          }
        </style>
      </head>
      <body>
        <div class="title">${t("University Classes")}</div>
        <table>
          <thead>
            <tr>
              <th>${t("University")}</th>
              <th>${t("Faculty")}</th>
              <th>${t("Male")}</th>
              <th>${t("Female")}</th>
              <th>${t("Total")}</th>
            </tr>
          </thead>
          <tbody>${rows}</tbody>
        </table>
      </body>
    </html>`;

  const printWindow = window.open("", "PRINT");
  printWindow.document.write(html);
  printWindow.document.close();
  printWindow.focus();
  printWindow.print();
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
  background-color: #e7f2f5;
  padding: 8px;
  border: 1px solid #ddd;
}

.gender-stats-table td {
  padding: 10px 8px;
  border: 1px solid #ddd;
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
