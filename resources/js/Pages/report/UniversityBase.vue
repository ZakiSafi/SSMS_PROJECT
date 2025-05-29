<template>
    <AppBar
        :pageTitle="`Statistical Data of Students in ${ReportRepository.type} Higher Education Institutions – Day and Night Shifts, Year ${ReportRepository.date}`"
    />
    <v-divider :thickness="1" class="border-opacity-100" />

    <div class="w-full flex flex gap-4 pt-6">
        <v-combobox
            v-model="ReportRepository.date"
            :items="yearRange"
            label="Select or Type Year"
            variant="outlined"
            density="compact"
            :rules="[validateYearInput]"
            @update:modelValue="onDateChange"
        ></v-combobox>

        <v-select
            v-model="ReportRepository.type"
            :items="['public', 'private']"
            label="Select University Type"
            variant="outlined"
            density="compact"
            :rules="[validateType]"
            @update:modelValue="onTypeChange"
        ></v-select>

        <v-select
            v-model="ReportRepository.shift"
            :items="['Day', 'Night']"
            label="Select Shift"
            variant="outlined"
            density="compact"
            :rules="[validateShift]"
            @update:modelValue="onShiftChange"
        ></v-select>
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

const headers = [
    { title: "University", key: "university", align: "start", sortable: false },
    { title: "Total Males", key: "Total_Males", align: "center" },
    { title: "Total Females", key: "Total_Females", align: "center" },
    { title: "Total Students", key: "Total_Students", align: "center" },
    { title: "Male Percentage", key: "Male_Percentage", align: "center" },
    { title: "Female Percentage", key: "Female_Percentage", align: "center" },
];
</script>

<style scoped>
.v-data-table {
    --primary-color: none;
    --row-spacing: 10px;
}
</style>
