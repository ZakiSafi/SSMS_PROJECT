<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="45rem"
            v-model="StudentStatisticsRepository.createDialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                StudentStatisticsRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("create")
                            }}
                        </h2>
                        <v-btn variant="text" @click="isActive.value = false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>

                    <v-divider class="border-opacity-100 mx-6"></v-divider>

                    <v-card-text>
                        <v-form
                            ref="formRef"
                            class="pt-4"
                            v-model="formIsValid"
                        >
                            <v-row dense>
                                <!-- Row 1 -->
                                <v-col cols="6">
                                    <DatePicker
                                        v-model="formData.academic_year"
                                        format="jYYYY"
                                        type="year"
                                        :placeholder="$t('form.select_year')"
                                        rounded
                                        :rules="[rules.required]"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <v-select
                                        v-model="formData.university_id"
                                        :items="
                                            StudentStatisticsRepository.universities
                                        "
                                        item-title="name"
                                        item-value="id"
                                        :label="$t('University')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :return-object="false"
                                        clearable
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <!-- Row 2 -->
                                <v-select
                                    v-model="formData.faculty_id"
                                    :items="facultiesOption"
                                    item-title="name"
                                    item-value="id"
                                    :label="$t('Faculty')"
                                    variant="outlined"
                                    density="compact"
                                    :rules="[rules.required]"
                                    :disabled="!formData.university_id"
                                    clearable
                                    :no-data-text="t('no_data_available')"
                                />

                                <v-col cols="6">
                                    <v-select
                                        v-model="formData.department_id"
                                        :items="departmentsOption"
                                        item-title="name"
                                        item-value="id"
                                        :label="$t('Department')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :disabled="!formData.faculty_id"
                                        clearable
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <!-- Row 3 -->
                                <v-col cols="6">
                                    <v-select
                                        v-model="formData.classroom"
                                        :items="classOptions"
                                        item-title="name"
                                        item-value="name"
                                        :label="$t('Class')"
                                        variant="outlined"
                                        density="compact"
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <v-select
                                        v-model="formData.semester_number"
                                        :items="availableSemesters"
                                        :label="$t('Semester')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :disabled="!formData.classroom"
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <v-select
                                        v-model="formData.student_type"
                                        :items="[
                                            {
                                                value: 'new',
                                                title: $t('New'),
                                            },
                                            {
                                                value: 'current',
                                                title: $t('Current'),
                                            },
                                            {
                                                value: 'graduated',
                                                title: $t('Graduated'),
                                            },
                                        ]"
                                        item-title="title"
                                        item-value="value"
                                        :label="$t('Student Type')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <v-col cols="3">
                                    <v-select
                                        v-model="formData.shift"
                                        :items="[
                                            { value: 'day', title: $t('day') },
                                            {
                                                value: 'night',
                                                title: $t('night'),
                                            },
                                        ]"
                                        :item-title="title"
                                        :item-value="value"
                                        :label="$t('Shift')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <v-col cols="3">
                                    <v-select
                                        v-model="formData.season"
                                        :items="['spring', 'autumn']"
                                        :item-title="(item) => $t(`${item}`)"
                                        :label="$t('Season')"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[rules.required]"
                                        :no-data-text="t('no_data_available')"
                                    />
                                </v-col>

                                <!-- Row 4: Total students -->
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="formData.male_total"
                                        :label="$t('Total Male Students')"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[
                                            rules.required,
                                            rules.positiveNumber,
                                        ]"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <v-text-field
                                        v-model="formData.female_total"
                                        :label="$t('Total Female Students')"
                                        type="number"
                                        variant="outlined"
                                        density="compact"
                                        :rules="[
                                            rules.required,
                                            rules.positiveNumber,
                                        ]"
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn color="primary" class="px-4" @click="save">
                            {{
                                StudentStatisticsRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("form.submit")
                            }}
                        </v-btn>
                    </div>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { useStudentStatisticRepository } from "@/store/StudentStatisticRepository";
