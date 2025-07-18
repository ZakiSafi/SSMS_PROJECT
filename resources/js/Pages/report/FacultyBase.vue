<template>
    <div :dir="dir">
        <AppBar :pageTitle="$t('facultyBase')" />
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>

        <div class="w-full d-flex justify-space-between pt-6 pb-6">
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
            <div class="w-1/3 flex">
                <div class="w-2/3">
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
                        class="mx-4"
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

// Reactive year (DatePicker)
const selectedYear = ref(ReportRepository.date || new persianDate().year());

// Update repository and fetch data when year changes
watch(selectedYear, (newYear) => {
    ReportRepository.date = newYear;
    fetchData();
});

// Watch season or university change
watch(
    () => ReportRepository.season,
    () => fetchData()
);
watch(
    () => ReportRepository.university,
    () => fetchData()
);

watch(
    () => ReportRepository.shift,
    (newShift) => {
        if (newShift) {
            fetchData();
        }
    }
);

const rules = {
    required: (v) => !!v || t("validation.required"),
};

// Centralized fetch
const fetchData = () => {
    const universityId =
        ReportRepository.university?.id || ReportRepository.university;
    ReportRepository.fecthJawad(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.season,
        universityId
    );
};
// fetch shift
const onShiftChange = (shift) => {
    ReportRepository.shift = shift;
    fetchData();
};
// Initial load
onMounted(() => {
    ReportRepository.fetchUniversities();
    fetchData();
});
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
</style>
