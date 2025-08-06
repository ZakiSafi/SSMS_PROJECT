<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('DBGR')" />
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

            <!-- Shift -->
            <div class="w-[200px] mx-4">
                <v-select
                    v-model="ReportRepository.shift"
                    :items="[
                        { text: $t('day'), value: 'day' },
                        { text: $t('night'), value: 'night' },
                    ]"
                    item-title="text"
                    item-value="value"
                    :label="$t('shift')"
                    variant="outlined"
                    hide-details
                    density="compact"
                    @update:modelValue="onDateChange"
                />
            </div>

            <!-- Print Button -->
            <div class="shrink-0 mx-4">
                <v-btn color="primary" @click="printTable">
                    {{ $t("print_report") }}
                </v-btn>
            </div>
        </div>

        <!-- Data Table -->
        <v-data-table-server
            v-model:items-per-page="ReportRepository.itemsPerPage"
            :headers="headers"
            :items-length="ReportRepository.totalItems"
            :items="flatDepartmentData"
            :loading="ReportRepository.loading"
            :search="ReportRepository.search"
            :dir="dir"
            @update:options="onOptionsUpdate"
            class="w-100 mx-auto"
            hover
        >
            <template #bottom>
                <div class="d-flex flex-wrap align-center justify-center pa-2">
                    <!-- Pagination with forced LTR direction -->
                    <div dir="ltr" class="mr-4">
                        <v-pagination
                            v-model="ReportRepository.page"
                            :length="totalPages"
                            @update:modelValue="onPageChange"
                            total-visible="7"
                            color="primary"
                            v-if="showPagination"
                        />
                    </div>

                    <!-- Items per page selector -->
                    <v-select
                        v-model="ReportRepository.itemsPerPage"
                        :items="pageSizeOptions"
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
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

const ReportRepository = useReportRepository();

// Initialize pagination values
ReportRepository.page = ReportRepository.page || 1;
ReportRepository.itemsPerPage = ReportRepository.itemsPerPage || 10;

// Page size options
const pageSizeOptions = [
    { value: 5, text: "5" },
    { value: 10, text: "10" },
    { value: 25, text: "25" },
    { value: 50, text: "50" },
    { value: 100, text: "100" },
];

// Computed properties
const getCurrentPersianYear = () => new persianDate().year();
const currentYear = ref(getCurrentPersianYear());

const yearRange = computed(() => {
    const years = [];
    const start = currentYear.value - 10;
    const end = currentYear.value + 10;
    for (let i = start; i <= end; i++) years.push(i);
    return years;
});

const totalPages = computed(() => {
    return ReportRepository.itemsPerPage === -1
        ? 1
        : Math.ceil(
              ReportRepository.totalItems / ReportRepository.itemsPerPage
          );
});

const showPagination = computed(() => {
    return ReportRepository.totalItems > ReportRepository.itemsPerPage;
});

// Flatten the nested department data for the table
const flatDepartmentData = computed(() => {
    if (!ReportRepository.departmentBaseGraduation.length) return [];

    const flatData = [];
    ReportRepository.departmentBaseGraduation.forEach((institution) => {
        institution.faculties.forEach((faculty) => {
            faculty.departments.forEach((department) => {
                flatData.push({
                    university: institution.university,
                    faculty: faculty.faculty,
                    department: department.department,
                    Total_Males: department.Total_Males || 0,
                    Total_Females: department.Total_Females || 0,
                    Total_Students: department.Total_Students || 0,
                    Male_Percentage: department.Male_Percentage || 0,
                    Female_Percentage: department.Female_Percentage || 0,
                });
            });
        });
    });
    return flatData;
});

// Methods
const onOptionsUpdate = (options) => {
    ReportRepository.page = options.page;
    ReportRepository.itemsPerPage = options.itemsPerPage;
    fetchData();
};

const onPageChange = (newPage) => {
    ReportRepository.page = newPage;
    fetchData();
};

const onItemsPerPageChange = () => {
    ReportRepository.page = 1;
    fetchData();
};

const onDateChange = () => {
    ReportRepository.page = 1;
    fetchData();
};

const fetchData = () => {
    ReportRepository.fetchDepartmentBaseGraduation(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.season,
        ReportRepository.shift
    );
};

const headers = computed(() => [
    {
        title: t("University"),
        key: "university",
        align: "start",
        sortable: false,
    },
    {
        title: t("Faculty"),
        key: "faculty",
        align: "start",
        sortable: false,
    },
    {
        title: t("Department"),
        key: "department",
        align: "start",
        sortable: false,
    },
    {
        title: t("Male"),
        key: "Total_Males",
        align: "center",
    },
    {
        title: t("Female"),
        key: "Total_Females",
        align: "center",
    },
    {
        title: t("Total"),
        key: "Total_Students",
        align: "center",
    },
    {
        title: t("male_percentage"),
        key: "Male_Percentage",
        align: "center",
    },
    {
        title: t("female_percentage"),
        key: "Female_Percentage",
        align: "center",
    },
]);

const printTable = () => {
    const data = flatDepartmentData.value;
    if (!data.length) return;

    const rows = data
        .map((item) => {
            return `<tr>
                <td>${item.university}</td>
                <td>${item.faculty}</td>
                <td>${item.department}</td>
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
        <div class="title">${t("DBGR")} - ${ReportRepository.date} (${t(
        ReportRepository.season
    )}, ${t(ReportRepository.shift)})</div>
        <table>
          <thead>
            <tr>
              <th>${t("University")}</th>
              <th>${t("Faculty")}</th>
              <th>${t("Department")}</th>
              <th>${t("Male")}</th>
              <th>${t("Female")}</th>
              <th>${t("Total")}</th>
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
.d-flex.flex-wrap {
    gap: 16px;
    margin-top: 20px;
    padding: 16px 0;
    border-top: 1px solid #eee;
}
</style>
