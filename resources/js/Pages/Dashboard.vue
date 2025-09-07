<template>
    <AppBar pageTitle="Dashboard" />
    <v-divider :thickness="1" class="border-opacity-100 mb-6"></v-divider>

    <v-container fluid class="pa-6">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-h4 font-weight-bold text-primary mb-2">{{ $t('Dashboard') }}</h1>
            <p class="text-subtitle-1 text-medium-emphasis">{{ $t('Welcome to the Student Management System Dashboard') }}</p>
        </div>

        <!-- Filter Section -->
        <v-card class="mb-8" elevation="2" rounded="lg">
            <v-card-title class="d-flex align-center pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-filter-variant</v-icon>
                <span class="text-h6">{{ $t('Filters') }}</span>
            </v-card-title>
            <v-card-text class="pa-6 pt-0">
                <v-row>
                    <v-col cols="12" sm="6" md="2.4" lg="2.4" xl="2.4">
                        <v-select
                            v-model="filters.year"
                            :items="yearOptions"
                            :label="$t('form.select_year')"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-calendar"
                            @update:modelValue="fetchData"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2.4" lg="2.4" xl="2.4">
                        <v-select
                            v-model="filters.season"
                            :items="seasonOptions"
                            :label="$t('Season')"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-weather-partly-cloudy"
                            @update:modelValue="fetchData"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2.4" lg="2.4" xl="2.4">
                        <v-select
                            v-model="filters.university"
                            :items="universityOptions"
                            item-title="name"
                            item-value="id"
                            :label="$t('University')"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-school"
                            @update:modelValue="fetchData"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2.4" lg="2.4" xl="2.4">
                        <v-select
                            v-model="filters.university_type"
                            :items="universityTypeOptions"
                            :label="$t('University Type')"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-domain"
                            @update:modelValue="fetchData"
                        />
                    </v-col>
                    <v-col cols="12" sm="6" md="2.4" lg="2.4" xl="2.4">
                        <v-select
                            v-model="filters.province"
                            :items="provinceOptions"
                            item-title="name"
                            item-value="id"
                            :label="$t('Province')"
                            variant="outlined"
                            density="comfortable"
                            prepend-inner-icon="mdi-map-marker"
                            @update:modelValue="fetchData"
                        />
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>

        <!-- Summary Cards -->
        <v-row class="mb-8">
            <v-col
                cols="12"
                sm="6"
                md="2.4"
                lg="2.4"
                xl="2.4"
                v-for="(stat, index) in summaryStats"
                :key="index"
            >
                <v-card 
                    class="summary-card h-100" 
                    elevation="3" 
                    rounded="xl"
                    :class="`stat-card-${index + 1}`"
                >
                    <v-card-text class="pa-6 text-center">
                        <div class="icon-container mb-4">
                            <v-icon size="48" :color="getCardColor(index)">{{ stat.icon }}</v-icon>
                        </div>
                        <div class="text-h6 font-weight-medium text-medium-emphasis mb-2">{{ stat.title }}</div>
                        <div class="text-h3 font-weight-bold" :class="`text-${getCardColor(index)}`">{{ stat.value }}</div>
                        <div class="text-caption text-medium-emphasis mt-2">{{ getCardSubtitle(index) }}</div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <!-- Charts Section -->
        <v-row class="mb-8">
            <!-- Department Chart -->
            <v-col cols="12" lg="6" class="mb-6">
                <v-card elevation="3" rounded="xl" class="chart-card">
                    <v-card-title class="d-flex align-center pa-6 pb-4">
                        <v-icon class="mr-3" color="primary">mdi-chart-bar</v-icon>
                        <span class="text-h6">{{ $t('Students per Department') }}</span>
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            @click="fetchBarChart"
                        >
                            <v-icon>mdi-refresh</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="pa-6 pt-0">
                        <div class="chart-container">
                            <canvas ref="barChartCanvas"></canvas>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Trends Chart -->
            <v-col cols="12" lg="6" class="mb-6">
                <v-card elevation="3" rounded="xl" class="chart-card">
                    <v-card-title class="d-flex align-center pa-6 pb-4">
                        <v-icon class="mr-3" color="primary">mdi-chart-line</v-icon>
                        <span class="text-h6">{{ $t('Student Trends') }}</span>
                        <v-spacer></v-spacer>
                        <v-btn
                            icon
                            size="small"
                            variant="text"
                            @click="fetchTrends"
                        >
                            <v-icon>mdi-refresh</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-card-text class="pa-6 pt-0">
                        <!-- Chart Type Controls -->
                        <v-row class="mb-4">
                            <v-col cols="12" sm="6" md="3">
                                <v-select
                                    v-model="trendFilters.university_type"
                                    :items="universityTypeOptions"
                                    :label="$t('University Type')"
                                    variant="outlined"
                                    density="compact"
                                    prepend-inner-icon="mdi-domain"
                                    @update:modelValue="fetchTrends"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-select
                                    v-model="trendFilters.province_id"
                                    :items="trendProvinceOptions"
                                    :label="$t('Province')"
                                    variant="outlined"
                                    density="compact"
                                    item-title="name"
                                    item-value="id"
                                    prepend-inner-icon="mdi-map-marker"
                                    @update:modelValue="fetchTrends"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-select
                                    v-model="trendFilters.time_range"
                                    :items="timeRangeOptions"
                                    :label="$t('Time Range')"
                                    variant="outlined"
                                    density="compact"
                                    prepend-inner-icon="mdi-clock"
                                    @update:modelValue="fetchTrends"
                                />
                            </v-col>
                            <v-col cols="12" sm="6" md="3">
                                <v-select
                                    v-model="trendFilters.chart_type"
                                    :items="chartTypeOptions"
                                    :label="$t('Chart Type')"
                                    variant="outlined"
                                    density="compact"
                                    prepend-inner-icon="mdi-chart"
                                    @update:modelValue="fetchTrends"
                                />
                            </v-col>
                        </v-row>
                        <div class="chart-container">
                            <canvas ref="lineChartCanvas"></canvas>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <!-- Recent Activity Section -->
        <v-card elevation="3" rounded="xl">
            <v-card-title class="d-flex align-center pa-6 pb-4">
                <v-icon class="mr-3" color="primary">mdi-history</v-icon>
                <span class="text-h6">{{ $t('Recent Activities') }}</span>
                <v-spacer></v-spacer>
                <v-btn
                    icon
                    variant="text"
                    @click="fetchRecentActivity"
                    :loading="loadingActivity"
                >
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-card-title>

            <v-progress-linear
                v-if="loadingActivity"
                indeterminate
                color="primary"
            ></v-progress-linear>

            <v-card-text v-else class="pa-6 pt-0">
                <div v-if="DashboardRepo.recentActivity && DashboardRepo.recentActivity.length > 0" class="activity-grid">
                    <v-card
                        v-for="(log, index) in DashboardRepo.recentActivity"
                        :key="log.id"
                        class="activity-card"
                        elevation="2"
                        rounded="lg"
                    >
                        <v-card-text class="pa-4">
                            <div class="d-flex justify-space-between align-center mb-3">
                                <div class="d-flex align-center">
                                    <v-avatar
                                        size="40"
                                        :color="getActionColor(log.action_type)"
                                        class="mr-3"
                                    >
                                        <span class="text-white text-subtitle-2">{{
                                            getUserInitials(log.user_name)
                                        }}</span>
                                    </v-avatar>
                                    <div>
                                        <div class="text-subtitle-2 font-weight-medium">{{ log.user_name }}</div>
                                        <div class="text-caption text-medium-emphasis">
                                            {{ log.user_email }}
                                        </div>
                                    </div>
                                </div>
                                <v-chip
                                    size="small"
                                    :color="getActionColor(log.action_type)"
                                    variant="flat"
                                >
                                    {{ log.action_type.toUpperCase() }}
                                </v-chip>
                            </div>

                            <div class="mb-3 d-flex align-center">
                                <v-icon size="16" class="mr-2 text-medium-emphasis">mdi-school</v-icon>
                                <span class="text-body-2">
                                    {{ log.university_name }} ({{ log.university_type }})
                                </span>
                            </div>

                            <div class="activity-description mb-3">
                                {{ log.action_description }}
                            </div>

                            <v-divider class="mb-3"></v-divider>

                            <div class="d-flex justify-space-between align-center">
                                <div class="text-caption text-medium-emphasis d-flex align-center">
                                    <v-icon size="14" class="mr-1">mdi-clock-outline</v-icon>
                                    {{ log.created_at ? formatDate(log.created_at) : "N/A" }}
                                </div>
                                <div class="text-caption text-medium-emphasis">
                                    {{ log.created_at ? formatTimeAgo(log.created_at) : "N/A" }}
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>
                </div>

                <div
                    v-else
                    class="d-flex flex-column justify-center align-center text-center py-8"
                >
                    <v-icon size="80" color="grey-lighten-1">mdi-information-outline</v-icon>
                    <div class="text-h6 mt-4 text-medium-emphasis">
                        {{ $t('No recent activities found') }}
                    </div>
                    <div class="text-body-2 text-medium-emphasis mt-2">
                        {{ $t('Activities will appear here when users perform actions') }}
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { Chart, registerables } from "chart.js";
import axios from "axios";
import AppBar from "@/components/AppBar.vue";
import { useDashboardRepository } from "../store/DashboardRepository";
import { useI18n } from "vue-i18n";

