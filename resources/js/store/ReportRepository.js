import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from '../axios';

// Utility delay function
function wait(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

export const useReportRepository = defineStore("reportRepository", {
  state() {
    const currentYear = new Date().getFullYear();
    return {
      departments: reactive([]),
      search: ref(""),
      date: ref(currentYear),
      loading: ref(false),
    }
  },
  actions: {
    async fetchJawad(date = new Date().getFullYear()) {
      this.loading = true;
      await wait(400); 

      try {
        const response = await axios.get(`report/studentsTypeBased?year=${date}`);
        this.departments = response.data.map(item => ({
          ...item,
        }));
      } catch (error) {
        console.error("Error fetching data:", error);
        this.departments = [];
      } finally {
        this.loading = false;
      }
    }
  }
});
