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

            <!-- 5 filters in a single row (wrap on small screens) -->
            <v-row dense class="pa-4">
                <v-col class="col-1-5" cols="12" sm="6">
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

                <v-col class="col-1-5" cols="12" sm="6">
                    <v-select
                        v-model="filters.university_type"
                        :items="universityTypeOptions"
                        item-title="label"
                        item-value="value"
                        :label="$t('university_type')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>

                <v-col class="col-1-5" cols="12" sm="6">
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

                <v-col class="col-1-5" cols="12" sm="6">
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

                <v-col class="col-1-5" cols="12" sm="6">
                    <v-select
                        v-model="filters.shift"
                        :items="shiftOptions"
                        item-title="label"
                        item-value="value"
                        :label="$t('Shift')"
                        density="comfortable"
                        @update:modelValue="handleTopFiltersChange"
                    />
                </v-col>
            </v-row>
        </v-card>

        <!-- Summary Cards (5 in one row) -->
        <v-row class="mt-4">
            <v-col
                v-for="(stat, index) in summaryStats"
                :key="index"
                class="col-1-5"
                cols="12"
                sm="6"
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
            <!-- Students per Faculty/Department -->
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
                                :items="breakdownLevelOptions"
                                item-title="label"
                                item-value="value"
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
                                :items="seasonOptions"
                                item-title="label"
                                item-value="value"
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

            <!-- Gender Distribution -->
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
                                :items="seasonOptions"
                                item-title="label"
                                item-value="value"
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

        <!-- Student Trends (modern area-spline with gradients) -->
        <v-row class="mt-6">
            <v-col cols="12">
                <v-card class="elevation-1">
                    <v-toolbar density="comfortable" flat>
                        <v-toolbar-title>
                            <v-icon class="mr-2">mdi-chart-areaspline</v-icon>
                            {{ $t("student_trends") }}
                        </v-toolbar-title>
                        <v-spacer></v-spacer>
                        <div class="d-flex" style="gap: 8px">
                            <v-select
                                v-model="trendFilters.university_type"
                                :items="universityTypeOptions"
                                item-title="label"
                                item-value="value"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('university_type')"
                                @update:modelValue="fetchTrends"
                            />
                            <v-select
                                v-model="trendFilters.province_id"
                                :items="provincesWithAll"
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
                                :items="timeRangeOptions"
                                item-title="label"
                                item-value="value"
                                density="compact"
                                hide-details
                                style="max-width: 160px"
                                :label="$t('range')"
                                @update:modelValue="fetchTrends"
                            />
                            <v-select
                                v-model="trendFilters.group_by"
                                :items="groupByOptions"
                                item-title="label"
                                item-value="value"
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
                                        getUserInitials(log.user?.name)
                                    }}</span>
                                </v-avatar>
                                <div>
                                    <strong>{{ log.user?.name }}</strong>
                                    <div class="text-caption">
                                        {{ $t("table_name") }}:
                                        {{ log.table_name }}
                                    </div>
                                </div>
                            </div>
                            <v-chip
                                small
                                :color="getActionColor(log.action_type)"
                                text-color="white"
                            >
                                {{ (log.action_type || "").toUpperCase() }}
                            </v-chip>
                        </div>

                        <div class="mb-2">
                            <v-icon small class="mr-1">mdi-pound</v-icon>
                            <span
                                >{{ $t("record_id") }}:
                                {{ log.record_id || "—" }}</span
                            >
                        </div>

                        <div class="activity-description">
                            {{
                                log.action_description ||
                                $t("no_data_available")
                            }}
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
                                <v-icon small class="mr-1">mdi-earth</v-icon>
                                {{ log.ip_address || "—" }}
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
import { ref, onMounted, computed, watch, nextTick } from "vue";
import { useI18n } from "vue-i18n";
import { Chart, registerables } from "chart.js";
import AppBar from "@/components/AppBar.vue";
import { useDashboardRepository } from "../store/DashboardRepository";

Chart.register(...registerables);

const { t, locale } = useI18n();
const DashboardRepo = useDashboardRepository();

// Refs
const barChartCanvas = ref(null);
const lineChartCanvas = ref(null);
const genderChartCanvas = ref(null);
const loadingActivity = ref(false);
const recentActivities = ref([]);

// Years array (static)
const years = [1400, 1401, 1402, 1403, 1404, 1405, 1406];

// Computed option lists — make them depend on locale so they re-evaluate on language change
const yearsWithAll = computed(() => {
    // make dependency on locale explicit
    void locale.value;
    return [
        { label: t("all_years"), value: null },
        ...years.map((y) => ({ label: String(y), value: y })),
    ];
});

