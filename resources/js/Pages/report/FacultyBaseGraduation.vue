<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('Faculty Base Graduation')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

        <!-- Filter & Print Section -->
        <v-row class="pt-6 pb-4" align="center" dense>
            <v-col cols="12" sm="6" md="3">
                <DatePicker
                    v-model="ReportRepository.date"
                    format="jYYYY"
                    type="year"
                    :placeholder="$t('Select or Type Year')"
                    rounded
                    :auto-submit="true"
                    @update:modelValue="onDateChange"
                />
            </v-col>
            <v-col cols="12" sm="6" md="3">
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
                    density="compact"
                    hide-details
                    @update:modelValue="onDateChange"
                />
            </v-col>
            <v-col cols="12" sm="6" md="3">
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
                    density="compact"
                    hide-details
                    @update:modelValue="onDateChange"
                />
            </v-col>
            <v-col cols="12" sm="6" md="3" class="text-sm-right">
                <v-btn color="primary" @click="printTable" class="mt-2 mt-sm-0">
                    {{ $t("print_report") }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- Table Section -->
        <v-data-table-server
            v-model:items-per-page="ReportRepository.itemsPerPage"
            :headers="headers"
            :items-length="ReportRepository.totalItems"
            :items="flatFacultyData"
            :loading="ReportRepository.loading"
            :search="ReportRepository.search"
            @update:options="onOptionsUpdate"
            class="w-100 mx-auto"
            hover
        >
            <template #item.university="{ item }">
                <span v-if="item.showUniversity">{{ item.university }}</span>
            </template>

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
import { ref, computed, onMounted } from "vue";
import { useReportRepository } from "../../store/ReportRepository";
import { useI18n } from "vue-i18n";
import persianDate from "persian-date";
import DatePicker from "vue3-persian-datetime-picker";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));
const ReportRepository = useReportRepository();

// Initialize pagination
ReportRepository.page = ReportRepository.page || 1;
ReportRepository.itemsPerPage = ReportRepository.itemsPerPage || 10;

const currentYear = ref(new persianDate().year());
const yearRange = computed(() => {
    const range = [];
    for (let i = currentYear.value - 10; i <= currentYear.value + 10; i++)
        range.push(i);
    return range;
});

// Page size options
const pageSizeOptions = [
    { value: 5, text: "5" },
    { value: 10, text: "10" },
    { value: 25, text: "25" },
    { value: 50, text: "50" },
    { value: 100, text: "100" },
];

// Computed properties
const totalPages = computed(() =>
    Math.ceil(ReportRepository.totalItems / ReportRepository.itemsPerPage)
);
const showPagination = computed(
    () => ReportRepository.totalItems > ReportRepository.itemsPerPage
);

const flatFacultyData = computed(() => {
    if (!ReportRepository.facultyBaseGraduation.length) return [];
    const flatData = [];
    ReportRepository.facultyBaseGraduation.forEach((institution) => {
        institution.faculties.forEach((faculty, fIndex) => {
            flatData.push({
                ...faculty,
                university: institution.university,
                showUniversity: fIndex === 0,
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
    ReportRepository.fetchFacultyBaseGraduation(
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
    { title: t("Faculty"), key: "faculty", align: "start", sortable: false },
    { title: t("Male"), key: "Total_Males", align: "center" },
    { title: t("Female"), key: "Total_Females", align: "center" },
    { title: t("Total"), key: "Total_Students", align: "center" },
]);

const printTable = () => {
    const data = ReportRepository.facultyBaseGraduation;
    if (!data.length) return;

    const rows = data
        .map((institution) =>
            institution.faculties
                .map((faculty, index) => {
                    const universityCell =
                        index === 0
                            ? `<td rowspan="${institution.faculties.length}">${institution.university}</td>`
                            : "";
                    return `<tr>${universityCell}<td>${
                        faculty.faculty
                    }</td><td>${faculty.Total_Males || 0}</td><td>${
                        faculty.Total_Females || 0
                    }</td><td>${faculty.Total_Students || 0}</td></tr>`;
                })
                .join("")
        )
        .join("");

    const html = `
    <html dir="${dir.value}">
      <head>
        <title>${t("Faculty Base Graduation")}</title>
        <style>
          body { font-family: Arial, sans-serif; padding: 20px; direction: ${
              dir.value
          }; }
          .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 16px; }
          table { width: 100%; border-collapse: collapse; text-align: center; }
          th, td { border: 1px solid #ccc; padding: 8px; }
          th { background-color: #e7f2f5; }
        </style>
      </head>
      <body>
        <div class="title">${t("Faculty Base Graduation")} - ${
        ReportRepository.date
    } (${t(ReportRepository.season)}, ${t(ReportRepository.shift)})</div>
        <table>
          <thead>
            <tr><th>${t("University")}</th><th>${t("Faculty")}</th><th>${t(
        "Male"
    )}</th><th>${t("Female")}</th><th>${t("Total")}</th></tr>
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

onMounted(() => {
    fetchData();
});
</script>

<style scoped>
.d-flex.flex-wrap {
    gap: 16px;
    margin-top: 20px;
    padding: 16px 0;
    border-top: 1px solid #eee;
}
:deep(td) {
    vertical-align: middle;
}
</style>
