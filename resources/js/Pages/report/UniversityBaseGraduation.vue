<template>
    <AppBar pageTitle="University Base Graduation Report" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

   <div class="w-[24rem] pt-6 pb-6  d-flex align-center">
  <v-combobox
    class="mr-4"
    v-model="ReportRepository.date"
    :items="yearRange"
    label="Select or Type Year"
    variant="outlined"
    density="compact"
    hide-details
    @update:modelValue="onDateChange"
  ></v-combobox>

  <v-select
    v-model="ReportRepository.season"
    :items="[
        {text: $t('spring'), value: 'public'},
        {text: $t('autumn'), value: 'autumn'}
        ]"

    item-title = 'text'
    item-value = 'value'
    label="Select Session"
    variant="outlined"
    hide-details
    density="compact"
    @update:modelValue="onDateChange"
  ></v-select>
</div>

    <v-data-table-server
        v-model:items-per-page="ReportRepository.itemsPerPage"
        :headers="headers"
        :items-length="ReportRepository.totalItems"
        :items="ReportRepository.universityBaseGraduation"
        :loading="ReportRepository.loading"
        :search="ReportRepository.search"
        @update:options="
            (options) =>
                ReportRepository.fetchUniversityBaseGraduation(options, ReportRepository.date, ReportRepository.season)
        "
        class="w-100 mx-auto"
        hover
    ></v-data-table-server>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
const ReportRepository = useReportRepository();
import persianDate from "persian-date";

const getCurrentPersianYear = () => {
  return new persianDate().year();
}

const currentYear = ref(getCurrentPersianYear());
const yearRange = computed(() => {
  const years = [];
  const startYear = currentYear.value - 10;
  const endYear = currentYear.value + 10;

  for (let i = startYear; i <= endYear; i++) {
    years.push(i);
  }
  return years;
})

const onDateChange = () => {
    ReportRepository.fetchUniversityBaseGraduation(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.session
    );
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

<style scoped>

</style>