Chart.register(...registerables);

const DashboardRepo = useDashboardRepository();
const { t } = useI18n();

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
    university_type: "all",
    province: "all",
});

const trendFilters = ref({
    university_type: "public",
    province_id: "all",
    time_range: "10years",
    group_by: "year",
    chart_type: "line",
    season: filters.value.season,
});

// Options for dropdowns
const yearOptions = [
    { value: '1400', text: '1400' },
    { value: '1401', text: '1401' },
    { value: '1402', text: '1402' },
    { value: '1403', text: '1403' },
    { value: '1404', text: '1404' },
    { value: '1405', text: '1405' },
    { value: '1406', text: '1406' },
];

const seasonOptions = [
    { value: 'spring', text: t('Spring') },
    { value: 'autumn', text: t('Autumn') },
];

const universityTypeOptions = [
    { value: 'all', text: t('All') },
    { value: 'public', text: t('Public') },
    { value: 'private', text: t('Private') },
];

const timeRangeOptions = [
    { value: '5years', text: t('5 Years') },
    { value: '10years', text: t('10 Years') },
];

const groupByOptions = [
    { value: 'year', text: t('Year') },
    { value: 'season', text: t('Season') },
];

const chartTypeOptions = [
    { value: 'line', text: t('Line Chart') },
    { value: 'bar', text: t('Bar Chart') },
    { value: 'area', text: t('Area Chart') },
    { value: 'doughnut', text: t('Doughnut Chart') },
    { value: 'polar', text: t('Polar Chart') },
];

