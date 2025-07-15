<template>
    <CreateStudentStatistic v-if="StudentStatisticRepository.createDialog" />

    <div :dir="dir">
        <AppBar :pageTitle="t('student_statistic')" />

        <v-divider :thickness="1" class="border-opacity-100" />

        <!-- Advanced Filters -->
        <v-expansion-panels class="my-4">
            <v-expansion-panel>
                <v-expansion-panel-title>
                    <v-icon icon="mdi-filter" class="mr-2"></v-icon>
                    {{ t("advanced_filters") }}
                </v-expansion-panel-title>
                <v-expansion-panel-text>
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters.province
                                "
                                :items="provinces"
                                item-title="name"
                                item-value="id"
                                :label="t('province')"
                                clearable
                                @update:modelValue="
                                    StudentStatisticRepository.fetchUniversitiesByProvince
                                "
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .university_type
                                "
                                :items="universityTypes"
                                :label="t('university_type')"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
        v-model="StudentStatisticRepository.filters.university"
        :items="filteredUniversities"
        :label="t('university')"
        item-title="name"
        item-value="id"
        clearable
        @update:modelValue="StudentStatisticRepository.fetchFacultiesByUniversity"
    ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters.faculty
                                "
                                :items="filteredFaculties"
                                :label="t('faculty')"
                                item-title="name"
                                item-value="id"
                                clearable
                                @update:modelValue="
                                    StudentStatisticRepository.fetchDepartmentsByFaculty
                                "
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .department
                                "
                                :items="filteredDepartments"
                                :label="t('department')"
                                item-title="name"
                                item-value="id"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .academic_year
                                "
                                :items="academicYears"
                                :label="t('academic_year')"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .student_type
                                "
                                :items="studentTypes"
                                :label="t('student_type')"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3" class="d-flex align-end">
                            <v-btn
                                color="primary"
                                @click="applyFilters"
                                class="mb-4"
                            >
                                {{ t("apply_filters") }}
                            </v-btn>
                            <v-btn
                                color="error"
                                @click="resetFilters"
                                class="mb-4 ml-2"
                                variant="outlined"
                            >
                                {{ t("reset") }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-expansion-panel-text>
            </v-expansion-panel>
        </v-expansion-panels>

        <!-- Search & Create Button -->
        <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
            <div class="text-field w-25">
                <v-text-field
                    color="primary"
                    density="compact"
                    variant="outlined"
                    :label="t('search')"
                    append-inner-icon="mdi-magnify"
                    hide-details
                    v-model="StudentStatisticRepository.search"
                    @update:modelValue="
                        loadStatistics({
                            page: 1,
                            itemsPerPage:
                                StudentStatisticRepository.itemsPerPage,
                        })
                    "
                />
            </div>

            <v-btn
                @click="showCreateDialog"
                color="primary"
                variant="flat"
                class="px-6"
            >
                {{ t("create") }}
            </v-btn>
        </div>

        <!-- Data Table -->
        <v-data-table-server
            :dir="dir"
            v-model:items-per-page="StudentStatisticRepository.itemsPerPage"
            :headers="headers"
            :items-length="StudentStatisticRepository.totalItems"
            :items="StudentStatisticRepository.statistics"
            :loading="StudentStatisticRepository.loading"
            class="w-100 mx-auto"
            hover
            @update:options="loadStatistics"
        >
            <template #bottom>
                <div class="d-flex align-center justify-end pa-2">
                    <span class="mx-2">{{
                        t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="StudentStatisticRepository.itemsPerPage"
                        :items="[
                            { value: 5, text: '5' },
                            { value: 10, text: '10' },
                            { value: 25, text: '25' },
                            { value: 50, text: '50' },
                            { value: -1, text: t('pagination.all') },
                        ]"
                        item-title="text"
                        item-value="value"
                        density="compact"
                        variant="outlined"
                        hide-details
                        style="max-width: 100px"
                        @update:modelValue="
                            loadStatistics({
                                page: 1,
                                itemsPerPage:
                                    StudentStatisticRepository.itemsPerPage,
                            })
                        "
                    ></v-select>
                </div>
            </template>

            <!-- Action Slot -->
            <template v-slot:item.action="{ item }">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn
                            icon="mdi-dots-vertical"
                            v-bind="props"
                            variant="text"
                        />
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title
                                @click="edit(item)"
                                class="cursor-pointer d-flex gap-3 pb-3"
                            >
                                <v-icon color="tealColor"
                                    >mdi-square-edit-outline</v-icon
                                >
                                {{ t("Edit") }}
                            </v-list-item-title>
                            <v-list-item-title
                                @click="deleteItem(item)"
                                class="cursor-pointer d-flex gap-3"
                            >
                                <v-icon color="error"
                                    >mdi-delete-outline</v-icon
                                >
                                {{ t("Delete") }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
        </v-data-table-server>
    </div>
</template>
<script setup>
import { computed, onMounted } from "vue";
import AppBar from "@/components/AppBar.vue";
import CreateStudentStatistic from "./CreateStudentStatistic.vue";
import { useStudentStatisticRepository } from "../../store/StudentStatisticRepository";
import { useUniversityRepository } from "../../store/UniversityRepository";
import { useProvinceRepository } from "../../store/ProvinceRepository";
// import { useStudentTypeRepository } from "../../store/StudentTypeRepository";

const StudentStatisticRepository = useStudentStatisticRepository();
const UniversityRepository = useUniversityRepository();
const ProvinceRepository = useProvinceRepository();
// const StudentTypeRepository = useStudentTypeRepository();

import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();

const dir = computed(() => {
    return locale.value === "en" ? "ltr" : "rtl";
});

// Computed properties for filter options
const provinces = computed(() => ProvinceRepository.provinces);
const universityTypes = computed(() => UniversityRepository.universityTypes);
// const studentTypes = computed(() => StudentTypeRepository.studentTypes);

// Computed properties for filtered dropdowns
const filteredUniversities = computed(() => {
    return UniversityRepository.universities.filter((uni) => {
        const provinceMatch =
            !StudentStatisticRepository.filters.province ||
            uni.province_id === StudentStatisticRepository.filters.province;
        const typeMatch =
            !StudentStatisticRepository.filters.university_type ||
            uni.type === StudentStatisticRepository.filters.university_type;
        return provinceMatch && typeMatch;
    });
});

const filteredFaculties = computed(() => {
    if (!StudentStatisticRepository.filters.university) {
        return StudentStatisticRepository.faculties;
    }
    return StudentStatisticRepository.faculties.filter(
        (faculty) =>
            faculty.university_id ===
            StudentStatisticRepository.filters.university
    );
});

const filteredDepartments = computed(() => {
    if (!StudentStatisticRepository.filters.faculty) {
        return StudentStatisticRepository.departments;
    }
    return StudentStatisticRepository.departments.filter(
        (dept) => dept.faculty_id === StudentStatisticRepository.filters.faculty
    );
});

// Methods
const showCreateDialog = () => {
    StudentStatisticRepository.statistic = {};
    StudentStatisticRepository.isEditMode = false;
    StudentStatisticRepository.createDialog = true;
};

const edit = (item) => {
    StudentStatisticRepository.isEditMode = true;
    StudentStatisticRepository.statistic = {};
    StudentStatisticRepository.fetchStatistic(item.id)
        .then(() => {
            StudentStatisticRepository.createDialog = true;
        })
        .catch((error) => {
            console.error("Error fetching statistic:", error);
        });
};

const deleteItem = async (item) => {
    await StudentStatisticRepository.deleteStatistic(item.id);
};

const applyFilters = () => {
    loadStatistics({
        page: 1,
        itemsPerPage: StudentStatisticRepository.itemsPerPage,
    });
};

const resetFilters = () => {
    StudentStatisticRepository.resetFilters();
    loadStatistics({
        page: 1,
        itemsPerPage: StudentStatisticRepository.itemsPerPage,
    });
};

const loadStatistics = (options) => {
    StudentStatisticRepository.fetchStatistics(options);
};

// Fetch initial data
onMounted(() => {
    StudentStatisticRepository.fetchUniversities();
    ProvinceRepository.FetchProvinces({page: 1, itemsPerPage: 10 });
    // StudentTypeRepository.fetchStudentTypes();
    StudentStatisticRepository.fetchFaculties();
    StudentStatisticRepository.fetchDepartments();
    loadStatistics({
        page: 1,
        itemsPerPage: StudentStatisticRepository.itemsPerPage,
    });
});

const headers = computed(() => [
    {
        title: t("Academic Year"),
        key: "academic_year",
        align: "start",
        sortable: false,
    },
    { title: t("University"), key: "university.name" },
    { title: t("Faculty"), key: "faculty.name" },
    { title: t("Department"), key: "department.name" },
    { title: t("Classroom"), key: "classroom" },
    { title: t("Shift"), key: "shift" },
    { title: t("Season"), key: "season" },
    { title: t("Semester"), key: "semester_number" },
    { title: t("Male"), key: "male_total" },
    { title: t("Female"), key: "female_total" },
    { title: t("Type"), key: "student_type" },
    { title: t("Action"), key: "action", align: "end", sortable: false },
]);
</script>
