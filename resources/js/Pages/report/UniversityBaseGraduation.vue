<template>
  <div :dir="dir">
    <AppBar :pageTitle="$t('university_base_graduation')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

    <!-- Filters and Print -->
    <div class="w-full d-flex flex-wrap align-center pt-6 pb-6">
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
            { text: $t('spring'), value: 'spring' },
            { text: $t('autumn'), value: 'autumn' },
          ]"
          item-title="text"
          item-value="value"
          :label="$t('Select season')"
          variant="outlined"
          hide-details
          density="compact"
          @update:modelValue="onDateChange"
        />
      </div>

      <!-- Print Button -->
      <div class="shrink-0 mx-4">
        <v-btn color="primary" @click="printTable">
          {{ $t('print_report') }}
        </v-btn>
      </div>
    </div>

    <!-- Data Table -->
    <v-data-table-server
      v-model:items-per-page="ReportRepository.itemsPerPage"
      :headers="headers"
      :items-length="ReportRepository.totalItems"
      :items="ReportRepository.universityBaseGraduation"
      :loading="ReportRepository.loading"
      :search="ReportRepository.search"
      :dir="dir"
      @update:options="(options) =>
        ReportRepository.fetchUniversityBaseGraduation(
          options,
          ReportRepository.date,
          ReportRepository.season
        )
      "
      class="w-100 mx-auto"
      hover
    />
  </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

const ReportRepository = useReportRepository();

const getCurrentPersianYear = () => new persianDate().year();

const currentYear = ref(getCurrentPersianYear());
const yearRange = computed(() => {
  const years = [];
  const start = currentYear.value - 10;
  const end = currentYear.value + 10;
  for (let i = start; i <= end; i++) years.push(i);
  return years;
});

const onDateChange = () => {
  ReportRepository.fetchUniversityBaseGraduation(
    { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
    ReportRepository.date,
    ReportRepository.season
  );
};

const headers = computed(() => [
  { title: t("menu.university"), key: "university", align: "start", sortable: false },
  { title: t("total_males"), key: "Total_Males", align: "center" },
  { title: t("total_females"), key: "Total_Females", align: "center" },
  { title: t("total_students"), key: "Total_Students", align: "center" },
  { title: t("male_percentage"), key: "Male_Percentage", align: "center" },
  { title: t("female_percentage"), key: "Female_Percentage", align: "center" },
]);

const printTable = () => {
  const data = ReportRepository.universityBaseGraduation;
  if (!data.length) return;

  const rows = data
    .map((item) => {
      return `<tr>
        <td>${item.university}</td>
        <td>${item.Total_Males || 0}</td>
        <td>${item.Total_Females || 0}</td>
        <td>${item.Total_Students || 0}</td>
        <td>${item.Male_Percentage || 0}%</td>
        <td>${item.Female_Percentage || 0}%</td>
      </tr>`;
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
        <div class="title">${t("university_base_graduation")}</div>
        <table>
          <thead>
            <tr>
              <th>${t("menu.university")}</th>
              <th>${t("total_males")}</th>
              <th>${t("total_females")}</th>
              <th>${t("total_students")}</th>
              <th>${t("male_percentage")}</th>
              <th>${t("female_percentage")}</th>
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
/* All styling is done via classes above */
</style>
