<template>
  <div  :dir="dir">
    <AppBar :pageTitle="$t('StudentTeacherRatio')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

   <div class="w-25 pt-6 ">
  <v-combobox
    class="mr-4"
    v-model="ReportRepository.date"
    :items="yearRange"
    :label="$t('Select or Type Year')"
    variant="outlined"
    density="compact"
    @update:modelValue="onDateChange"
  ></v-combobox>
</div>

    <v-data-table-server
        v-model:items-per-page="ReportRepository.itemsPerPage"
        :headers="headers"
        :items-length="ReportRepository.totalItems"
        :items="ReportRepository.studentTeacher"
        :loading="ReportRepository.loading"
        :search="ReportRepository.search"
        @update:options="
            (options) =>
                ReportRepository.fetchStudentTeacherRatio(options, ReportRepository.date)
        "
        class="w-100 mx-auto"
        hover
    >
    <template #bottom>
                <div class="d-flex align-center justify-end pa-2" >
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
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
                        density="compact"
                        variant="outlined"
                        hide-details
                        style="max-width: 100px"
                    ></v-select>
                </div>
            </template>
</v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "@/store/ReportRepository";
import { ref, computed } from "vue";
const ReportRepository = useReportRepository();
import persianDate from "persian-date";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "en" ? "ltr" : "rtl"; // Correctly set "rtl" and "ltr"
});


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
    ReportRepository.fetchStudentTeacherRatio(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
    );
};

const headers = computed(() =>[
  { title: t("University"), key: "university", align: "start", sortable: false },
  { title: t("Teacher"), key: "teachers", align: "start" },
  { title: t("Total Student"), key: "total_students", align: "center" },
  { title: t("ST Ratio"), key: "students_per_teacher_ratio", align: "center" },
]);

</script>

<style scoped>

</style>
