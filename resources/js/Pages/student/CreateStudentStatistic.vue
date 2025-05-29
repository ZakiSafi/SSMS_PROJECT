<template>
  <div dir="rtl">
    <v-dialog transition="dialog-top-transition" width="45rem" v-model="StudentStatisticsRepository.createDialog">
      <template v-slot:default="{ isActive }">
        <v-card class="px-3">
          <v-card-title class="px-2 pt-4 d-flex justify-space-between">
            <h2 class="font-weight-bold pl-4">
              {{ StudentStatisticsRepository.isEditMode ? "Update" : "Create" }}
            </h2>
            <v-btn variant="text" @click="isActive.value = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-divider class="border-opacity-100 mx-6"></v-divider>

          <v-card-text>
            <v-form ref="formRef" class="pt-4">
              <v-row dense>
                <!-- Row 1 -->
                <v-col cols="6">
                  <v-select v-model="formData.academic_year" :items="yearRange" label="Academic Year"
                    variant="outlined" density="compact" :rules="[rules.required]" />
                </v-col>

                <v-col cols="6">
                  <v-combobox v-model="formData.university_id" :items="StudentStatisticsRepository.universities"
                    item-title="name" item-value="id" label="University" variant="outlined" density="compact"
                    :rules="[rules.required]" :return-object="false" />
                </v-col>

                <!-- Row 2 -->
                <v-col cols="6">
                  <v-select v-model="formData.faculty_id" :items="StudentStatisticsRepository.faculties"
                    item-title="name" item-value="id" label="Faculty" variant="outlined" density="compact"
                    :rules="[rules.required]" />
                </v-col>

                <v-col cols="6">
                  <v-select v-model="formData.department_id" :items="filteredDepartments"
                    item-title="name" item-value="id" label="Department" variant="outlined" density="compact"
                    :rules="[rules.required]" :disabled="!formData.faculty_id" />
                </v-col>

                <!-- Row 3 -->
                <v-col cols="6">
                  <v-select v-model="formData.classroom" :items="classOptions" item-title="name" item-value="name"
                    label="Class" variant="outlined" density="compact" />
                </v-col>
                <v-col cols="6">
                  <v-select v-model="formData.semester_number" :items="availableSemesters" label="Semester"
                    variant="outlined" density="compact" :rules="[rules.required]" :disabled="!formData.classroom" />
                </v-col>
                <v-col cols="6">
                  <v-select v-model="formData.student_type" :items="['new', 'current', 'graduated']"
                    label="Student Type" variant="outlined" density="compact" :rules="[rules.required]" />
                </v-col>
                <v-col cols="3">
                  <v-select v-model="formData.shift" :items="['day', 'night']" label="Shift" variant="outlined"
                    density="compact" :rules="[rules.required]" />
                </v-col>
                <v-col cols="3">
                  <v-select v-model="formData.season" :items="['spring', 'autumn']" label="Season" variant="outlined"
                    density="compact" :rules="[rules.required]" />
                </v-col>

                <!-- Row 4: Total students -->
                <v-col cols="6">
                  <v-text-field v-model="formData.male_total" label="Total Male Students" type="number"
                    variant="outlined" density="compact" :rules="[rules.required]" />
                </v-col>
                <v-col cols="6">
                  <v-text-field v-model="formData.female_total" label="Total Female Students" type="number"
                    variant="outlined" density="compact" :rules="[rules.required]" />
                </v-col>
              </v-row>
            </v-form>
          </v-card-text>

          <div class="d-flex flex-row-reverse mb-6 mx-6">
            <v-btn color="primary" class="px-4" @click="save">
              {{ StudentStatisticsRepository.isEditMode ? "Update" : "Submit" }}
            </v-btn>
          </div>
        </v-card>
      </template>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch, computed } from "vue";
import { useStudentStatisticRepository } from "@/store/StudentStatisticRepository";
import persianDate from "persian-date";

const StudentStatisticsRepository = useStudentStatisticRepository();

const getCurrentPersianYear = () => new persianDate().year().toString();
const currentYear = ref(getCurrentPersianYear());

const yearRange = computed(() => {
  const years = [];
  const start = parseInt(currentYear.value) - 10;
  const end = parseInt(currentYear.value) + 10;
  for (let y = start; y <= end; y++) years.push(y.toString());
  return years;
});

const formRef = ref(null);

const formData = reactive({
  id: StudentStatisticsRepository.statistic.id,
  academic_year: StudentStatisticsRepository.statistic.academic_year || currentYear.value,
  university_id: StudentStatisticsRepository.statistic.university?.university_id || null,
  faculty_id: StudentStatisticsRepository.statistic.faculty?.faculty_id || null,
  department_id: StudentStatisticsRepository.statistic.department?.department_id || null,
  classroom: StudentStatisticsRepository.statistic.classroom || null,
  shift: StudentStatisticsRepository.statistic.shift || "day",
  season: StudentStatisticsRepository.statistic.season || "spring",
  semester_number: StudentStatisticsRepository.statistic.semester_number,
  male_total: StudentStatisticsRepository.statistic.male_total || 0,
  female_total: StudentStatisticsRepository.statistic.female_total || 0,
  student_type: StudentStatisticsRepository.statistic.student_type || "new",
});

// Filter departments based on selected faculty
const filteredDepartments = computed(() => {
  return StudentStatisticsRepository.departments.filter(
    dept => dept.faculty?.id === formData.faculty_id
  );
});

// Reset department when faculty changes
watch(() => formData.faculty_id, () => {
  formData.department_id = null;
});

// Watch classroom and update available semesters
const classOptions = [
  { id: 1, name: "Class 1", semesters: [1, 2] },
  { id: 2, name: "Class 2", semesters: [3, 4] },
  { id: 3, name: "Class 3", semesters: [5, 6] },
  { id: 4, name: "Class 4", semesters: [7, 8] },
];

const availableSemesters = ref([]);
watch(() => formData.classroom, (newVal) => {
  const selected = classOptions.find(c => c.name === newVal);
  availableSemesters.value = selected ? selected.semesters : [];
});

onMounted(() => {
  StudentStatisticsRepository.fetchDepartments();
  StudentStatisticsRepository.fetchFaculties();
  StudentStatisticsRepository.fetchUniversities();
});

const rules = {
  required: (v) => !!v || "This field is required",
};

const save = async () => {
  const isValid = await formRef.value.validate();
  if (!isValid) return;

  if (StudentStatisticsRepository.isEditMode) {
    await StudentStatisticsRepository.updateStatistic(formData, formData.id);
  } else {
    await StudentStatisticsRepository.createStatistic(formData);
  }
};
</script>

<style scoped>
.persian-year-picker {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  direction: rtl;
}
</style>
