<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('StudentTeacherRatio')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

        <v-row class="pt-6 align-center">
            <!-- Year Combobox (left side) -->
            <v-col cols="3">
                <v-combobox
                    v-model="ReportRepository.date"
                    :items="yearRange"
                    :label="$t('Select or Type Year')"
                    variant="outlined"
                    density="compact"
                    @update:modelValue="onDateChange"
                ></v-combobox>
            </v-col>

            <!-- Spacer pushes the button to the far right
            <v-spacer></v-spacer> -->

            <!-- Print Button (right side) -->
            <v-col cols="auto" class="mb-6">
                <v-btn color="primary" class="ml-4" @click="printTable">
                    {{ $t("print_report") }}
                </v-btn>
            </v-col>
        </v-row>

        <v-data-table-server
            v-model:items-per-page="ReportRepository.itemsPerPage"
            :headers="headers"
            :items-length="ReportRepository.totalItems"
            :items="ReportRepository.studentTeacher"
            :loading="ReportRepository.loading"
            :search="ReportRepository.search"
            @update:options="
                (options) =>
                    ReportRepository.fetchStudentTeacherRatio(
                        options,
                        ReportRepository.date
                    )
            "
            class="w-100 mx-auto"
            hover
        >
            <template #bottom>
                <div class="d-flex align-center justify-end pa-2">
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="ReportRepository.itemsPerPage"
                        :items="[
                            { value: 5, text: '5' },
                            { value: 10, text: '10' },
                            { value: 25, text: '25' },
                            { value: 50, text: '50' },
                            { value: -1, text: $t('pagination.all') },
                        ]"
                        item-title="text"
                        item-value="value"
                        density="compact"
                        variant="outlined"
                        hide-details
                        style="max-width: 100px"
                    ></v-select>
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
const ReportRepository = useReportRepository();
import persianDate from "persian-date";
import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();
import * as XLSX from "xlsx";
import { saveAs } from "file-saver";
const dir = computed(() => {
    return locale.value === "en" ? "ltr" : "rtl"; // Correctly set "rtl" and "ltr"
});

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
    ReportRepository.fetchStudentTeacherRatio(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date
    );
};

const headers = computed(() => [
    {
        title: t("University"),
        key: "university",
        align: "start",
        sortable: false,
    },
    { title: t("Teacher"), key: "teachers", align: "start" },
    { title: t("Total Student"), key: "total_students", align: "center" },
    {
        title: t("ST Ratio"),
        key: "students_per_teacher_ratio",
        align: "center",
    },
]);
const printTable = () => {
    const printContent = ReportRepository.studentTeacher
        .map(
            (item, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${item.university}</td>
      <td>${item.teachers}</td>
      <td>${item.total_students}</td>
      <td>${item.students_per_teacher_ratio}</td>
    </tr>
  `
        )
        .join("");

    const html = `
    <html dir="${dir.value}">
      <head>
        <title>${t("StudentTeacherRatio")}</title>
        <style>
          body {
            font-family: Arial, sans-serif;
            padding: 20px;
          }
          h2 {
            text-align: center;
            margin-bottom: 20px;
          }
          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }
          th, td {
            border: 1px solid #444;
            padding: 8px;
            text-align: center;
          }
          th {
            background-color: #f2f2f2;
          }
        </style>
      </head>
      <body>
        <h2>${t("StudentTeacherRatio")} - ${ReportRepository.date}</h2>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>${t("University")}</th>
              <th>${t("Teacher")}</th>
              <th>${t("Total Student")}</th>
              <th>${t("ST Ratio")}</th>
            </tr>
          </thead>
          <tbody>
            ${printContent}
          </tbody>
        </table>
      </body>
    </html>
  `;

    const printWindow = window.open("", "_blank");
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
};
</script>

<style scoped></style>
