import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";
import persianDate from "persian-date";

export const useReportRepository = defineStore("reportRepository", {
    state() {
        return {
            universities: reactive([]),
            allUniversities: reactive([]),
            universityBaseGraduation: reactive([]),
            facultyBaseGraduation: reactive([]),
            departmentBaseGraduation: reactive([]),
            studentTeacher: reactive([]),
            universityClasses: reactive([]),
            jawad: reactive([]),
            fawad: reactive([]),
            search: ref(""),
            university: ref(""),
            date: ref(new persianDate().year().toString()),
            season: ref("spring"),
            shift: ref("day"),
            type: ref("public"),
            loading: ref(false),
            totalItems: ref(0),
            selectedItems: ref([]),
            itemsPerPage: ref(10),
        };
    },
    actions: {
        async fetchUniversity({ page, itemsPerPage }, date = this.date) {
            this.loading = true;

            try {
                const response = await axios.get(
                    `report/university?year=${date}&type=${this.type}&shift=${this.shift}&page=${page}&perPage=${itemsPerPage}`
                );
                this.universities = response.data.data;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error("Error fetching data:", error);
                this.universities = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchUniversityBaseGraduation(
            { page, itemsPerPage },
            date = this.date,
            season = this.season
        ) {
            this.loading = true;

            try {
                const response = await axios.get(
                    `report/universityBaseGraduation?year=${date}&season=${season}&page=${page}&perPage=${itemsPerPage}`
                );
                this.universityBaseGraduation = response.data.data;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error("Error fetching data:", error);
                this.universityBaseGraduation = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchStudentTeacherRatio(
            { page, itemsPerPage },
            date = this.date
        ) {
            this.loading = true;

            try {
                const response = await axios.get(
                    `report/studentTeacherRatio?year=${date}&page=${page}&perPage=${itemsPerPage}`
                );
                this.studentTeacher = response.data.data;
                this.totalItems = response.data.total;
            } catch (error) {
                console.error("Error fetching data:", error);
                this.studentTeacher = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchUniversityClasses(
            { page, itemsPerPage },
            date = this.date,
            shift = this.shift
        ) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `report/universitiesClasses?year=${date}&shift=${shift}&page=${page}&perPage=${itemsPerPage}`
                );
                this.universityClasses = response.data.data;
                this.totalItems = response.data.total;
            } catch {
                console.error("Error fetching data:", error);
                this.universityClasses = [];
            } finally {
                this.loading = false;
            }
        },

        async fecthJawad(
            { page, itemsPerPage },
            date = this.date,
            season = this.season,
            universityId,
            shift = this.shift
        ) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `report/facultyClassBased?year=${date}&season=${season}&shift=${shift}&university=${universityId}&page=${page}&perPage=${itemsPerPage}`
                );
                this.jawad = response.data.data;
                this.totalItems = response.data.total;
            } catch {
                console.error("Error fetching data:", error);
                this.jawad = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchFawad(
            { page, itemsPerPage },
            date = this.date,
            season = this.season,
            universityId,
            shift = this.shift
        ) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `report/departmentClassBase?year=${date}&season=${season}&shift=${shift}&university=${universityId}&page=${page}&perPage=${itemsPerPage}`
                );
                this.fawad = response.data.data;
                this.totalItems = response.data.total;
            } catch {
                console.error(
                    "Error fetching data sdgdvfbgfvdsdfbfvdcsaxscdvfdcscdfvdc:",
                    error
                );
                this.jawad = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchFacultyBaseGraduation(
            { page, itemsPerPage },
            date = this.date,
            season = this.season,
            shift = this.shift
        ) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `report/facultyBasedGraduation?year=${date}&season=${season}&shift=${shift}&page=${page}&perPage=${itemsPerPage}`
                );
                this.facultyBaseGraduation = response.data.data;
                this.totalItems = response.data.total;
            } catch {
                console.error(
                    "Error fetching data sdgdvfbgfvdsdfbfvdcsaxscdvfdcscdfvdc:",
                    error
                );
                this.facultyBaseGraduation = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchDepartmentBaseGraduation(
            { page, itemsPerPage },
            date = this.date,
            season = this.season,
            shift = this.shift
        ) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `report/departmentBasedGraduation?year=${date}&season=${season}&shift=${shift}&page=${page}&perPage=${itemsPerPage}`
                );
                this.departmentBaseGraduation = response.data.data;
                this.totalItems = response.data.total;
            } catch {
                console.error(
                    "Error fetching data sdgdvfbgfvdsdfbfvdcsaxscdvfdcscdfvdc:",
                    error
                );
                this.departmentBaseGraduation = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchUniversities() {
            try {
                const response = await axios.get(`universities`);
                this.allUniversities = response.data.data;
            } catch (error) {
                console.error("Failed to fetch universities:", error);
            }
        },
    },
});
