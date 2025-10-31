<template>
    <div :dir="dir" class="rtl-datepicker-container">
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
                            @submit.prevent="save"
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
                                    <RTLSelect
                                        v-model="formData.university_id"
                                        :items="StudentStatisticsRepository.universities"
                                        item-title="name"
                                        item-value="id"
                                        :placeholder="$t('University')"
                                        select-class="compact"
                                        :required="true"
                                    />
                                </v-col>

                                <!-- Row 2 -->
                                <v-col cols="6">
                                    <RTLSelect
                                        v-model="formData.faculty_id"
                                        :items="facultiesOption"
                                        item-title="name"
                                        item-value="id"
                                        :placeholder="$t('Faculty')"
                                        select-class="compact"
                                        :required="true"
                                        :disabled="!formData.university_id"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <RTLSelect
                                        v-model="formData.department_id"
                                        :items="departmentsOption"
                                        item-title="name"
                                        item-value="id"
                                        :placeholder="$t('Department')"
                                        select-class="compact"
                                        :required="true"
                                        :disabled="!formData.faculty_id"
                                    />
                                </v-col>

                                <!-- Row 3 -->
                                <v-col cols="6">
                                    <RTLSelect
                                        v-model="formData.classroom"
                                        :items="classOptions"
                                        item-title="name"
                                        item-value="name"
                                        :placeholder="$t('Class')"
                                        select-class="compact"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <RTLSelect
                                        v-model="formData.semester_number"
                                        :items="availableSemesters.map(sem => ({ value: sem, title: sem.toString() }))"
                                        item-title="title"
                                        item-value="value"
                                        :placeholder="$t('Semester')"
                                        select-class="compact"
                                        :required="true"
                                        :disabled="!formData.classroom"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <RTLSelect
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
                                        :placeholder="$t('Student Type')"
                                        select-class="compact"
                                        :required="true"
                                    />
                                </v-col>

                                <v-col cols="3">
                                    <RTLSelect
                                        v-model="formData.shift"
                                        :items="[
                                            { value: 'day', title: $t('day') },
                                            {
                                                value: 'night',
                                                title: $t('night'),
                                            },
                                        ]"
                                        item-title="title"
                                        item-value="value"
                                        :placeholder="$t('Shift')"
                                        select-class="compact"
                                        :required="true"
                                    />
                                </v-col>

                                <v-col cols="3">
                                    <RTLSelect
                                        v-model="formData.season"
                                        :items="['spring', 'autumn'].map(item => ({ value: item, title: $t(item) }))"
                                        item-title="title"
                                        item-value="value"
                                        :placeholder="$t('Season')"
                                        select-class="compact"
                                        :required="true"
                                    />
                                </v-col>

                                <!-- Row 4: Total students -->
                                <v-col cols="6">
                                    <RTLInput
                                        v-model="formData.male_total"
                                        :placeholder="$t('Total Male Students')"
                                        type="number"
                                        input-class="compact"
                                        :required="true"
                                    />
                                </v-col>

                                <v-col cols="6">
                                    <RTLInput
                                        v-model="formData.female_total"
                                        :placeholder="$t('Total Female Students')"
                                        type="number"
                                        input-class="compact"
                                        :required="true"
                                    />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn
                            type="submit"
                            color="primary"
                            class="px-4"
                            @click="save"
                        >
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
import { ref, reactive, onMounted, watch, nextTick } from "vue";
import { useStudentStatisticRepository } from "@/store/StudentStatisticRepository";
import persianDate from "persian-date";
import DatePicker from "vue3-persian-datetime-picker";
import { useI18n } from "vue-i18n";
import { computed } from "vue";
import RTLInput from "@/components/RTLInput.vue";
import RTLSelect from "@/components/RTLSelect.vue";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" || locale.value === "pa" ? "rtl" : "ltr"));

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

// Force DatePicker RTL alignment
const applyDatePickerRTL = () => {
    if (locale.value === "fa" || locale.value === "pa") {
        nextTick(() => {
            const datePickerInputs = document.querySelectorAll('[dir="rtl"] .vdp-datepicker input, [dir="rtl"] .vue3-datepicker input, [dir="rtl"] [class*="datepicker"] input');
            datePickerInputs.forEach((input) => {
                input.style.setProperty('text-align', 'right', 'important');
                input.style.setProperty('direction', 'rtl', 'important');
            });
            
            // Also target by type attribute
            const allInputs = document.querySelectorAll('[dir="rtl"] input[type="text"]');
            allInputs.forEach((input) => {
                if (input.closest('.vdp-datepicker') || input.closest('.vue3-datepicker')) {
                    input.style.setProperty('text-align', 'right', 'important');
                    input.style.setProperty('direction', 'rtl', 'important');
                }
            });
        });
    }
};

onMounted(() => {
    StudentStatisticsRepository.fetchDepartments();
    StudentStatisticsRepository.fetchFaculties();
    StudentStatisticsRepository.fetchUniversities();
    applyDatePickerRTL();
    setTimeout(applyDatePickerRTL, 100);
    setTimeout(applyDatePickerRTL, 300);
    setTimeout(applyDatePickerRTL, 500);
});

watch(locale, () => {
    applyDatePickerRTL();
});

watch(() => StudentStatisticsRepository.createDialog, (isOpen) => {
    if (isOpen) {
        setTimeout(applyDatePickerRTL, 100);
        setTimeout(applyDatePickerRTL, 300);
        setTimeout(applyDatePickerRTL, 500);
    }
});

const rules = {
    required: (v) => !!v || t("validation.required"),
    positiveNumber: (v) => v >= 0 || t("validation.positive_number"),
};

const save = async () => {
    // Manual validation since we're using custom components
    if (!formData.academic_year || !formData.university_id || !formData.faculty_id || 
        !formData.department_id || !formData.semester_number || !formData.student_type || 
        !formData.shift || !formData.season || 
        (formData.male_total === null || formData.male_total === '') ||
        (formData.female_total === null || formData.female_total === '')) {
        return;
    }

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
