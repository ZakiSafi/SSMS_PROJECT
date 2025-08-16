import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";
import { useNotificationStore } from "@/store/notification";

export const useProvinceRepository = defineStore("provinceRepository", {
    state() {
        return {
            isEditMode: ref(false),
            search: ref(""),
            serverItems: ref([]),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            selectedItems: ref([]),
            itemsPerPage: ref(5),
            createDialog: ref(false),
            provinceSearch: ref(""),
            provinces: reactive([]),
            province: reactive({}),
        };
    },

    actions: {
        async FetchProvinces(params = { page: 1, itemsPerPage: 10 }) {
            this.loading = true;
            try {
                const response = await axios.get(
                    `provinces?page=${params.page}&perPage=${params.itemsPerPage}&name=${this.provinceSearch}`
                );
                this.provinces = response.data.data;
                this.totalItems = response.data.meta.total;
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to fetch provinces: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async FetchProvince(id) {
            try {
                const response = await axios.get(`provinces/${id}`);
                this.province = response.data.data;
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to fetch province: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            }
        },

        async CreateProvince(formData) {
            try {
                this.loading = true;
                const response = await axios.post("provinces", formData);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Province created successfully",
                    "success"
                );

                this.createDialog = false;
                await this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
                return response.data;
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to create province: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async UpdateProvince(id, data) {
            try {
                this.loading = true;
                const response = await axios.put(`provinces/${id}`, data);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Province updated successfully",
                    "success"
                );

                this.createDialog = false;
                await this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
                return response.data;
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to update province: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async DeleteProvince(id) {
            try {
                this.loading = true;
                await axios.delete(`provinces/${id}`);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Province deleted successfully",
                    "success"
                );

                await this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to delete province: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            } finally {
                this.loading = false;
            }
        },
    },
});
