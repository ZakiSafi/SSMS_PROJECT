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
                />
            </v-col>
            <v-col cols="12" md="4">
                <v-select
                    v-model="filters.season"
                    :items="seasons"
                    label="Season"
                    dense
                />
            </v-col>
            <v-col cols="12" md="4">
                <v-select
                    v-model="filters.university"
                    :items="universities"
                    label="University"
                    dense
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

        <!-- Recent Reports -->
        <v-card class="mt-6 pa-4 elevation-1">
            <div class="text-subtitle-1 mb-2">Recent Reports</div>
            <v-skeleton-loader type="list-item-two-line" :loading="true" />
        </v-card>
    </v-container>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import AppBar from "@/components/AppBar.vue";

Chart.register(...registerables);

// Filters
const filters = ref({
    year: 2024,
    season: "Spring",
    university: "All",
});
const years = [2024, 2023, 2022];
const seasons = ["Spring", "Autumn"];
const universities = ["All", "Kabul University", "Herat University"];

// Summary Stats
const summaryStats = ref([
    { title: "Total Students", value: "10,245", icon: "mdi-school" },
    { title: "Total Teachers", value: "1,152", icon: "mdi-account-tie" },
    { title: "Graduated Students", value: "2,430", icon: "mdi-school-outline" },
    { title: "Universities", value: "15", icon: "mdi-office-building" },
]);

// Chart Refs
const barChartCanvas = ref(null);
const lineChartCanvas = ref(null);

// Init Charts
onMounted(() => {
    new Chart(barChartCanvas.value, {
        type: "bar",
        data: {
            labels: ["CS", "IT", "Math", "Physics", "English"],
            datasets: [
                {
                    label: "Students",
                    data: [200, 300, 150, 180, 100],
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

    new Chart(lineChartCanvas.value, {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr"],
            datasets: [
                {
                    label: "Enrolled",
                    data: [100, 200, 180, 220],
                    borderColor: "#42A5F5",
                    fill: false,
                },
                {
                    label: "Graduated",
                    data: [90, 160, 150, 210],
                    borderColor: "#66BB6A",
                    fill: false,
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
});
</script>

<style scoped>
canvas {
    width: 100% !important;
    height: 300px !important;
}
</style>
