<template>
    <AppBar
        :pageTitle="`Statistical Data of Students in ${ReportRepository.type} Higher Education Institutions – Day and Night Shifts, Year ${ReportRepository.date}`"
    />
    <v-divider :thickness="1" class="border-opacity-100" />

    <div class="w-full flex justify-between items-start gap-4 pt-6">
        <!-- Left side: Combobox -->
        <div class="w-1/5">
            <v-combobox
                v-model="ReportRepository.date"
                :items="yearRange"
                label="Select or Type Year"
                variant="outlined"
                density="compact"
                :rules="[validateYearInput]"
                @update:modelValue="onDateChange"
            ></v-combobox>
        </div>

        <!-- Right side: Two selects side by side -->
        <div class="w-1/4 flex">
            <div class="w-1/2">
                <v-select
                    v-model="ReportRepository.type"
                    :items="[
                        { text: $t('public'), value: 'public' },
                        { text: $t('private'), value: 'private' },
                    ]"
                    label="Select University Type"
                    variant="outlined"
                    density="compact"
                    item-title="text"
                    item-value="value"
                    :rules="[validateType]"
                    @update:modelValue="onTypeChange"
                />
            </div>
            <div class="w-1/2 ml-4">
                <v-select
                    v-model="ReportRepository.shift"
                    :items="[{text:$t('day'), value: 'Day'}, {text:$t('night'), value: 'Night'}]"
                    item-title = text
                    item-value = value
                    label="Select Shift"
                    variant="outlined"
                    density="compact"
                    :rules="[validateShift]"
                    @update:modelValue="onShiftChange"
                ></v-select>
            </div>
        </div>
    </div>

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
    ></v-data-table-server>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
import persianDate from "persian-date";

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

const fetchData = () => {
    ReportRepository.fetchUniversity(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.type,
        ReportRepository.shift
    );
};

const onDateChange = (date) => {
    ReportRepository.date = date;
    fetchData();
};

const onTypeChange = (type) => {
    ReportRepository.type = type;
    fetchData();
};

const onShiftChange = (shift) => {
    ReportRepository.shift = shift;
    fetchData();
};

const onTableOptionsUpdate = (options) => {
    ReportRepository.fetchUniversity(
        options,
        ReportRepository.date,
        ReportRepository.type,
        ReportRepository.shift
    );
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

import { useI18n } from 'vue-i18n';
const { t } = useI18n();

const headers = computed(() => [
  { title: t("menu.university"), key: "university", align: "start", sortable: false },
  { title: t("total_males"), key: "Total_Males", align: "center" },
  { title: t("total_females"), key: "Total_Females", align: "center" },
  { title: t("total_students"), key: "Total_Students", align: "center" },
  { title: t("male_percentage"), key: "Male_Percentage", align: "center" },
  { title: t("female_percentage"), key: "Female_Percentage", align: "center" },
])


</script>

<style scoped></style>
