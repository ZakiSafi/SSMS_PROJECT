<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('facultyBase')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

        <div class="w-full d-flex justify-space-between align-center pt-6 pb-6">
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

            <!-- Right side: Season and University Selects -->
            <div class="w-2/3 flex align-center">
                <div class="w-2/3 mt-6">
                    <v-select
                        v-model="ReportRepository.shift"
                        :items="[
                            { text: $t('day'), value: 'day' },
                            { text: $t('night'), value: 'night' },
                        ]"
                        item-title="text"
                        item-value="value"
                        :label="$t('Select Shift')"
                        variant="outlined"
                        density="compact"
                        :rules="[validateShift]"
                        @update:modelValue="onShiftChange"
                    />
                </div>
                <div class="w-2/3">
                    <v-select
                        class="ml-4 mr-4"
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
                <div class="w-3/4 ml-4">
                    <v-combobox
                        v-model="ReportRepository.university"
                        :items="[
                            ...ReportRepository.allUniversities,
                            { id: 'all', name: $t('all') },
                        ]"
                        item-title="name"
                        item-value="id"
                        :label="$t('select_university')"
                        variant="outlined"
                        hide-details
                        density="compact"
                    />
                </div>
                <div class="ml-4 flex align-center">
                    <v-btn color="primary" @click="printTable">
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
                        <th colspan="3" v-for="n in 6" :key="'head-' + n">
                            {{ $t("Class") }} {{ n }}
                        </th>
                    </tr>
                    <tr>
                        <template v-for="n in 6" :key="'subhead-' + n">
                            <th>{{ $t("Male") }}</th>
                            <th>{{ $t("Female") }}</th>
                            <th>{{ $t("Total") }}</th>
                        </template>
                    </tr>
                </thead>

                <!-- Loading bar -->
                <tr v-if="ReportRepository.loading" class="loading-row">
                    <td colspan="20">
                        <v-progress-linear
                            :reverse="dir === 'rtl'"
                            indeterminate
                            color="primary"
                            height="4"
                            class="ma-0"
                        />
                    </td>
                </tr>

                <tbody v-if="ReportRepository.jawad.length">
                    <template
                        v-for="(institution, index) in ReportRepository.jawad"
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
                                <td>
                                    {{
                                        faculty.faculty || faculty.faculty_name
                                    }}
                                </td>

                                <template
                                    v-for="classNum in 6"
                                    :key="'class-' + classNum"
                                >
                                    <td>
                                        {{
                                            faculty.classes[`class${classNum}`]
                                                ?.Total_males || 0
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            faculty.classes[`class${classNum}`]
                                                ?.Total_Females || 0
                                        }}
                                    </td>
                                    <td>
                                        {{
                                            faculty.classes[`class${classNum}`]
                                                ?.Total_Students || 0
                                        }}
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </template>
                </tbody>

                <tbody v-else>
                    <tr>
                        <td colspan="20" class="text-center py-4">
                            {{ $t("No data available") }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination and Items per page -->
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
                    { value: -1, text: $t('pagination.all') },
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
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref, computed, watch, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useReportRepository } from "../../store/ReportRepository";
import DatePicker from "vue3-persian-datetime-picker";
import persianDate from "persian-date";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

const ReportRepository = useReportRepository();

// Initialize pagination values
ReportRepository.page = ReportRepository.page || 1;
ReportRepository.itemsPerPage = ReportRepository.itemsPerPage || 10;

// Reactive year (DatePicker)
const selectedYear = ref(ReportRepository.date || new persianDate().year());

const rules = {
    required: (v) => !!v || t("validation.required"),
};

const onPageChange = (newPage) => {
    ReportRepository.page = newPage;
    fetchData();
};

const onItemsPerPageChange = () => {
    ReportRepository.page = 1; // Reset to first page when items per page changes
    fetchData();
};

// Update repository and fetch data when year changes
watch(selectedYear, (newYear) => {
    ReportRepository.date = newYear;
    ReportRepository.page = 1;
    fetchData();
});

// Watch season or university change
watch(
    () => ReportRepository.season,
    () => {
        ReportRepository.page = 1;
        fetchData();
    }
);
watch(
    () => ReportRepository.university,
    () => {
        ReportRepository.page = 1;
        fetchData();
    }
);

watch(
    () => ReportRepository.shift,
    (newShift) => {
        if (newShift) {
            ReportRepository.page = 1;
            fetchData();
        }
    }
);

// Centralized fetch
const fetchData = () => {
    const universityId =
        ReportRepository.university?.id || ReportRepository.university;
    ReportRepository.fecthJawad(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.season,
        universityId
    );
};

// fetch shift
const onShiftChange = (shift) => {
    ReportRepository.shift = shift;
    ReportRepository.page = 1;
    fetchData();
};

// Initial load
onMounted(() => {
    ReportRepository.fetchUniversities();
    fetchData();
});

// printing option function
const printTable = () => {
    const tableEl = document.querySelector(".gender-stats-table");
    if (!tableEl) return;

    const tableHtml = tableEl.outerHTML;
    const season = t(ReportRepository.season || "");
    const university = ReportRepository.university?.name || t("all");

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
        <div class="title">${t("facultyBase")}</div>
        ${tableHtml}
      </body>
    </html>`;

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

/* Pagination container styling */
.d-flex.flex-wrap {
    gap: 16px;
}
</style>