import persianDate from "persian-date";
import DatePicker from "vue3-persian-datetime-picker";
import { useI18n } from "vue-i18n";
import { computed } from "vue";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" ? "rtl" : "ltr"));

const StudentStatisticsRepository = useStudentStatisticRepository();
const currentYear = ref(new persianDate().year().toString());
const formRef = ref(null);
const formIsValid = ref(false);

const formData = reactive({
    id: StudentStatisticsRepository.statistic.id,
    academic_year:
        StudentStatisticsRepository.statistic.academic_year?.toString() ||
        currentYear.value,
    university_id:
        StudentStatisticsRepository.statistic.university?.university_id || null,
    faculty_id:
        StudentStatisticsRepository.statistic.faculty?.faculty_id || null,
    department_id:
        StudentStatisticsRepository.statistic.department?.department_id || null,
    classroom: StudentStatisticsRepository.statistic.classroom || null,
    shift: StudentStatisticsRepository.statistic.shift || "day",
    season: StudentStatisticsRepository.statistic.season || "spring",
    semester_number: StudentStatisticsRepository.statistic.semester_number,
    male_total: StudentStatisticsRepository.statistic.male_total || 0,
    female_total: StudentStatisticsRepository.statistic.female_total || 0,
    student_type: StudentStatisticsRepository.statistic.student_type || "new",
});

// Handle faculty change
const handleFacultyChange = (facultyId) => {
    formData.department_id = null;
    StudentStatisticsRepository.fetchFormDepartmentsByFaculty(facultyId);
};

// Reset form when dialog closes
const resetForm = () => {
    StudentStatisticsRepository.resetFormDependencies();
};

const classOptions = [
    { id: 1, name: t("class1"), semesters: [1, 2] },
    { id: 2, name: t("class2"), semesters: [3, 4] },
    { id: 3, name: t("class3"), semesters: [5, 6] },
    { id: 4, name: t("class4"), semesters: [7, 8] },
    { id: 5, name: t("class5"), semesters: [7, 8] },
    { id: 6, name: t("class6"), semesters: [7, 8] },
    { id: 7, name: t(""), semesters: [7, 8] },
];

const availableSemesters = ref([]);
watch(
    () => formData.classroom,
    (newVal) => {
        const selected = classOptions.find((c) => c.name === newVal);
        availableSemesters.value = selected ? selected.semesters : [];
    }
);
const facultiesOption = ref([]);
watch(
    () => formData.university_id,
    (newVal) => {
        if (!newVal) {
            facultiesOption.value = [];
            return;
        }

        console.log(StudentStatisticsRepository.faculties);

        facultiesOption.value = StudentStatisticsRepository.faculties.filter(
            (faculty) => faculty.universities.some((uni) => uni.id === newVal)
        );

        console.log("Faculties updated:", facultiesOption.value);
    }
);
const departmentsOption = ref([]);
watch(
    () => formData.faculty_id,
    (newVal) => {
        if (!newVal) {
            departmentsOption.value = [];
            return;
        }

        departmentsOption.value =
            StudentStatisticsRepository.departments.filter(
                (department) => department.faculty.id === newVal
            );
    }
);

onMounted(() => {
    StudentStatisticsRepository.fetchDepartments();
    StudentStatisticsRepository.fetchFaculties();
    StudentStatisticsRepository.fetchUniversities();
});

const rules = {
    required: (v) => !!v || t("validation.required"),
    positiveNumber: (v) => v >= 0 || t("validation.positive_number"),
};

const save = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    if (StudentStatisticsRepository.isEditMode) {
        await StudentStatisticsRepository.updateStatistic(
            formData,
            formData.id
        );
    } else {
        await StudentStatisticsRepository.createStatistic(formData);
    }
};
</script>

<style scoped>
.persian-year-picker {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    direction: rtl;
}
</style>
