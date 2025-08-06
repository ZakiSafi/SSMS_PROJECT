<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('University Classes')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

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
                    @update:modelValue="onDateChange"
                />
            </v-col>

            <!-- Shift Select -->
            <v-col cols="3">
                <v-select
                    v-model="ReportRepository.shift"
                    :items="[$t('day'), $t('night')]"
                    :label="$t('Select Shift')"
                    variant="outlined"
                    density="compact"
                    hide-details
                    @update:modelValue="onDateChange"
                />
            </v-col>

            <!-- Print Button -->
            <v-col cols="3">
                <v-btn color="primary" @click="printTable">
                    {{ $t("print_report") }}
                </v-btn>
            </v-col>
        </v-row>

        <!-- Table -->
        <div class="table-container">
            <table class="gender-stats-table">
                <thead>
                    <tr>
                        <th rowspan="2">{{ $t("University") }}</th>
                        <th colspan="3">{{ $t("Class") }} 1</th>
                        <th colspan="3">{{ $t("Class") }} 2</th>
                        <th colspan="3">{{ $t("Class") }} 3</th>
                        <th colspan="3">{{ $t("Class") }} 4</th>
                        <th colspan="3">{{ $t("Class") }} 5</th>
                        <th colspan="3">{{ $t("Class") }} 6</th>
                    </tr>
                    <tr>
                        <template v-for="n in 6" :key="n">
                            <th>{{ $t("Male") }}</th>
                            <th>{{ $t("Female") }}</th>
                            <th>{{ $t("Total") }}</th>
                        </template>
                    </tr>
                </thead>

                <tr v-if="ReportRepository.loading" class="loading-row">
                    <td colspan="19">
                        <v-progress-linear
                            indeterminate
                            color="primary"
                            height="4"
                            class="ma-0"
                        />
                    </td>
                </tr>

                <tbody v-if="ReportRepository.universityClasses.length">
                    <tr
                        v-for="(
                            institution, index
                        ) in ReportRepository.universityClasses"
                        :key="index"
                    >
                        <td>{{ institution.university_name }}</td>
                        <template v-for="classNum in 6" :key="classNum">
                            <td>
                                {{
                                    institution.classes?.[`class${classNum}`]
                                        ?.Total_males || 0
                                }}
                            </td>
                            <td>
                                {{
                                    institution.classes?.[`class${classNum}`]
                                        ?.Total_Females || 0
                                }}
                            </td>
                            <td>
                                {{
                                    institution.classes?.[`class${classNum}`]
                                        ?.Total_Students || 0
                                }}
                            </td>
                        </template>
                    </tr>
                </tbody>

                <tbody v-else>
                    <tr>
                        <td colspan="19" class="text-center py-4">
                            {{ $t("No data available") }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Items per page -->
        <div class="d-flex flex-wrap align-center justify-center py-4">
            <div dir="ltr" class="mr-4">
                <!-- Force LTR for pagination -->
                <v-pagination
                    v-model="ReportRepository.page"
                    :length="
                        Math.ceil(
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
            <v-select
                v-model="ReportRepository.itemsPerPage"
                :items="[5, 10, 20, 50, 100]"
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
import { ref, computed, onMounted } from "vue";
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
// In your <script setup>
const onPageChange = (newPage) => {
    ReportRepository.page = newPage;
    ReportRepository.fetchUniversityClasses(
        { page: newPage, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.shift
    );
};

const onItemsPerPageChange = () => {
    ReportRepository.page = 1;
    ReportRepository.fetchUniversityClasses(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.shift
    );
};
const onDateChange = () => {
    ReportRepository.page = 1;
    ReportRepository.fetchUniversityClasses(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.shift
    );
};

onMounted(() => {
    ReportRepository.page = 1;
    ReportRepository.fetchUniversityClasses(
        {
            page: ReportRepository.page,
            itemsPerPage: ReportRepository.itemsPerPage,
        },
        ReportRepository.date,
        ReportRepository.shift
    );
});

const printTable = () => {
    const tableEl = document.querySelector(".gender-stats-table");
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
        <div class="title">${t("University Classes")}</div>
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
    border-bottom: 1px solid #ddd;
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
