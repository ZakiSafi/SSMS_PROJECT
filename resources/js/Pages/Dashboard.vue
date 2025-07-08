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
        <!-- Trend Chart Filters -->
        <v-row dense class="mt-4">
            <v-col cols="12" md="3">
                <v-select
                    v-model="trendFilters.university_type"
                    :items="['public', 'private']"
                    label="University Type"
                    dense
                    @update:modelValue="fetchTrends"
                />
            </v-col>

            <v-col cols="12" md="3">
                <v-select
                    v-model="trendFilters.province_id"
                    :items="provinces"
                    label="Province"
                    dense
                    item-title="name"
                    item-value="id"
                    @update:modelValue="fetchTrends"
                />
            </v-col>

            <v-col cols="12" md="3">
                <v-select
                    v-model="trendFilters.time_range"
                    :items="['5years', '10years']"
                    label="Time Range"
                    dense
                    @update:modelValue="fetchTrends"
                />
            </v-col>

            <v-col cols="12" md="3">
                <v-select
                    v-model="trendFilters.group_by"
                    :items="['year', 'season']"
                    label="Group By"
                    dense
                    @update:modelValue="fetchTrends"
                />
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
                    v-for="(log, index) in DashboardRepo.recentActivity"
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

const DashboardRepo = useDashboardRepository();

async function fetchRecentActivity() {
    loadingActivity.value = true;
    try {
        await DashboardRepo.fetchRecentActivity();
        recentActivities.value = DashboardRepo.recentActivity;
    } catch (err) {
        console.error("Error fetching activity:", err);
    } finally {
        loadingActivity.value = false;
    }
}

// Refs
const barChartCanvas = ref(null);
const lineChartCanvas = ref(null);
const loadingActivity = ref(false);
const recentActivities = ref([]);

// Filters
const filters = ref({
    year: 1402,
    season: "spring",
    university: "All",
});

const trendFilters = ref({
    university_type: "public",
    province_id: 15,
    time_range: "10years",
    group_by: "year",
    season: filters.value.season,
});

const provinces = [
    { id: 15, name: "Kabul" },
    { id: 16, name: "Herat" },
    { id: 17, name: "Balkh" },
];

// Summary Stats
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

// Fetch functions
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

async function fetchBarChart() {
    await DashboardRepo.fetchFacultyBreakdown();
    const labels = DashboardRepo.facultyBreakdown.map((f) => f.name);
    const data = DashboardRepo.facultyBreakdown.map((f) => f.total_students);

    if (barChartCanvas.value._chartInstance) {
        barChartCanvas.value._chartInstance.destroy();
    }

    const chart = new Chart(barChartCanvas.value, {
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

    barChartCanvas.value._chartInstance = chart;
}

async function fetchTrends() {
    await DashboardRepo.fetchFacultyTrends(trendFilters.value);

    const trendLabels = DashboardRepo.trends.map((item) => item.year);
    const total = DashboardRepo.trends.map((item) => parseInt(item.total));
    const male = DashboardRepo.trends.map((item) => parseInt(item.male));
    const female = DashboardRepo.trends.map((item) => parseInt(item.female));

    if (lineChartCanvas.value._chartInstance) {
        lineChartCanvas.value._chartInstance.destroy();
    }

    const chart = new Chart(lineChartCanvas.value, {
        type: "line",
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: "Total Students",
                    data: total,
                    borderColor: "#42A5F5",
                    backgroundColor: "rgba(66, 165, 245, 0.1)",
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: "Male",
                    data: male,
                    borderColor: "#29B6F6",
                    backgroundColor: "rgba(41, 182, 246, 0.1)",
                    fill: true,
                    tension: 0.3,
                },
                {
                    label: "Female",
                    data: female,
                    borderColor: "#EC407A",
                    backgroundColor: "rgba(236, 64, 122, 0.1)",
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

    lineChartCanvas.value._chartInstance = chart;
}

// Load everything
onMounted(async () => {
    await fetchData();
    await fetchBarChart();
    await fetchTrends();
});

// Helpers
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
    const diff = Math.floor((now - date) / 1000);

    if (diff < 60) return "Just now";
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    return `${Math.floor(diff / 86400)} days ago`;
}

function getUserInitials(name) {
    return name
        ? name
              .split(" ")
              .map((n) => n[0])
              .join("")
              .toUpperCase()
        : "UU";
}

function getTimelineColor(type) {
    return (
        {
            create: "green",
            update: "blue",
            delete: "red",
            login: "purple",
            logout: "orange",
        }[type] || "grey"
    );
}

function getActionColor(type) {
    return (
        {
            create: "success",
            update: "info",
            delete: "error",
            login: "secondary",
            logout: "warning",
        }[type] || "primary"
    );
}
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
