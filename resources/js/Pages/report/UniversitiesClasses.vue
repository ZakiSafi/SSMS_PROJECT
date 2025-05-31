<template>
    <AppBar :pageTitle="$t('University Classes')" />
    <v-divider :thickness="1" class="border-opacity-100"></v-divider>

    <div class="w-[24rem] pt-6 pb-6 d-flex align-center">
        <v-combobox
            class="mr-4"
            v-model="ReportRepository.date"
            :items="yearRange"
            :label="$t('Select or Type Year')"
            variant="outlined"
            density="compact"
            hide-details
            @update:modelValue="onDateChange"
        ></v-combobox>

        <v-select
            v-model="ReportRepository.shift"
            :items="[$t('day'), $t('night')]"
            :label="$t('Select Shift')"
            variant="outlined"
            hide-details
            density="compact"
            @update:modelValue="onDateChange"
        ></v-select>
    </div>

    <div class="table-container">
        <table class="gender-stats-table">
            <thead>
                <tr>
                    <th rowspan="2">{{ $t('University') }}</th>
                    <th colspan="3">{{ $t('Class') }} 1</th>
                    <th colspan="3">{{ $t('Class') }} 2</th>
                    <th colspan="3">{{ $t('Class') }} 3</th>
                    <th colspan="3">{{ $t('Class') }} 4</th>
                    <th colspan="3">{{ $t('Class') }} 5</th>
                    <th colspan="3">{{ $t('Class') }} 6</th>
                </tr>
                <tr>
                    <!-- Repeat for each class -->
                    <template v-for="n in 6" :key="n">
                        <th>{{ $t('Male') }}</th>
                        <th>{{ $t('Female') }}</th>
                        <th>{{ $t('Total') }}</th>
                    </template>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="(institution, index) in ReportRepository.universityClasses"
                    :key="index"
                >
                    <td>{{ institution.university_name }}</td>

                    <template v-for="classIndex in 6" :key="classIndex">
                        <td class="male">
                            {{
                                institution.classes["Class " + classIndex]?.Total_males || 0
                            }}
                        </td>
                        <td class="female">
                            {{
                                institution.classes["Class " + classIndex]?.Total_Females || 0
                            }}
                        </td>
                        <td class="total">
                            {{
                                institution.classes["Class " + classIndex]?.Total_Students || 0
                            }}
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import { ref, computed, onMounted } from "vue";
import { useI18n } from 'vue-i18n';
import { useReportRepository } from "../../store/ReportRepository";

const { t } = useI18n();
const ReportRepository = useReportRepository();
import persianDate from "persian-date";

const getCurrentPersianYear = () => {
    return new persianDate().year();
};

const currentYear = ref(getCurrentPersianYear());
const yearRange = computed(() => {
    const years = [];
    const startYear = currentYear.value - 10;
    const endYear = currentYear.value + 10;

    for (let i = startYear; i <= endYear; i++) {
        years.push(i);
    }
    return years;
});

const onDateChange = () => {
    ReportRepository.fetchUniversityClasses(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date
    );
};

onMounted(() => {
    ReportRepository.fetchUniversityClasses(
        { page: 1, itemsPerPage: ReportRepository.itemsPerPage },
        ReportRepository.date,
        ReportRepository.shift
    );
});
<<<<<<< HEAD
=======

>>>>>>> e0f3100d89370cb0843e20a21f7e71630bfe5c83
</script>

<style scoped>
.table-container {
    overflow-x: auto;
}

.gender-stats-table {
    width: 100%;
    border-collapse: collapse;
    text-align: center;
}

.gender-stats-table th {
    background-color: #e7f2f5;
    padding: 8px;
    border: 1px solid #ddd;
}

.gender-stats-table td {
    padding: 10px 8px;
    border: 1px solid #ddd;
}

.total {
    font-size: small;
    font-weight: bold;
    color: #333;
    background-color: #f9f9f9;
}

th {
    font-size: small;
    color: #333;
}
</style>
