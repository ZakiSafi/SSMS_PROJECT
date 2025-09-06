import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";

export const useDashboardRepository = defineStore("DashboardRepository", {
    state: () => ({
        // Dashboard data
        dashboards: reactive([]),
        totalExpenses: 0,
        expenses: reactive([]),
        universities: reactive([]),
        provinces: reactive([]),

        // UI state
        dialog: false,
        isLoading: false,
        error: null,
        loading: true,
        itemsPerPage: 10,
        page: 1,
        showSelect: true,
        totalItems: 0,
        itemKey: "id",
        visaId: reactive([]),
        symbol: ref(null),

        // Search
        search: "",

        summaryData: reactive({
            total_students: 0,
            new_students: 0,
            graduated_students: 0,
            universities_count: {
                public: 0,
                private: 0,
                total: 0,
            },
            student_teacher_ratio: 0,
        }),

        facultyBreakdown: reactive([]),
        trends: reactive([]),
        recentActivity: reactive([]),
        genderDistribution: reactive({ data: [], percentages: {} }),

        // API-specific data
        earnings: 0,
        todayExpenses: [],
        thisMonthExpenses: [],
        thisYearExpenses: [],
        expensesList: [],
        dashboardReport: reactive({
            thisMonthProfit: 0,
            lastMonthProfit: 0,
            todayEarning: 0,
            totalTodayExpense: 0,
            newPatients: 0,
            totalPatients: 0,
            totalEarnings: 0,
            totalAllExpenses: 0,
            netProfit: 0,
            dailyExpenses: [],
            monthlyExpenses: [],
            yearlyExpenses: [],
            upcomingAppointments: [],
            monthExpenses: [],
            monthIncomes: [],
            monthProfits: [],
        }),
        monthExpenses: reactive([]),
        monthIncomes: reactive([]),
        monthProfits: reactive([]),
    }),

    actions: {
        _sanitizeFilters(raw) {
            const params = { ...raw };
            // Normalize 'all' or empty-like values to undefined so backend treats as no filter
            if (
                params.season &&
                String(params.season).toLowerCase() === "all"
            ) {
                delete params.season;
            }
            if (
                params.university_type &&
                String(params.university_type).toLowerCase() === "all"
            ) {
                // keep 'all' for endpoints that accept it; summary does accept 'all'.
                // We will only delete for endpoints that do NOT accept it.
            }
            // Remove explicit nulls to avoid exists validations
            Object.keys(params).forEach((k) => {
                if (
                    params[k] === null ||
                    params[k] === undefined ||
                    params[k] === ""
                ) {
                    delete params[k];
                }
            });
            return params;
        },
        async fetchSummaryData({
            year,
            season,
            university_type,
            province_id,
            university_id,
            shift,
            faculty_id,
        }) {
            this.isLoading = true;
            try {
                const raw = {
                    year,
                    season,
                    university_type,
                    province_id,
                    university_id,
                    faculty_id,
                    shift,
                };
                // summary endpoint accepts university_type=all but NOT season=all
                const params = this._sanitizeFilters(raw);
                const response = await axios.get("/dashboard/summary", {
                    params,
                });

                const data = response.data.data;

                this.$patch({
                    summaryData: {
                        total_students: parseInt(data.total_students) || 0,
                        new_students: parseInt(data.new_students) || 0,
                        graduated_students:
                            parseInt(data.graduated_students) || 0,
                        universities_count: {
                            public: data.universities_count.public || 0,
                            private: data.universities_count.private || 0,
                            total: data.universities_count.total || 0,
                        },
                        student_teacher_ratio:
                            parseFloat(data.student_teacher_ratio) || 0,
                    },
                });
            } catch (error) {
                console.error("Failed to fetch summary data:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchFacultyBreakdown(filters = {}) {
            this.isLoading = true;
            try {
                // faculty-breakdown does NOT accept season=all; strip it
                const params = this._sanitizeFilters(filters);
                const response = await axios.get(
                    "/dashboard/faculty-breakdown",
                    { params }
                );
                this.facultyBreakdown = response.data.data || [];
            } catch (error) {
                console.error("Failed to fetch faculty breakdown:", error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchFacultyTrends(filters = {}) {
            this.isLoading = true;
            try {
                const response = await axios.get("/dashboard/trends", {
                    params: filters,
                });
                this.trends = response.data.data || [];
            } catch (error) {
                console.error("Failed to fetch trends :", error);
            } finally {
                this.isLoading = false;
            }
        },
        async fetchGenderDistribution(filters = {}) {
            this.isLoading = true;
            try {
                // gender-distribution does NOT accept season=all; strip it
                const params = this._sanitizeFilters(filters);
                const response = await axios.get(
                    "/dashboard/gender-distribution",
                    { params }
                );
                this.genderDistribution = response.data.data || {
                    data: [],
                    percentages: {},
                };
            } catch (error) {
                console.error("Failed to fetch gender distribution:", error);
                this.genderDistribution = { data: [], percentages: {} };
            } finally {
                this.isLoading = false;
            }
        },
        async fetchRecentActivity(limit = 5) {
            try {
                const response = await axios.get("/dashboard/recent-activity", {
                    params: { limit },
                });
                this.recentActivity = response.data.data || [];
            } catch (error) {
                console.error("Failed to fetch recent activity:", error);
                this.recentActivity = [];
            }
        },

        async fetchUniversities() {
            try {
                const response = await axios.get(`universities`);
                this.universities = response.data.data;
            } catch (error) {
                console.error("Failed to fetch universities:", error);
            }
        },
        async fetchProvinces() {
            try {
                const response = await axios.get(`provinces`);
                this.provinces = response.data.data || [];
            } catch (error) {
                console.error("Failed to fetch provinces:", error);
                this.provinces = [];
            }
        },
    },
});
