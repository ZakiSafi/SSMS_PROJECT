<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('student_type_report')" />
        <v-divider :thickness="1" class="border-opacity-100" />

        <!-- Filters and Print Button -->
        <v-row class="pt-6 pb-6" align="center">
            <!-- Year Combobox -->
            <v-col cols="3">
                <v-combobox
                    v-model="ReportRepository.date"
                    :items="yearRange"
                    :label="$t('Select or Type Year')"
                    variant="outlined"
                    density="compact"
                    hide-details
                    :rules="[validateYearInput]"
                    @update:modelValue="onDateChange"
                />
            </v-col>

            <!-- Shift Select -->
            <v-col cols="3">
                <v-select
                    v-model="ReportRepository.shift"
                    :items="[
                        { text: $t('all'), value: 'all' },
                        { text: $t('day'), value: 'day' },
                        { text: $t('night'), value: 'night' },
                    ]"
                    item-title="text"
                    item-value="value"
                    :label="$t('Select Shift')"
                    variant="outlined"
                    density="compact"
                    hide-details
                    :rules="[validateShift]"
                    @update:modelValue="onShiftChange"
                />
            </v-col>

            <!-- Print Button -->
            <v-col cols="auto">
                <v-btn color="primary" @click="printTable">
                    {{ $t("print_report") }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- Table -->
        <div class="table-container">
            <table class="student-type-table">
                <thead>
                    <tr>
                        <th rowspan="2">{{ $t("University") }}</th>
                        <th rowspan="2">{{ $t("Faculty") }}</th>
                        <th colspan="3">{{ $t("Newly Enrolled") }}</th>
                        <th colspan="3">{{ $t("Graduated") }}</th>
                        <th colspan="3">{{ $t("Current") }}</th>
                    </tr>
                    <tr>
                        <th>{{ $t("Male") }}</th>
                        <th>{{ $t("Female") }}</th>
                        <th>{{ $t("Total") }}</th>
                        <th>{{ $t("Male") }}</th>
                        <th>{{ $t("Female") }}</th>
                        <th>{{ $t("Total") }}</th>
                        <th>{{ $t("Male") }}</th>
                        <th>{{ $t("Female") }}</th>
                        <th>{{ $t("Total") }}</th>
                    </tr>
                </thead>

                <tr v-if="ReportRepository.loading" class="loading-row">
                    <td colspan="11">
                        <v-progress-linear
                            indeterminate
                            color="primary"
                            height="4"
                            class="ma-0"
                        />
                    </td>
                </tr>

                <tbody v-if="flatRows.length">
                    <tr v-for="(row, index) in flatRows" :key="index">
                        <td>{{ row.university }}</td>
                        <td>{{ row.faculty }}</td>
                        <td>{{ row.new_males }}</td>
                        <td>{{ row.new_females }}</td>
                        <td>{{ row.new_total }}</td>
                        <td>{{ row.graduated_males }}</td>
                        <td>{{ row.graduated_females }}</td>
                        <td>{{ row.graduated_total }}</td>
                        <td>{{ row.current_males }}</td>
                        <td>{{ row.current_females }}</td>
                        <td>{{ row.current_total }}</td>
                    </tr>
                </tbody>

                <tbody v-else>
                    <tr>
                        <td colspan="11" class="text-center py-4">
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
import { useReportRepository } from "@/store/ReportRepository";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));
const ReportRepository = useReportRepository();

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

const validateShift = (value) => {
    if (!value) return "Shift is required";
    return true;
};

// Flatten nested data for the table
const flatRows = computed(() => {
    if (!ReportRepository.studentTypeReport) return [];
    const rows = [];
    ReportRepository.studentTypeReport.forEach((uni) => {
        uni.faculties.forEach((faculty) => {
            rows.push({
                university: uni.university,
                faculty: faculty.faculty,
                new_males: faculty.students?.new?.males || 0,
                new_females: faculty.students?.new?.females || 0,
                new_total: faculty.students?.new?.total || 0,
                graduated_males: faculty.students?.graduated?.males || 0,
                graduated_females: faculty.students?.graduated?.females || 0,
                graduated_total: faculty.students?.graduated?.total || 0,
                current_males: faculty.students?.current?.males || 0,
                current_females: faculty.students?.current?.females || 0,
                current_total: faculty.students?.current?.total || 0,
            });
        });
    });
    return rows;
});

const fetchData = (options = {}) => {
    const page = options.page || 1;
    const itemsPerPage = options.itemsPerPage || ReportRepository.itemsPerPage;
    ReportRepository.fetchStudentTypeReport(
        { page, itemsPerPage },
        ReportRepository.date,
        ReportRepository.shift
    );
};

const onDateChange = (date) => {
    ReportRepository.date = date;
    fetchData({ page: 1 });
};

const onShiftChange = (shift) => {
    ReportRepository.shift = shift;
    fetchData({ page: 1 });
};

const printTable = () => {
    const tableEl = document.querySelector(".student-type-table");
    if (!tableEl) return;

    const tableHtml = tableEl.outerHTML;

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
            margin-bottom: 12px;
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
        <div class="title">${t("student_type_report")}</div>
        ${tableHtml}
      </body>
    </html>`;

    const printWindow = window.open("", "PrintWindow");
    printWindow.document.write(html);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
};

onMounted(() => {
    ReportRepository.date = currentYear.value;
    ReportRepository.shift = "day";
    fetchData();
});
</script>

<style scoped>
.table-container {
    overflow-x: auto;
}
.student-type-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}
.student-type-table th {
    background-color: #e7f2f5;
    padding: 8px;
    border: 1px solid #ddd;
}
.student-type-table td {
    padding: 10px 8px;
    border: 1px solid #ddd;
}
.loading-row td {
    padding: 0 !important;
    border: none !important;
}
.v-progress-linear {
    margin: 0;
}
</style>
