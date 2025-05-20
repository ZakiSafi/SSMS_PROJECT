import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';
import { axios } from '../axios';

export const useStudentStatisticRepository = defineStore("StudentStatisticRepository", {
  state() {
    return {
      isEditMode: ref(false),

      search: ref(""),
      statistics: reactive([]),
      statistic: reactive({}),
      departments: reactive([]),
      itemsPerPage: ref(5),
      totalItems: ref(0),
      loading: ref(false),
      createDialog: ref(false),
    };
  },

  actions: {
    async fetchStatistics({ page, itemsPerPage }) {
      this.loading = true;
      try {
        const response = await axios.get(
          `studentStatistics?page=${page}&perPage=${itemsPerPage}&name=${this.search}`
        );
        this.statistics = response.data.data;
        this.totalItems = response.data.meta.total;
      } catch (error) {
        console.error("Failed to fetch student statistics:", error);
      } finally {
        this.loading = false;
      }
    },

    async fetchStatistic(id) {
      try {
        const response = await axios.get(`studentStatistics/${id}`);
        this.statistic = response.data.data;
      } catch (error) {
        console.error("Failed to fetch student statistic:", error);
      }
    },

    async createStatistic(data) {
      try {
        await axios.post("studentStatistics", data);
        this.createDialog = false;
        await this.fetchStatistics({ page: 1, itemsPerPage: this.itemsPerPage });
      } catch (error) {
        console.error("Failed to create statistic:", error);
      }
    },

    async updateStatistic(id, data) {
      try {
        await axios.put(`studentStatistics/${id}`, data);
        this.createDialog = false;
        await this.fetchStatistics({ page: 1, itemsPerPage: this.itemsPerPage });
      } catch (error) {
        console.error("Failed to update statistic:", error);
      }
    },

    async deleteStatistic(id) {
      try {
        await axios.delete(`studentStatistics/${id}`);
        await this.fetchStatistics({ page: 1, itemsPerPage: this.itemsPerPage });
      } catch (error) {
        console.error("Failed to delete statistic:", error);
      }
    },

    async fetchDepartments() {
      try {
        const response = await axios.get(`departments`);
        this.departments = response.data.data;
      } catch (error) {
        console.error("Failed to fetch departments:", error);
      }
  },
  }
});
