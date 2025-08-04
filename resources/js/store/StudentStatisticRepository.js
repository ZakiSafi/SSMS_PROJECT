import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";

export const useStudentStatisticRepository = defineStore(
    "StudentStatisticRepository",
    {
        state() {
            return {
                isEditMode: ref(false),
                search: ref(""),
                statistics: reactive([]),
                statistic: reactive({}),
                departments: reactive([]),
                universities: reactive([]),
                faculties: reactive([]),
                itemsPerPage: ref(5),
                totalItems: ref(0),
                loading: ref(false),
                createDialog: ref(false),

                // Add filters state
                filters: reactive({
                    province: null,
                    university_type: null,
                    university: null,
                    faculty: null,
                    department: null,
                    academic_year: null,
                    student_type: null,
                }),
            };
        },

        actions: {
            async fetchStatistics({ page, itemsPerPage }) {
                this.loading = true;
                try {
                    // Build query params from filters
                    const params = {
                        page,
                        perPage: itemsPerPage,
                        search: this.search,
                        ...this.filters,
                    };

                    // Remove null/undefined filters
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
                } catch (error) {
                    console.error("Failed to fetch student statistics:", error);
                } finally {
                    this.loading = false;
                }
            },

            async fetchStatistic(id) {
                try {
                    const response = await axios.get(`studentStatistics/${id}`);
                    this.statistic = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch student statistic:", error);
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
                }
            },

            async fetchDepartments() {
                try {
                    const response = await axios.get(`departments`);
                    this.departments = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch departments:", error);
                }
            },

            async fetchFaculties() {
                try {
                    const response = await axios.get(`faculties`);
                    this.faculties = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch faculties:", error);
                }
            },

            async fetchUniversities() {
                try {
                    const response = await axios.get(`universities`);
                    const allUniversities = response.data.data;
                    this.universities = allUniversities.filter(
                        (item) =>
                            Array.isArray(item) === false || item.length > 0
                    );
                } catch (error) {
                    console.error("Failed to fetch universities:", error);
                }
            },

            // Add reset filters method
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

            // Add methods to fetch filtered data
            async fetchUniversitiesByProvince(province) {
                try {
                    const params = province ? { province } : {};
                    const response = await axios.get("universities", {
                        params,
                    });
                    this.universities = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch universities:", error);
                }
            },

            async fetchFacultiesByUniversity(universityId) {
                try {
                    const params = universityId
                        ? { university_id: universityId }
                        : {};
                    const response = await axios.get("faculties", { params });
                    this.faculties = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch faculties:", error);
                }
            },

            async fetchDepartmentsByFaculty(facultyId) {
                try {
                    const params = facultyId ? { faculty_id: facultyId } : {};
                    const response = await axios.get("departments", { params });
                    this.departments = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch departments:", error);
                }
            },

            async fetchFormFacultiesByUniversity(universityId) {
                try {
                    this.faculties = [];
                    if (!universityId) return;

                    const response = await axios.get("faculties", {
                        params: { university_id: universityId },
                    });
                    this.faculties = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch faculties:", error);
                }
            },

            async fetchFormDepartmentsByFaculty(facultyId) {
                try {
                    this.departments = [];
                    if (!facultyId) return;

                    const response = await axios.get("departments", {
                        params: { faculty_id: facultyId },
                    });
                    this.departments = response.data.data;
                } catch (error) {
                    console.error("Failed to fetch departments:", error);
                }
            },

            resetFormDependencies() {
                this.faculties = [];
                this.departments = [];
                if (this.statistic) {
                    this.statistic.faculty_id = null;
                    this.statistic.department_id = null;
                }
            },
        },
    }
);