const seasonOptions = computed(() => {
    void locale.value;
    return [
        { label: t("all"), value: "all" },
        { label: t("spring"), value: "spring" },
        { label: t("autumn"), value: "autumn" },
    ];
});

const universityTypeOptions = computed(() => {
    void locale.value;
    return [
        { label: t("all"), value: "all" },
        { label: t("public"), value: "public" },
        { label: t("private"), value: "private" },
    ];
});

const shiftOptions = computed(() => {
    void locale.value;
    return [
        { label: t("day"), value: "day" },
        { label: t("night"), value: "night" },
    ];
});

const breakdownLevelOptions = computed(() => {
    void locale.value;
    return [
        { label: t("faculty"), value: "faculty" },
        { label: t("department"), value: "department" },
    ];
});

const timeRangeOptions = computed(() => {
    void locale.value;
    return [
        { label: `5 ${t("years")}`, value: "5years" },
        { label: `10 ${t("years")}`, value: "10years" },
        { label: t("all"), value: "all" },
    ];
});

const groupByOptions = computed(() => {
    void locale.value;
    return [
        { label: t("year"), value: "year" },
        { label: t("season"), value: "season" },
    ];
});

// Provinces and universities computed lists (depend on DashboardRepo data and locale)
const provincesWithAll = computed(() => {
    void locale.value;
    return [
        { id: null, name: t("all_provinces") },
        ...(DashboardRepo.provinces || []),
    ];
});
const universitiesWithAll = computed(() => {
    void locale.value;
    return [
        { id: null, name: t("all_universities") },
        ...(DashboardRepo.universities || []),
    ];
});

// Global filters
const filters = ref({
    year: null,
    season: "all",
    university_type: "all",
    province_id: null,
    university_id: null,
    shift: "day",
    breakdown_level: "faculty",
});

// Chart-specific filters
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
    province_id: null, // includes "All provinces"
    time_range: "10years",
    group_by: "year",
    season: filters.value.season,
});

// Summary Cards (computed to depend on locale via t inside)
const summaryStats = computed(() => {
    void locale.value;
    return [
        {
            title: t("total_students"),
            value: DashboardRepo.summaryData.total_students,
            icon: "mdi-school",
        },
        {
            title: t("new_students"),
            value: DashboardRepo.summaryData.new_students,
            icon: "mdi-account-plus",
        },
        {
            title: t("graduated_students"),
            value: DashboardRepo.summaryData.graduated_students,
            icon: "mdi-school-outline",
        },
        {
            title: t("universities"),
            value: DashboardRepo.summaryData.universities_count?.total,
            icon: "mdi-office-building",
        },
        {
            title: t("student_teacher_ratio"),
            value: DashboardRepo.summaryData.student_teacher_ratio,
            icon: "mdi-account-group-outline",
        },
    ];
});

// Fetch functions
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
    // summary uses global filters now
    await DashboardRepo.fetchSummaryData(buildTopFilterParams());
    await fetchRecentActivity();
}

