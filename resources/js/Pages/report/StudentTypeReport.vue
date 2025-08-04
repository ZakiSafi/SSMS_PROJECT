<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('student_type_report')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

        <v-row class="pt-6 pb-6" align="center" justify="space-between">
            <!-- Year Combobox -->
            <v-col cols="3">
                <v-combobox
                    v-model="ReportRepository.date"
                    :items="yearRange"
                    :label="$t('Select or Type Year')"
                    variant="outlined"
                    density="compact"
                    hide-details
                    @update:modelValue="onFilterChange"
                />
            </v-col>

            <!-- Shift Select -->
            <v-col cols="2">
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
                    @update:modelValue="onFilterChange"
                />
            </v-col>

            <!-- Print Button -->
            <v-col cols="2">
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

                <!-- Loading State -->
                <tr v-if="ReportRepository.loading" class="loading-row">
                    <td colspan="11">
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
                <tbody v-if="ReportRepository.studentTypeReport.length">
                    <template
                        v-for="(
                            university, uIndex
                        ) in ReportRepository.studentTypeReport"
                        :key="'uni-' + uIndex"
                    >
                        <template
                            v-for="(faculty, fIndex) in university.faculties"
                            :key="'fac-' + fIndex"
                        >
                            <tr>
                                <template v-if="fIndex === 0">
                                    <td :rowspan="university.faculties.length">
                                        {{ university.university }}
                                    </td>
                                </template>
                                <td>{{ faculty.faculty }}</td>

                                <!-- Newly Enrolled -->
                                <td>{{ faculty.students.new?.males || 0 }}</td>
                                <td>
                                    {{ faculty.students.new?.females || 0 }}
                                </td>
                                <td>{{ faculty.students.new?.total || 0 }}</td>

                                <!-- Graduated -->
                                <td>
                                    {{ faculty.students.graduated?.males || 0 }}
                                </td>
                                <td>
                                    {{
                                        faculty.students.graduated?.females || 0
                                    }}
                                </td>
                                <td>
                                    {{ faculty.students.graduated?.total || 0 }}
                                </td>

                                <!-- Current -->
                                <td>
                                    {{ faculty.students.current?.males || 0 }}
                                </td>
                                <td>
                                    {{ faculty.students.current?.females || 0 }}
                                </td>
                                <td>
                                    {{ faculty.students.current?.total || 0 }}
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>

                <!-- No Data -->
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
import { ref, computed, onMounted, watch } from "vue";
import { useReportRepository } from "@/store/ReportRepository";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

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

const onFilterChange = () => {
    fetchData();
};

const fetchData = () => {
    ReportRepository.fetchStudentTypeReport(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.shift
    );
};

onMounted(() => {
    // Initialize default values
    ReportRepository.date = currentYear.value;
    ReportRepository.shift = "day";
    fetchData();
});

// Debugging - log when component mounts
onMounted(() => {
    console.log("StudentTypeReport mounted");
    console.log("Current filters:", {
        date: ReportRepository.date,
        shift: ReportRepository.shift,
    });
});

// Add this to watch for data changes
watch(
    () => ReportRepository.studentTypeReport,
    (newData) => {
        console.log("Data updated:", newData);
    },
    { deep: true }
);

const printTable = () => {
    const tableEl = document.querySelector(".student-type-table");
    if (!tableEl) return;

    const tableHtml = tableEl.outerHTML;
    const shift =
        ReportRepository.shift === "all" ? t("all") : t(ReportRepository.shift);

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
    <div class="title">${t("student_type_report")} - ${
        ReportRepository.date
    } (${shift})</div>
    ${tableHtml}
  </body>
</html>
`;

    const printWindow = window.open("", "PrintWindow");
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
