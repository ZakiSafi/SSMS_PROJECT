import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";

export const useDashboardRepository = defineStore("DashboardRepository", {
    state: () => ({
        // Dashboard data
        dashboards: reactive([]),
        totalExpenses: 0,
        expenses: reactive([]),

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
        async fetchSummaryData({ year, season, university }) {
            this.isLoading = true;
            try {
                const response = await axios.get("/dashboard/summary", {
                    params: {
                        year,
                        season: season.toLowerCase(),
                        shift: "day", // You can make this dynamic too if needed
                        university, // optional, if your API supports it
                    },
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
        async fetchFacultyBreakdown() {
            this.isLoading = true;
            try {
                const response = await axios.get(
                    "/dashboard/faculty-breakdown"
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

        // =========================================================================================================
    },
});