async function handleTopFiltersChange() {
    await fetchData();
    await fetchBarChart();
    await fetchGenderChart();
    await fetchTrends();
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

    if (barChartCanvas.value?._chartInstance) {
        barChartCanvas.value._chartInstance.destroy();
    }

    // make sure canvas exists
    await nextTick();

    const chart = new Chart(barChartCanvas.value, {
        type: "bar",
        data: {
            labels,
            datasets: [
                {
                    label: t("total_students"),
                    data,
                    backgroundColor: "#42A5F5",
                    borderRadius: 6,
                    barPercentage: 0.8,
                    categoryPercentage: 0.8,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { ticks: { autoSkip: true, maxRotation: 0 } },
                y: { beginAtZero: true },
            },
            plugins: {
                legend: { display: true },
                tooltip: { mode: "index", intersect: false },
            },
        },
    });

    barChartCanvas.value._chartInstance = chart;
}

async function fetchTrends() {
    await DashboardRepo.fetchFacultyTrends(trendFilters.value);

    const labels = DashboardRepo.trends.map((item) => item.year);
    const total = DashboardRepo.trends.map((item) => Number(item.total || 0));
    const male = DashboardRepo.trends.map((item) => Number(item.male || 0));
    const female = DashboardRepo.trends.map((item) => Number(item.female || 0));

    if (lineChartCanvas.value?._chartInstance) {
        lineChartCanvas.value._chartInstance.destroy();
    }

    // ensure DOM ready
    await nextTick();

    const ctx = lineChartCanvas.value.getContext("2d");
    const gradBlue = ctx.createLinearGradient(0, 0, 0, 300);
    gradBlue.addColorStop(0, "rgba(66,165,245,0.35)");
    gradBlue.addColorStop(1, "rgba(66,165,245,0.02)");

    const gradCyan = ctx.createLinearGradient(0, 0, 0, 300);
    gradCyan.addColorStop(0, "rgba(41,182,246,0.35)");
    gradCyan.addColorStop(1, "rgba(41,182,246,0.02)");

    const gradPink = ctx.createLinearGradient(0, 0, 0, 300);
    gradPink.addColorStop(0, "rgba(236,64,122,0.35)");
    gradPink.addColorStop(1, "rgba(236,64,122,0.02)");

    const chart = new Chart(lineChartCanvas.value, {
        type: "line",
        data: {
            labels,
            datasets: [
                {
                    label: t("total_students"),
                    data: total,
                    borderColor: "#42A5F5",
                    backgroundColor: gradBlue,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                },
                {
                    label: t("male"),
                    data: male,
                    borderColor: "#29B6F6",
                    backgroundColor: gradCyan,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                },
                {
                    label: t("female"),
                    data: female,
                    borderColor: "#EC407A",
                    backgroundColor: gradPink,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 2,
                    pointHoverRadius: 4,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            stacked: false,
            plugins: {
                legend: { position: "top" },
                tooltip: {
                    usePointStyle: true,
                    callbacks: {
                        label: (ctx) =>
                            `${ctx.dataset.label}: ${ctx.formattedValue}`,
                    },
                },
            },
            scales: {
                x: { ticks: { autoSkip: true, maxRotation: 0 } },
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

    const labels =
        (DashboardRepo.genderDistribution.data || []).map((d) => d.gender) ||
        [];
    const data =
        (DashboardRepo.genderDistribution.data || []).map((d) =>
            Number(d.count || 0)
        ) || [];

    if (genderChartCanvas.value?._chartInstance) {
        genderChartCanvas.value._chartInstance.destroy();
    }

    await nextTick();

    const chart = new Chart(genderChartCanvas.value, {
        type: "doughnut",
        data: {
            labels,
            datasets: [
                {
                    data,
                    backgroundColor: ["#29B6F6", "#EC407A"],
                    hoverOffset: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: "bottom" },
            },
        },
    });

    genderChartCanvas.value._chartInstance = chart;
}

// When locale changes, re-render computed labels and recreate charts so labels/legends update
watch(locale, async () => {
    // small tick to allow computed updates to propagate
    await nextTick();
    // re-render charts and summary (summaryStats is computed so UI updates automatically)
    await fetchBarChart();
    await fetchGenderChart();
    await fetchTrends();
});

// Load everything
onMounted(async () => {
    await fetchData();
    await fetchBarChart();
    await fetchGenderChart();
    await fetchTrends();
    // also populate select lists
    DashboardRepo.fetchUniversities();
    DashboardRepo.fetchProvinces();
});

// Helpers
function parseServerUtc(dateString) {
    // Expect format like "YYYY-MM-DD HH:mm:ss" (no timezone). Treat as UTC.
    // Fallback to native Date if parsing fails.
    try {
        const [datePart, timePart] = String(dateString).split(" ");
        const [y, m, d] = datePart.split("-").map(Number);
        const [hh = 0, mm = 0, ss = 0] = (timePart || "0:0:0")
            .split(":")
            .map(Number);
        const utcMs = Date.UTC(y, (m || 1) - 1, d || 1, hh, mm, ss);
        return new Date(utcMs);
    } catch (e) {
        return new Date(dateString);
    }
}

function formatDate(dateString) {
    const dateUtc = parseServerUtc(dateString);
    // Render in Asia/Kabul timezone
    return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
        timeZone: "Asia/Kabul",
    }).format(dateUtc);
}

function formatTimeAgo(dateString) {
    const dateUtc = parseServerUtc(dateString);
    const nowMs = Date.now(); // current time in ms UTC
    const diff = Math.floor((nowMs - dateUtc.getTime()) / 1000);

    if (diff < 60) return t("just_now") || "Just now";
    if (diff < 3600)
        return `${Math.floor(diff / 60)} ${t("minutes_ago") || "minutes ago"}`;
    if (diff < 86400)
        return `${Math.floor(diff / 3600)} ${t("hours_ago") || "hours ago"}`;
    return `${Math.floor(diff / 86400)} ${t("days_ago") || "days ago"}`;
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
/* 5-items-per-row helper: on large screens force 20% width columns */
.col-1-5 {
    flex: 1 1 100%;
    max-width: 100%;
}
@media (min-width: 1280px) {
    .col-1-5 {
        flex: 0 0 20%;
        max-width: 20%;
    }
}

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
