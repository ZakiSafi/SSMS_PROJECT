<template>
    <AppBar pageTitle="Dashboard" />
    <v-divider :thickness="1" class="border-opacity-100 mb-4"></v-divider>

    <v-container fluid>
        <!-- Filter Section -->
        <v-row dense>
            <v-col cols="12" md="4">
                <v-select
                    v-model="filters.year"
                    :items="years"
                    label="Year"
                    dense
                    @update:modelValue="fetchData"
                />
            </v-col>
            <v-col cols="12" md="4">
                <v-select
                    v-model="filters.season"
                    :items="seasons"
                    label="Season"
                    dense
                    @update:modelValue="fetchData"
                />
            </v-col>
            <v-col cols="12" md="4">
                <v-select
                    v-model="filters.university"
                    :items="universities"
                    label="University"
                    dense
                    @update:modelValue="fetchData"
                />
            </v-col>
        </v-row>

        <!-- Summary Cards -->
        <v-row class="mt-4">
            <v-col
                cols="12"
                sm="6"
                md="3"
                v-for="(stat, index) in summaryStats"
                :key="index"
            >
                <v-card class="pa-4 text-center elevation-1">
                    <v-icon size="32" color="primary">{{ stat.icon }}</v-icon>
                    <div class="text-subtitle-1 mt-2">{{ stat.title }}</div>
                    <div class="text-h5 font-weight-bold">{{ stat.value }}</div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Charts Section -->
        <v-row class="mt-6">
            <v-col cols="12" md="6">
                <v-card class="pa-4 elevation-1">
                    <div class="text-subtitle-1 mb-2">
                        Students per Department
                    </div>
                    <canvas ref="barChartCanvas"></canvas>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card class="pa-4 elevation-1">
                    <div class="text-subtitle-1 mb-2">Student Trends</div>
                    <canvas ref="lineChartCanvas"></canvas>
                </v-card>
            </v-col>
        </v-row>

        <!-- Recent Activity Section -->
        <v-card class="mt-6 pa-4 elevation-1">
            <div class="d-flex justify-space-between align-center mb-4">
                <div class="text-subtitle-1">Recent Activities</div>
                <v-btn
                    icon
                    @click="fetchRecentActivity"
                    :loading="loadingActivity"
                >
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </div>

            <v-progress-linear
                v-if="loadingActivity"
                indeterminate
                color="primary"
            ></v-progress-linear>

            <v-timeline v-else align="start" side="end">
                <v-timeline-item
                    v-for="(log, index) in recentActivities"
                    :key="log.id"
                    :dot-color="getTimelineColor(log.action_type)"
                    size="small"
                >
                    <template v-slot:opposite>
                        <div class="text-caption text-medium-emphasis">
                            {{ formatDate(log.created_at) }}
                        </div>
                    </template>

                    <v-card class="elevation-2">
                        <v-card-text>
                            <div class="d-flex align-center mb-2">
                                <v-avatar
                                    size="32"
                                    color="primary"
                                    class="mr-2"
                                >
                                    <span class="text-white">{{
                                        getUserInitials(log.user_name)
                                    }}</span>
                                </v-avatar>
                                <div>
                                    <strong>{{ log.user_name }}</strong>
                                    <div class="text-caption">
                                        {{ log.user_email }}
                                    </div>
                                </div>
                                <v-chip
                                    small
                                    class="ml-auto"
                                    :color="getActionColor(log.action_type)"
                                    text-color="white"
                                >
                                    {{ log.action_type.toUpperCase() }}
                                </v-chip>
                            </div>

                            <div class="mb-2">
                                <v-icon small class="mr-1">mdi-school</v-icon>
                                <span
                                    >{{ log.university_name }} ({{
                                        log.university_type
                                    }})</span
                                >
                            </div>

                            <div class="activity-description">
                                {{ log.action_description }}
                            </div>

                            <v-divider class="my-2"></v-divider>

                            <div class="text-caption text-medium-emphasis">
                                <v-icon small>mdi-clock-outline</v-icon>
                                {{ formatTimeAgo(log.created_at) }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-timeline-item>
            </v-timeline>

            <div
                v-if="!loadingActivity && recentActivities.length === 0"
                class="text-center py-4"
            >
                <v-icon size="64" color="grey lighten-1"
                    >mdi-information-outline</v-icon
                >
                <div class="text-subtitle-1 mt-2">
                    No recent activities found
                </div>
            </div>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { Chart, registerables } from "chart.js";
import axios from "axios";
import AppBar from "@/components/AppBar.vue";

import { useDashboardRepository } from "../store/DashboardRepository";
Chart.register(...registerables);
async function fetchData() {
    await DashboardRepo.fetchSummaryData({
        year: filters.value.year,
        season: filters.value.season,
        university:
            filters.value.university !== "All"
                ? filters.value.university
                : null,
    });

    await fetchRecentActivity();
}

const DashboardRepo = useDashboardRepository();

// ...

const summaryStats = computed(() => [
    {
        title: "Total Students",
        value: DashboardRepo.summaryData.total_students,
        icon: "mdi-school",
    },
    {
        title: "New Students",
        value: DashboardRepo.summaryData.new_students,
        icon: "mdi-account-plus",
    },
    {
        title: "Graduated Students",
        value: DashboardRepo.summaryData.graduated_students,
        icon: "mdi-school-outline",
    },
    {
        title: "Universities",
        value: DashboardRepo.summaryData.universities_count.total,
        icon: "mdi-office-building",
    },
]);

// Filters
const filters = ref({
    year: 1402,
    season: "spring",
    university: "All",
});
const years = [1402, 1403, 1404];
const seasons = ["spring", "autumn"];
const universities = ["all", "kabul University", "herat University"];

// Recent Activities
const recentActivities = ref([]);
const loadingActivity = ref(false);

// Chart Refs
const barChartCanvas = ref(null);
const lineChartCanvas = ref(null);
// await DashboardRepo.fetchFacultyTrends({
//     university_type: "private",
//     time_range: "10years",
//     group_by: "year",
//     season: filters.value.season,
//     province_id: 15, // You may bind this dynamically later
// });

onMounted(async () => {
    // Fetch summary and activities
    await fetchData();

    // Fetch bar chart
    await DashboardRepo.fetchFacultyBreakdown();
    const labels = DashboardRepo.facultyBreakdown.map((f) => f.name);
    const data = DashboardRepo.facultyBreakdown.map((f) => f.total_students);
    if (barChartCanvas.value._chartInstance) {
        barChartCanvas.value._chartInstance.destroy();
    }
    const barChart = new Chart(barChartCanvas.value, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Total Students",
                    data,
                    backgroundColor: "#42A5F5",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true },
            },
        },
    });
    barChartCanvas.value._chartInstance = barChart;

    // Fetch trends chart
    await DashboardRepo.fetchFacultyTrends({
        university_type: "private",
        time_range: "10years",
        group_by: "year",
        season: filters.value.season,
        province_id: 15,
    });
    const trendLabels = DashboardRepo.trends.map((item) => item.year); // adjust key
    const enrolledData = DashboardRepo.trends.map((item) => item.enrolled);
    const graduatedData = DashboardRepo.trends.map((item) => item.graduated);
    if (lineChartCanvas.value._chartInstance) {
        lineChartCanvas.value._chartInstance.destroy();
    }
    const lineChart = new Chart(lineChartCanvas.value, {
        type: "line",
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: "Enrolled",
                    data: enrolledData,
                    borderColor: "#42A5F5",
                    backgroundColor: "rgba(66, 165, 245, 0.1)",
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: "Graduated",
                    data: graduatedData,
                    borderColor: "#66BB6A",
                    backgroundColor: "rgba(102, 187, 106, 0.1)",
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true },
            },
        },
    });
    lineChartCanvas.value._chartInstance = lineChart;
});

