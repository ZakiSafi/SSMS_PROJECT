import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from '../axios';
import persianDate from 'persian-date';

export const useReportRepository = defineStore("reportRepository", {
  state() {
    return {
      departments: reactive([]),
      search: ref(""),
      date: ref(new persianDate().year().toString()), 
      loading: ref(false),
      totalItems: ref(0),
      selectedItems: ref([]),
      itemsPerPage: ref(5),
    }
  },
  actions: {
    async fetchJawad({ page, itemsPerPage },date = this.date) {
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
