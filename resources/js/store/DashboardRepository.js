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
        // =========================================================================================================

        // Fetch data from the API
        async fetchDashboardData() {
            this.isLoading = true;
            try {
                const response = await axios.get("dashboardReport");
                const data = response.data;

                console.log("Fetched dashboard data:", data);

                // Ensure numeric values are converted to numbers
                this.$patch({
                    dashboardReport: data,
                    dashboards: data,
                    totalExpenses: parseFloat(data.totalAllExpenses) || 0,
                    todayExpenses: data.dailyExpenses || [],
                    thisMonthExpenses: data.monthlyExpenses || [],
                    thisYearExpenses: data.yearlyExpenses || [],
                    monthExpenses: data.monthExpenses || [],
                    monthIncomes: data.monthIncomes || [],
                    monthProfits: data.monthProfits || [],
                    earnings: parseFloat(data.totalEarnings) || 0,
                    expensesList: this.processExpenses(
                        data.monthlyExpenses,
                        "green"
                    ),
                });

                this.isLoading = false;
            } catch (error) {
                console.error("Failed to fetch dashboard data:", error);
                this.error = "Failed to load dashboard data.";
                this.isLoading = false;
            }
        },

        // Update expenses dynamically
        updateExpenses(expenses, color) {
            console.log("Updating expenses with:", expenses, color);
            if (!Array.isArray(expenses)) {
                console.warn("Expenses parameter is not an array");
                return;
            }
            this.totalExpenses = expenses.reduce(
                (acc, expense) => acc + (parseFloat(expense.totalExpense) || 0),
                0
            );
            this.expensesList = this.processExpenses(expenses, color);
        },

        // Process expenses to add percentage and color
        processExpenses(expenses, color) {
            return expenses.map((exp) => ({
                ...exp,
                percentage:
                    ((parseFloat(exp.totalExpense) || 0) / this.totalExpenses) *
                    100,
                color,
            }));
        },
    },
});
