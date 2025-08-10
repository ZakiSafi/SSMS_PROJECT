<template>
    <div :dir="dir">
        <AppBar
            :pageTitle="`Statistical Data of Students in ${ReportRepository.type} Higher Education Institutions – Day and Night Shifts, Year ${ReportRepository.date}`"
        />
        <v-divider :thickness="1" class="border-opacity-100" />

        <v-row class="pt-6 align-center">
            <!-- Left side: Year Combobox -->
            <v-col cols="3">
                <v-combobox
                    v-model="ReportRepository.date"
                    :items="yearRange"
                    :label="$t('Select or Type Year')"
                    variant="outlined"
                    density="compact"
                    :rules="[validateYearInput]"
                    @update:modelValue="onDateChange"
                ></v-combobox>
            </v-col>

            <!-- Center: Type and Shift selects -->
            <v-col cols="6">
                <v-row>
                    <v-col cols="6">
                        <v-select
                            v-model="ReportRepository.type"
                            :items="[
                                { text: $t('public'), value: 'public' },
                                { text: $t('private'), value: 'private' },
                            ]"
                            :label="$t('Select University Type')"
                            variant="outlined"
                            density="compact"
                            item-title="text"
                            item-value="value"
                            :rules="[validateType]"
                            @update:modelValue="onTypeChange"
                        />
                    </v-col>
                    <v-col cols="6">
                        <v-select
                            v-model="ReportRepository.shift"
                            :items="[
                                { text: $t('day'), value: 'Day' },
                                { text: $t('night'), value: 'Night' },
                            ]"
                            item-title="text"
                            item-value="value"
                            :label="$t('Select Shift')"
                            variant="outlined"
                            density="compact"
                            :rules="[validateShift]"
                            @update:modelValue="onShiftChange"
                        />
                    </v-col>
                </v-row>
            </v-col>

            <!-- Right side: Print Button -->
            <v-col cols="auto" class="mb-6">
                <v-btn color="primary" @click="printTable">
                    {{ $t("print_report") }}
                </v-btn>
            </v-col>
        </v-row>

        <v-data-table-server
            v-model:items-per-page="ReportRepository.itemsPerPage"
            :headers="headers"
            :items-length="ReportRepository.totalItems"
            :items="ReportRepository.universities"
            :loading="ReportRepository.loading"
            :search="ReportRepository.search"
            @update:options="onTableOptionsUpdate"
            class="w-100 mx-auto"
            hover
        >
            <template #bottom>
                <div class="d-flex flex-wrap align-center justify-center pa-2">
                    <!-- Pagination with forced LTR direction -->
                    <div dir="ltr" class="mr-4">
                        <v-pagination
                            v-model="ReportRepository.page"
                            :length="
                                ReportRepository.itemsPerPage === -1
                                    ? 1
                                    : Math.ceil(
                                          ReportRepository.totalItems /
                                              ReportRepository.itemsPerPage
                                      )
                            "
                            @update:modelValue="onPageChange"
                            total-visible="7"
                            color="primary"
                            v-if="
                                ReportRepository.totalItems >
                                ReportRepository.itemsPerPage
                            "
                        />
                    </div>

                    <!-- Items per page selector -->
                    <v-select
                        v-model="ReportRepository.itemsPerPage"
                        :items="[
                            { value: 5, text: '5' },
                            { value: 10, text: '10' },
                            { value: 25, text: '25' },
                            { value: 50, text: '50' },
                            { value: 100, text: '100' },
                        ]"
                        item-title="text"
                        item-value="value"
                        :label="$t('pagination.items_per_page')"
                        variant="outlined"
                        density="compact"
                        hide-details
                        style="max-width: 160px"
                        @update:modelValue="onItemsPerPageChange"
                    />
                </div>
            </template>
        </v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
import persianDate from "persian-date";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const dir = computed(() => {
    return locale.value === "en" ? "ltr" : "rtl";
});

const ReportRepository = useReportRepository();

// Initialize pagination values
ReportRepository.page = ReportRepository.page || 1;
ReportRepository.itemsPerPage = ReportRepository.itemsPerPage || 10;

const getCurrentPersianYear = () => new persianDate().year().toString();
const currentYear = ref(getCurrentPersianYear());

const yearRange = computed(() => {
    const years = [];
    const startYear = parseInt(currentYear.value) - 10;
    const endYear = parseInt(currentYear.value) + 10;
    for (let i = startYear; i <= endYear; i++) {
        years.push(i.toString());
    }
    return years;
});

const printTable = () => {
    const printContent = ReportRepository.universities
        .map(
            (item, index) => `
    <tr>
      <td>${index + 1}</td>
      <td>${item.university}</td>
      <td>${item.Total_Males}</td>
      <td>${item.Total_Females}</td>
      <td>${item.Total_Students}</td>
      <td>${item.Male_Percentage}</td>
      <td>${item.Female_Percentage}</td>
    </tr>
  `
        )
        .join("");

    const html = `
    <html dir="${dir.value}">
      <head>
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
         <h2>Statistical Data of Students in ${
             ReportRepository.type
         } Higher Education Institutions – Day and Night Shifts, Year ${
        ReportRepository.date
    }</h2>
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>${t("menu.university")}</th>
              <th>${t("total_males")}</th>
              <th>${t("total_females")}</th>
              <th>${t("total_students")}</th>
              <th>${t("male_percentage")}</th>
              <th>${t("female_percentage")}</th>
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

const onPageChange = (newPage) => {
    ReportRepository.page = newPage;
    fetchData();
};

const onItemsPerPageChange = () => {
    ReportRepository.page = 1; // Reset to first page when items per page changes
    fetchData();
};

const fetchData = () => {
    ReportRepository.fetchUniversity(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.type,
        ReportRepository.shift
    );
};

const onDateChange = (date) => {
    ReportRepository.date = date;
    ReportRepository.page = 1;
    fetchData();
};

const onTypeChange = (type) => {
    ReportRepository.type = type;
    ReportRepository.page = 1;
    fetchData();
};

const onShiftChange = (shift) => {
    ReportRepository.shift = shift;
    ReportRepository.page = 1;
    fetchData();
};

const onTableOptionsUpdate = (options) => {
    ReportRepository.page = options.page;
    ReportRepository.itemsPerPage = options.itemsPerPage;
    fetchData();
};

const validateYearInput = (value) => {
    if (!value) return "Year is required";
    const yearNum = parseInt(value);
    if (isNaN(yearNum)) return "Must be a valid year";
    const minYear = parseInt(currentYear.value) - 10;
    const maxYear = parseInt(currentYear.value) + 10;
    if (yearNum < minYear || yearNum > maxYear) {
        return `Year must be between ${minYear} and ${maxYear}`;
    }
    return true;
};

const validateType = (value) => {
    if (!value) return "University type is required";
    return true;
};

const validateShift = (value) => {
    if (!value) return "Shift is required";
    return true;
};

const headers = computed(() => [
    {
        title: t("menu.university"),
        key: "university",
        align: "start",
        sortable: false,
    },
    { title: t("total_males"), key: "Total_Males", align: "center" },
    { title: t("total_females"), key: "Total_Females", align: "center" },
    { title: t("total_students"), key: "Total_Students", align: "center" },
    { title: t("male_percentage"), key: "Male_Percentage", align: "center" },
    {
        title: t("female_percentage"),
        key: "Female_Percentage",
        align: "center",
    },
]);
</script>

<style scoped>
/* Additional styling for consistent appearance */
.d-flex.flex-wrap {
    gap: 16px;
}
</style>
