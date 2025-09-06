<template>
    <AppBar :pageTitle="$t('dashboard')" />
    <v-divider :thickness="1" class="border-opacity-100 mb-4"></v-divider>

    <v-container fluid>
        <!-- Global Context Filters -->
        <v-card class="mb-4 elevation-1">
            <v-toolbar density="comfortable" flat>
                <v-toolbar-title>
                    <v-icon class="mr-2">mdi-tune-variant</v-icon>
                    {{ $t("global_filters") }}
                </v-toolbar-title>
            </v-toolbar>
            <v-divider></v-divider>
            <v-row dense class="pa-4">
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filters.year"
                        :items="yearsWithAll"
                        item-title="label"
                        item-value="value"
                        :label="$t('year')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filters.university_type"
                        :items="['all', 'public', 'private']"
                        :label="$t('University Type')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filters.province_id"
                        :items="provincesWithAll"
                        item-title="name"
                        item-value="id"
                        :label="$t('Province')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filters.university_id"
                        :items="universitiesWithAll"
                        item-title="name"
                        item-value="id"
                        :label="$t('University')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
                <v-col cols="12" md="3">
                    <v-select
                        v-model="filters.shift"
                        :items="['day', 'night']"
                        :label="$t('Shift')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
            </v-row>
        </v-card>

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
                <v-card class="elevation-1">
                    <v-toolbar density="comfortable" flat>
                        <v-toolbar-title>
                            <v-icon class="mr-2">mdi-chart-bar</v-icon>
                            {{ $t("students_per") }}
                            {{
                                breakdownFilters.breakdown_level ===
                                "department"
                                    ? $t("department")
                                    : $t("faculty")
                            }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <div class="d-flex" style="gap: 8px">
                            <v-select
                                v-model="breakdownFilters.breakdown_level"
                                :items="['faculty', 'department']"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="
                                    $t('faculty') + ' / ' + $t('department')
                                "
                                @update:modelValue="fetchBarChart"
                            />
                            <v-select
                                v-model="breakdownFilters.year"
                                :items="yearsWithAll"
                                item-title="label"
                                item-value="value"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('year')"
                                @update:modelValue="fetchBarChart"
                            />
                            <v-select
                                v-model="breakdownFilters.season"
                                :items="seasonsWithAll"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('season')"
                                @update:modelValue="fetchBarChart"
                            />
                        </div>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <div class="pa-4">
                        <div class="text-caption text-medium-emphasis mb-2">
                            {{ $t("shows_total_students_hint") }}
                        </div>
                        <canvas ref="barChartCanvas"></canvas>
                    </div>
                </v-card>
            </v-col>

            <v-col cols="12" md="6">
                <v-card class="elevation-1">
                    <v-toolbar density="comfortable" flat>
                        <v-toolbar-title>
                            <v-icon class="mr-2">mdi-chart-donut</v-icon>
                            {{ $t("gender_distribution") }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <div class="d-flex" style="gap: 8px">
                            <v-select
                                v-model="genderFilters.year"
                                :items="yearsWithAll"
                                item-title="label"
                                item-value="value"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('year')"
                                @update:modelValue="fetchGenderChart"
                            />
                            <v-select
                                v-model="genderFilters.season"
                                :items="seasonsWithAll"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('season')"
                                @update:modelValue="fetchGenderChart"
                            />
                        </div>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <div class="pa-4">
                        <div class="text-caption text-medium-emphasis mb-2">
                            {{ $t("gender_distribution_hint") }}
                        </div>
                        <canvas ref="genderChartCanvas"></canvas>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <v-row class="mt-6">
            <v-col cols="12" md="12">
                <v-card class="elevation-1">
                    <v-toolbar density="comfortable" flat>
                        <v-toolbar-title>
                            <v-icon class="mr-2"
                                >mdi-chart-timeline-variant</v-icon
                            >
                            {{ $t("student_trends") }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <div class="d-flex" style="gap: 8px">
                            <v-select
                                v-model="trendFilters.university_type"
                                :items="['public', 'private']"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('university_type')"
                                @update:modelValue="fetchTrends"
                            />
                            <v-select
                                v-model="trendFilters.province_id"
                                :items="DashboardRepo.provinces"
                                item-title="name"
                                item-value="id"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('Province')"
                                @update:modelValue="fetchTrends"
                            />
                            <v-select
                                v-model="trendFilters.time_range"
                                :items="['5years', '10years', 'all']"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                label="Range"
                                @update:modelValue="fetchTrends"
                            />
                            <v-select
                                v-model="trendFilters.group_by"
                                :items="['year', 'season']"
                                density="compact"
                                hide-details
                                style="max-width: 140px"
                                :label="$t('group')"
                                @update:modelValue="fetchTrends"
                            />
                        </div>
                    </v-toolbar>
                    <v-divider></v-divider>
                    <div class="pa-4">
                        <div class="text-caption text-medium-emphasis mb-2">
                            {{ $t("trend_hint") }}
                        </div>
                        <canvas ref="lineChartCanvas"></canvas>
                    </div>
                </v-card>
            </v-col>
        </v-row>

        <!-- Recent Activity Section -->
        <v-card class="mt-6">
            <div class="d-flex justify-space-between align-center mb-4 pa-4">
                <div class="text-subtitle-1">{{ $t("recent_activities") }}</div>
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

            <div v-else class="activity-grid pa-4">
                <v-card
                    v-for="(log, index) in DashboardRepo.recentActivity"
                    :key="log.id"
                    class="activity-card"
                    elevation="2"
                >
                    <v-card-text>
                        <div
                            class="d-flex justify-space-between align-center mb-2"
                        >
                            <div class="d-flex align-center">
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
                            </div>
                            <v-chip
                                small
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

                        <div class="d-flex justify-space-between align-center">
                            <div class="text-caption text-medium-emphasis">
                                <v-icon small>mdi-clock-outline</v-icon>
                                {{
                                    log.created_at
                                        ? formatDate(log.created_at)
                                        : "N/A"
                                }}
                            </div>
                            <div class="text-caption text-medium-emphasis">
                                {{
                                    log.created_at
                                        ? formatTimeAgo(log.created_at)
                                        : "N/A"
                                }}
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </div>

            <div
                v-if="!loadingActivity && recentActivities.length === 0"
                class="d-flex flex-column justify-center align-center text-center py-4"
            >
                <v-icon size="64" color="grey lighten-1"
                    >mdi-information-outline</v-icon
                >
                <div class="text-subtitle-1 mt-2">
                    {{ $t("no_data_available") }}
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
const genderChartCanvas = ref(null);
const loadingActivity = ref(false);
const recentActivities = ref([]);

// Filters
const years = [1400, 1401, 1402, 1403, 1404, 1405, 1406];
const yearsWithAll = computed(() => [
    { label: "All Years", value: null },
    ...years.map((y) => ({ label: String(y), value: y })),
]);
const seasonsWithAll = ["all", "spring", "autumn"];

const provincesWithAll = computed(() => [
    { id: null, name: "All Provinces" },
    ...(DashboardRepo.provinces || []),
]);
const universitiesWithAll = computed(() => [
    { id: null, name: "All Universities" },
    ...(DashboardRepo.universities || []),
]);

const filters = ref({
    year: null,
    season: "all",
    university_type: "all",
    province_id: null,
    university_id: null,
    shift: "day",
    breakdown_level: "faculty",
});

// summary uses global filters now

const breakdownFilters = ref({
    year: 1402,
    season: "spring",
    breakdown_level: "faculty",
});

const genderFilters = ref({
    year: 1402,
    season: "spring",
});

const trendFilters = ref({
    university_type: "all",
    province_id: null,
    time_range: "10years",
    group_by: "year",
    season: filters.value.season,
});

// Provinces now loaded from repository (DashboardRepo.provinces)

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
    {
        title: "Student/Teacher Ratio",
        value: DashboardRepo.summaryData.student_teacher_ratio,
        icon: "mdi-account-group-outline",
    },
]);

// Fetch functions
function buildTopFilterParams() {
    return {
        year: filters.value.year,
        season: filters.value.season,
        university_type: filters.value.university_type,
        province_id: filters.value.province_id,
        university_id: filters.value.university_id,
        shift: filters.value.shift,
    };
}

async function fetchData() {
    // Fetch summary with global filters so cards reflect selected context
    await DashboardRepo.fetchSummaryData(buildTopFilterParams());
    await fetchRecentActivity();
}

async function handleTopFiltersChange() {
    // Province/university changes should inform charts as well
    await fetchData();
    await fetchBarChart();
    await fetchGenderChart();
}

async function fetchBarChart() {
    await DashboardRepo.fetchFacultyBreakdown({
        year: breakdownFilters.value.year,
        season: breakdownFilters.value.season,
        breakdown_level: breakdownFilters.value.breakdown_level,
        province_id: filters.value.province_id,
        university_id: filters.value.university_id,
        university_type: filters.value.university_type,
        shift: filters.value.shift,
    });
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

async function fetchGenderChart() {
    await DashboardRepo.fetchGenderDistribution({
        year: genderFilters.value.year,
        season: genderFilters.value.season,
        province_id: filters.value.province_id,
        university_id: filters.value.university_id,
        university_type: filters.value.university_type,
        shift: filters.value.shift,
    });

    const labels = (DashboardRepo.genderDistribution.data || []).map(
        (d) => d.gender
    );
    const data = (DashboardRepo.genderDistribution.data || []).map((d) =>
        parseInt(d.count)
    );

    if (genderChartCanvas.value._chartInstance) {
        genderChartCanvas.value._chartInstance.destroy();
    }

    const chart = new Chart(genderChartCanvas.value, {
        type: "doughnut",
        data: {
            labels,
            datasets: [
                {
                    data,
                    backgroundColor: ["#29B6F6", "#EC407A"],
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
        },
    });

    genderChartCanvas.value._chartInstance = chart;
}

// Load everything
onMounted(async () => {
    await fetchData();
    await fetchBarChart();
    await fetchGenderChart();
    await fetchTrends();
    DashboardRepo.fetchUniversities();
    DashboardRepo.fetchProvinces();
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
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 16px;
}

.activity-card {
    height: 100%;
    transition: transform 0.2s;
}

.activity-card:hover {
    transform: translateY(-2px);
}

.activity-description {
    line-height: 1.6;
    margin: 8px 0;
}

canvas {
    width: 100% !important;
    height: 300px !important;
}

@media (max-width: 600px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
}
</style>