const provinces = [
    { id: 15, name: "Kabul" },
    { id: 16, name: "Herat" },
    { id: 17, name: "Balkh" },
];

// Computed options
const universityOptions = computed(() => [
    { id: "All", name: t('All') },
    ...DashboardRepo.universities
]);

const provinceOptions = computed(() => [
    { id: "all", name: t('All') },
    ...provinces
]);

const trendProvinceOptions = computed(() => [
    { id: "all", name: t('All Provinces') },
    ...provinces
]);

// Summary Stats
const summaryStats = computed(() => [
    {
        title: t("Total Student"),
        value: DashboardRepo.summaryData.total_students,
        icon: "mdi-school",
    },
    {
        title: t("New Students"),
        value: DashboardRepo.summaryData.new_students,
        icon: "mdi-account-plus",
    },
    {
        title: t("Graduated Students"),
        value: DashboardRepo.summaryData.graduated_students,
        icon: "mdi-school-outline",
    },
    {
        title: t("Universities"),
        value: DashboardRepo.summaryData.universities_count.total,
        icon: "mdi-office-building",
    },
    {
        title: t("Student Teacher Ratio"),
        value: DashboardRepo.summaryData.student_teacher_ratio,
        icon: "mdi-account-group",
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
        university_type: filters.value.university_type !== "all"
            ? filters.value.university_type
            : null,
        province: filters.value.province !== "all"
            ? filters.value.province
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
                    label: t("Total Students"),
                    data,
                    backgroundColor: [
                        "#1976D2",
                        "#388E3C", 
                        "#D32F2F",
                        "#F57C00",
                        "#7B1FA2",
                        "#5D4037",
                        "#455A64",
                        "#E91E63",
                        "#00BCD4",
                        "#4CAF50"
                    ],
                    borderColor: [
                        "#0D47A1",
                        "#1B5E20",
                        "#B71C1C", 
                        "#E65100",
                        "#4A148C",
                        "#3E2723",
                        "#263238",
                        "#AD1457",
                        "#006064",
                        "#1B5E20"
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#1976D2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return `${t('Faculty')}: ${context[0].label}`;
                        },
                        label: function(context) {
                            return `${t('Total Students')}: ${context.parsed.y.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: '#666',
                        maxRotation: 45,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: '#666',
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
            },
            elements: {
                bar: {
                    borderWidth: 2,
                }
            }
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

    const chartType = trendFilters.value.chart_type || 'line';
    const isArea = chartType === 'area';
    const isDoughnut = chartType === 'doughnut';
    const isPolar = chartType === 'polar';

    // For doughnut and polar charts, we'll show a different data structure
    let chartData, chartOptions;

    if (isDoughnut || isPolar) {
        // For doughnut/polar charts, show gender distribution for the latest year
        const latestYear = trendLabels[trendLabels.length - 1];
        const latestTotal = total[total.length - 1];
        const latestMale = male[male.length - 1];
        const latestFemale = female[female.length - 1];

        chartData = {
            labels: [t('Male'), t('Female')],
            datasets: [{
                data: [latestMale, latestFemale],
                backgroundColor: [
                    'rgba(56, 142, 60, 0.8)',
                    'rgba(211, 47, 47, 0.8)'
                ],
                borderColor: [
                    '#388E3C',
                    '#D32F2F'
                ],
                borderWidth: 2,
                hoverBackgroundColor: [
                    'rgba(56, 142, 60, 1)',
                    'rgba(211, 47, 47, 1)'
                ]
            }]
        };

        chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#1976D2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return `${t('Year')}: ${latestYear}`;
                        },
                        label: function(context) {
                            const percentage = ((context.parsed / latestTotal) * 100).toFixed(1);
                            return `${context.label}: ${context.parsed.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        };
    } else {
        // For line, bar, and area charts
        chartData = {
            labels: trendLabels,
            datasets: [
                {
                    label: t("Total Students"),
                    data: total,
                    borderColor: "#1976D2",
                    backgroundColor: isArea ? "rgba(25, 118, 210, 0.2)" : "rgba(25, 118, 210, 0.1)",
                    fill: isArea,
                    tension: 0.4,
                    pointBackgroundColor: "#1976D2",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
                {
                    label: t("Male"),
                    data: male,
                    borderColor: "#388E3C",
                    backgroundColor: isArea ? "rgba(56, 142, 60, 0.2)" : "rgba(56, 142, 60, 0.1)",
                    fill: isArea,
                    tension: 0.4,
                    pointBackgroundColor: "#388E3C",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
                {
                    label: t("Female"),
                    data: female,
                    borderColor: "#D32F2F",
                    backgroundColor: isArea ? "rgba(211, 47, 47, 0.2)" : "rgba(211, 47, 47, 0.1)",
                    fill: isArea,
                    tension: 0.4,
                    pointBackgroundColor: "#D32F2F",
                    pointBorderColor: "#ffffff",
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                },
            ],
        };

        chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#1976D2',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        title: function(context) {
                            return `${t('Year')}: ${context[0].label}`;
                        },
                        label: function(context) {
                            return `${context.dataset.label}: ${context.parsed.y.toLocaleString()}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: '#666'
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.1)',
                        drawBorder: false,
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        },
                        color: '#666',
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
            },
            elements: {
                line: {
                    borderWidth: 3,
                },
                point: {
                    hoverBackgroundColor: '#ffffff',
                }
            }
        };
    }

    const chart = new Chart(lineChartCanvas.value, {
        type: isArea ? 'line' : chartType,
        data: chartData,
        options: chartOptions,
    });

    lineChartCanvas.value._chartInstance = chart;
}

// Load everything
onMounted(async () => {
    await fetchData();
    await fetchBarChart();
    await fetchTrends();
    DashboardRepo.fetchUniversities();
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

// Helper functions for card styling
function getCardColor(index) {
    const colors = ['primary', 'success', 'info', 'warning', 'error'];
    return colors[index % colors.length];
}

function getCardSubtitle(index) {
    const subtitles = [
        t('Total enrolled students'),
        t('Newly enrolled this period'),
        t('Successfully graduated'),
        t('Active institutions'),
        t('Students per teacher')
    ];
    return subtitles[index] || '';
}
</script>

<style scoped>
/* Welcome Section */
.text-primary {
    background: linear-gradient(135deg, #1976D2, #42A5F5);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Summary Cards */
.summary-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
    position: relative;
    overflow: hidden;
}

.summary-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #1976D2, #42A5F5);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.summary-card:hover::before {
    transform: scaleX(1);
}

.summary-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
}

.stat-card-1::before { background: linear-gradient(90deg, #1976D2, #42A5F5); }
.stat-card-2::before { background: linear-gradient(90deg, #388E3C, #4CAF50); }
.stat-card-3::before { background: linear-gradient(90deg, #F57C00, #FF9800); }
.stat-card-4::before { background: linear-gradient(90deg, #7B1FA2, #9C27B0); }
.stat-card-5::before { background: linear-gradient(90deg, #D32F2F, #F44336); }

.icon-container {
    position: relative;
    display: inline-block;
}

.icon-container::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 60px;
    height: 60px;
    background: rgba(25, 118, 210, 0.1);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    z-index: -1;
}

/* Chart Cards */
.chart-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.chart-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.chart-container {
    position: relative;
    height: 400px;
    width: 100%;
}

canvas {
    width: 100% !important;
    height: 100% !important;
}

/* Activity Grid */
.activity-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 20px;
}

.activity-card {
    height: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.activity-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.activity-description {
    line-height: 1.6;
    color: rgba(0, 0, 0, 0.7);
    font-size: 0.9rem;
}

/* Filter Section */
.v-card-title {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

/* Responsive Design */
@media (max-width: 1200px) {
    .activity-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    }
    
    .summary-card {
        margin-bottom: 16px;
    }
}

@media (max-width: 768px) {
    .activity-grid {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        height: 300px;
    }
    
    .summary-card {
        margin-bottom: 16px;
    }
}

@media (max-width: 600px) {
    .v-container {
        padding: 16px !important;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .summary-card {
        margin-bottom: 16px;
    }
}

/* Custom scrollbar for activity section */
.activity-grid::-webkit-scrollbar {
    width: 6px;
}

.activity-grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.activity-grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.activity-grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.summary-card,
.chart-card,
.activity-card {
    animation: fadeInUp 0.6s ease-out;
}

/* Staggered animation for cards */
.summary-card:nth-child(1) { animation-delay: 0.1s; }
.summary-card:nth-child(2) { animation-delay: 0.2s; }
.summary-card:nth-child(3) { animation-delay: 0.3s; }
.summary-card:nth-child(4) { animation-delay: 0.4s; }
.summary-card:nth-child(5) { animation-delay: 0.5s; }
</style>
