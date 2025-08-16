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
                                :items="StudentStatisticRepository.provinces"
                                item-title="name"
                                item-value="id"
                                :label="t('province')"
                                clearable
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .university_type
                                "
                                :items="['private', 'public']"
                                :label="t('university_type')"
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters
                                        .university
                                "
                                :items="UniversitiesOption"
                                :label="t('university')"
                                item-title="name"
                                item-value="id"
                                clearable
                               
                            ></v-select>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select
                                v-model="
                                    StudentStatisticRepository.filters.faculty
                                "
                                :items="facultiesOption"
                                :label="t('faculty')"
                                item-title="name"
                                item-value="id"
                                clearable
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
            :headers="dynamicHeaders"
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
import { computed, onMounted, watch ,ref} from "vue";
import AppBar from "@/components/AppBar.vue";
import CreateStudentStatistic from "./CreateStudentStatistic.vue";
import { useStudentStatisticRepository } from "../../store/StudentStatisticRepository";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const StudentStatisticRepository = useStudentStatisticRepository();

const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

// ---------------- Dynamic Headers ----------------
const dynamicHeaders = computed(() => {
    const baseHeaders = [
        { title: t("Academic Year"), key: "academic_year", align: "start" },
        { title: t("University"), key: "university.name" },
        { title: t("Faculty"), key: "faculty.name" },
        { title: t("Department"), key: "department.name" },
    ];

    if (StudentStatisticRepository.filters.student_type) {
        baseHeaders.push({
            title: t("Students Count"),
            key: "student_count",
            value: (item) => {
                switch (StudentStatisticRepository.filters.student_type) {
                    case "current":
                        return item.current_count;
                    case "graduated":
                        return item.graduated_count;
                    case "new":
                        return item.new_count;
                    default:
                        return item.male_total + item.female_total;
                }
            },
        });
    } else {
        baseHeaders.push(
            { title: t("Male"), key: "male_total" },
            { title: t("Female"), key: "female_total" },
            { title: t("Total"), key: "total_students" }
        );
    }

    baseHeaders.push(
        { title: t("Classroom"), key: "classroom" },
        { title: t("Shift"), key: "shift" },
        { title: t("Action"), key: "action", align: "end", sortable: false }
    );

    return baseHeaders;
});

// ---------------- Dropdown Options ----------------
const UniversitiesOption = ref([]);
const facultiesOption = ref([]);
const filteredDepartments = ref([]);

// Watch province → fetch universities
watch(
  () => StudentStatisticRepository.filters.province,
  async (newVal) => {
    if (!newVal) {
      UniversitiesOption.value = [];
      return;
    }

    await StudentStatisticRepository.fetchUniversitiesByProvince(newVal);
    UniversitiesOption.value = StudentStatisticRepository.universities;
  }
);

// Watch university → fetch faculties
watch(
  () => StudentStatisticRepository.filters.university,
  async (newVal) => {
    if (!newVal) {
      facultiesOption.value = [];
      return;
    }

    await StudentStatisticRepository.fetchFacultiesByUniversity(newVal);
    facultiesOption.value = StudentStatisticRepository.faculties;
  }
);

// Watch faculty → fetch departments
watch(
  () => StudentStatisticRepository.filters.faculty,
  async (newVal) => {
    if (!newVal) {
      filteredDepartments.value = [];
      return;
    }

    await StudentStatisticRepository.fetchDepartmentsByFaculty(newVal);
    filteredDepartments.value = StudentStatisticRepository.departments;
  }
);

// ---------------- Academic years & Student Types ----------------
const academicYears = computed(() => {
    const currentYear = new Date().getFullYear();
    return Array.from({ length: 10 }, (_, i) => (currentYear - i).toString());
});

const studentTypes = computed(() => [
    { title: t("Current"), value: "current" },
    { title: t("Graduated"), value: "graduated" },
    { title: t("New"), value: "new" },
]);

// ---------------- Methods ----------------
const showCreateDialog = () => {
    StudentStatisticRepository.statistic = {};
    StudentStatisticRepository.isEditMode = false;
    StudentStatisticRepository.createDialog = true;
};

const edit = async (item) => {
    try {
        StudentStatisticRepository.isEditMode = true;
        await StudentStatisticRepository.fetchStatistic(item.id);
        StudentStatisticRepository.createDialog = true;
    } catch (error) {
        console.error("Error fetching statistic:", error);
        toast.error(t("error_fetching_statistic"));
    }
};

const deleteItem = async (item) => {
    try {
        await StudentStatisticRepository.deleteStatistic(item.id);
        toast.success(t("statistic_deleted"));
        loadStatistics({
            page: 1,
            itemsPerPage: StudentStatisticRepository.itemsPerPage,
        });
    } catch (error) {
        console.error("Error deleting statistic:", error);
        toast.error(t("error_deleting_statistic"));
    }
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

// ---------------- Lifecycle ----------------
onMounted(() => {
    StudentStatisticRepository.fetchProvinces();
});
</script>


