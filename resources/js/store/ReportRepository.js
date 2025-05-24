import { defineStore } from "pinia";
import { reactive,ref } from "vue";
import { axios } from '../axios';

export const useReportRepository = defineStore("reportRepository", {
  state() {
    return {
      departments: reactive([]),
      search: ref(""),
    }
  },
  actions: {
    async fetchJawad(date=null) {
      try {
        const response = await axios.get(`report/studentsTypeBased?date=${date}`);
        // Transform the data if needed
        this.departments = response.data.map(item => ({
          ...item,
          // Add any transformations here if needed
        }));
      } catch (error) {
        console.error("Error fetching data:", error);
        this.departments = [];
      }
    }
  }
});