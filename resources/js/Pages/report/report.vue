<template>
    <AppBar pageTitle="Report"/>
    <v-divider :thickness="1" class="border-opacity-100 "></v-divider>
    <table class="styled-table">
        <thead>
          <tr>
            <th v-for="(header, key) in departments" :key="key">{{ header }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in departments" :key="index">
            <td
              v-for="(header, key) in departments"
              :key="key"
              
            >
              {{ item[key] }}
            </td>
          </tr>
        </tbody>
      </table>
</template>
<script setup>
import { onMounted } from "vue";
import AppBar from "@/components/AppBar.vue";
import { useReportRepository } from "../../store/ReportRepository";

const ReportRepository=useReportRepository;

onMounted (()=>{
    ReportRepository.fetchJawad();

})

const departments=ReportRepository.departments

</script>