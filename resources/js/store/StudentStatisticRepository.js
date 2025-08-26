import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";

export const useStudentStatisticRepository = defineStore(
    "StudentStatisticRepository",
    {
        state: () => ({
            isEditMode: ref(false),
            search: ref(""),
            statistics: reactive([]),
            provinces: reactive([]),
            statistic: reactive({}),
            departments: reactive([]),
            universities: reactive([]),
            faculties: reactive([]),
            itemsPerPage: ref(10),
            totalItems: ref(0),
            loading: ref(false),
            createDialog: ref(false),
            filterOptions: reactive({}),

            studentTypes: reactive([
                { text: "Current", value: "current" },
                { text: "Graduated", value: "graduated" },
                { text: "New", value: "new" },
            ]),

            filters: reactive({
                province: null,
                university_type: null,
                university: null,
                faculty: null,
                department: null,
                academic_year: null,
                student_type: null,
            }),
        }),

        actions: {
            async fetchStatistics({ page, itemsPerPage }) {
                this.loading = true;
                try {
                    const params = {
                        page,
                        perPage: itemsPerPage,
                        search: this.search,
                        ...this.filters,
                    };

                    // Remove empty filters
                    Object.keys(params).forEach((key) => {
                        if (
                            params[key] === null ||
                            params[key] === undefined ||
                            params[key] === ""
                        ) {
                            delete params[key];
                        }
                    });

                    const response = await axios.get("studentStatistics", {
                        params,
                    });
                    this.statistics = response.data.data;
                    this.totalItems = response.data.meta.total;
                    this.filterOptions = response.data.filter_options || {};

                    // Update dropdown options based on active filters
                    if (this.filterOptions.universities) {
                        this.universities = this.filterOptions.universities;
                    }
                    if (this.filterOptions.faculties) {
                        this.faculties = this.filterOptions.faculties;
                    }
                    if (this.filterOptions.departments) {
                        this.departments = this.filterOptions.departments;
                    }
                } catch (error) {
                    console.error("Failed to fetch statistics:", error);
                    throw error;
                } finally {
                    this.loading = false;
                }
            },

            async fetchUniversitiesByProvince(provinceId) {
                try {
                    const response = await axios.get("universities");
                    this.universities = response.data.data.filter(
                        (uni) => uni.province.id === provinceId
                    );
                    console.log(this.universities);
                } catch (error) {
                    console.error("Error fetching universities:", error);
                    throw error;
                }
            },
            async fetchFacultiesByUniversity(universityId) {
                try {
                    const params = universityId
                        ? { university_id: universityId }
                        : {};
                    const response = await axios.get("faculties", { params });
                    this.faculties = response.data.data.filter((faculty) =>
                        faculty.universities.some(
                            (uni) => uni.id === universityId
                        )
                    );
                } catch (error) {
                    console.error("Error fetching faculties:", error);
                    throw error;
                }
            },

            async fetchDepartmentsByFaculty(facultyId) {
                try {
                    const params = facultyId ? { faculty_id: facultyId } : {};
                    const response = await axios.get("departments", { params });
                    this.departments = response.data.data.filter(
                        (dep) => dep.faculty.id === facultyId
                    );
                } catch (error) {
                    console.error("Error fetching departments:", error);
                    throw error;
                }
            },

            async fetchUniversities() {
                try {
                    const response = await axios.get("universities");
                    this.universities = response.data.data.filter(
                        (item) =>
                            Array.isArray(item) === false || item.length > 0
                    );
                } catch (error) {
                    console.error("Failed to fetch universities:", error);
                    throw error;
                }
            },

            async fetchFaculties() {
                try {
                    const response = await axios.get("faculties");
                    this.faculties = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch faculties:", error);
                    throw error;
                }
            },

            async fetchDepartments() {
                try {
                    const response = await axios.get("departments");
                    this.departments = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch departments:", error);
                    throw error;
                }
            },

            async fetchProvinces() {
                try {
                    const response = await axios.get("provinces");
                    this.provinces = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch departments:", error);
                    throw error;
                }
            },

            async fetchStatistic(id) {
                try {
                    const response = await axios.get(`studentStatistics/${id}`);
                    this.statistic = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch student statistic:", error);
                    throw error;
                }
            },

            async createStatistic(data) {
                try {
                    await axios.post("studentStatistics", data);
                    this.createDialog = false;
                    await this.fetchStatistics({
                        page: 1,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (error) {
                    console.error("Failed to create statistic:", error);
                    throw error;
                }
            },

            async updateStatistic(data, id) {
                try {
                    await axios.put(`studentStatistics/${id}`, data);
                    this.createDialog = false;
                    await this.fetchStatistics({
                        page: 1,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (error) {
                    console.error("Failed to update statistic:", error);
                    throw error;
                }
            },

            async deleteStatistic(id) {
                try {
                    await axios.delete(`studentStatistics/${id}`);
                    await this.fetchStatistics({
                        page: 1,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (error) {
                    console.error("Failed to delete statistic:", error);
                    throw error;
                }
            },

            resetFilters() {
                this.filters = {
                    province: null,
                    university_type: null,
                    university: null,
                    faculty: null,
                    department: null,
                    academic_year: null,
                    student_type: null,
                };
            },

            resetFormDependencies() {
                this.faculties = [];
                this.departments = [];
                if (this.statistic) {
                    this.statistic.faculty_id = null;
                    this.statistic.department_id = null;
                }
            },

            getStudentTypeText(value) {
                const type = this.studentTypes.find((t) => t.value === value);
                return type ? type.text : "";
            },
        },

        getters: {
            translatedStudentTypes() {
                return this.studentTypes.map((type) => ({
                    ...type,
                    text: this.$i18n?.t(type.text) || type.text,
                }));
            },
        },
    }
);
