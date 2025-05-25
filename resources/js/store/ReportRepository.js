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
      totalItems: ref(0),
      selectedItems: ref([]),
      itemsPerPage: ref(5),
    }
  },
  actions: {
    async fetchJawad({ page, itemsPerPage },date = new Date().getFullYear(),) {
      this.loading = true;

      try {
        const response = await axios.get(`report/university?year=${date}&page=${page}&perPage=${itemsPerPage}`);
        this.departments = response.data.data;
         this.totalItems = response.data.total
      } catch (error) {
        console.error("Error fetching data:", error);
        this.departments = [];
      } finally {
        this.loading = false;
      }
    }
  }
});