// Helper functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function formatTimeAgo(dateString) {
    const now = new Date();
    const date = new Date(dateString);
    const diffInSeconds = Math.floor((now - date) / 1000);

    if (diffInSeconds < 60) return "Just now";
    if (diffInSeconds < 3600)
        return `${Math.floor(diffInSeconds / 60)} minutes ago`;
    if (diffInSeconds < 86400)
        return `${Math.floor(diffInSeconds / 3600)} hours ago`;
    return `${Math.floor(diffInSeconds / 86400)} days ago`;
}

function getUserInitials(name) {
    if (!name) return "UU";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase();
}

function getTimelineColor(actionType) {
    const colors = {
        create: "green",
        update: "blue",
        delete: "red",
        login: "purple",
        logout: "orange",
    };
    return colors[actionType] || "grey";
}

function getActionColor(actionType) {
    const colors = {
        create: "success",
        update: "info",
        delete: "error",
        login: "secondary",
        logout: "warning",
    };
    return colors[actionType] || "primary";
}

async function fetchRecentActivity() {
    loadingActivity.value = true;
    try {
        const response = await axios.get("/dashboard/recent-activity", {
            params: { limit: 5 },
        });
        recentActivities.value = response.data.data;
    } catch (error) {
        console.error("Error fetching recent activity:", error);
    } finally {
        loadingActivity.value = false;
    }
}

// async function fetchData() {
//     // You can add other data fetching here if needed
//     await fetchRecentActivity();
// }

// Init Charts and fetch initial data
onMounted(async () => {
    await DashboardRepo.fetchFacultyBreakdown(); // fetch data

    // Build chart data from the repository
    const labels = DashboardRepo.facultyBreakdown.map((f) => f.name);
    const data = DashboardRepo.facultyBreakdown.map((f) => f.total_students);

    // Destroy existing chart instance if needed
    if (barChartCanvas.value._chartInstance) {
        barChartCanvas.value._chartInstance.destroy();
    }

    const barChart = new Chart(barChartCanvas.value, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: "Total Students",
                    data,
                    backgroundColor: "#42A5F5",
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true },
            },
        },
    });

    // Store chart instance (optional, for destroy/re-render)
    barChartCanvas.value._chartInstance = barChart;

    // Call other initial methods if needed
    await fetchData();
});
</script>

<style scoped>
canvas {
    width: 100% !important;
    height: 300px !important;
}
.activity-description {
    line-height: 1.6;
    margin: 8px 0;
}
.v-timeline-item {
    min-height: 80px;
}
</style>
