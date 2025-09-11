import { defineStore } from "pinia";
import { ref } from "vue";
import { axios } from "../axios";
import { useNotificationStore } from "./notification";

export const useUniversityRepository = defineStore(
    "universityRepository",
    () => {
        // State
        const isEditMode = ref(false);
        const loading = ref(false);
        const createDialog = ref(false);
        const uniSearch = ref("");
        const universities = ref([]);
        const university = ref(null); // Changed to ref
        const provinces = ref([]);
        const faculties = ref([]);
        const totalItems = ref(0);
        const itemsPerPage = ref(5);

        // Actions
        const FetchUniversities = async ({ page, itemsPerPage }) => {
            loading.value = true;
            try {
                const response = await axios.get(
                    `universities?page=${page}&perPage=${itemsPerPage}&name=${uniSearch.value}`
                );
                universities.value = response.data.data;
                totalItems.value = response.data.meta.total;
            } catch (error) {
                console.error("Error fetching universities:", error);
            } finally {
                loading.value = false;
            }
        };

        const FetchUniversity = async (id) => {
            loading.value = true;
            try {
                const response = await axios.get(`universities/${id}`);
                // Make sure the response includes faculties with their IDs
                university.value = {
                    ...response.data.data,
                    faculties: response.data.data.faculties || [],
                };
                return university.value;
            } catch (error) {
                console.error("Error fetching university:", error);
                throw error;
            } finally {
                loading.value = false;
            }
        };

        const CreateUniversity = async (formData) => {
            loading.value = true;
            try {
                await axios.post("universities", formData);
                createDialog.value = false;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "University created successfully",
                    "success"
                );
                await FetchUniversities({
                    page: 1,
                    itemsPerPage: itemsPerPage.value,
                });
            } catch (error) {
                const serverMessage = error.response?.data?.message;
                const firstValidationError = Array.isArray(
                    error.response?.data?.errors
                )
                    ? error.response.data.errors[0]
                    : error.response?.data?.errors &&
                      typeof error.response.data.errors === "object"
                    ? Object.values(error.response.data.errors).flat()[0]
                    : null;
                const message =
                    firstValidationError || serverMessage || error.message;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    `Failed to create university: ${message}`,
                    "error"
                );
                throw error;
            } finally {
                loading.value = false;
            }
        };

        const UpdateUniversity = async (id, data) => {
            loading.value = true;
            try {
                await axios.put(`universities/${id}`, data);
                createDialog.value = false;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "University updated successfully",
                    "success"
                );
                await FetchUniversities({
                    page: 1,
                    itemsPerPage: itemsPerPage.value,
                });
            } catch (error) {
                const serverMessage = error.response?.data?.message;
                const firstValidationError = Array.isArray(
                    error.response?.data?.errors
                )
                    ? error.response.data.errors[0]
                    : error.response?.data?.errors &&
                      typeof error.response.data.errors === "object"
                    ? Object.values(error.response.data.errors).flat()[0]
                    : null;
                const message =
                    firstValidationError || serverMessage || error.message;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    `Failed to update university: ${message}`,
                    "error"
                );
                throw error;
            } finally {
                loading.value = false;
            }
        };

        const DeleteUniversity = async (id) => {
            loading.value = true;
            try {
                await axios.delete(`universities/${id}`);
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "University deleted successfully",
                    "success"
                );
                await FetchUniversities({
                    page: 1,
                    itemsPerPage: itemsPerPage.value,
                });
            } catch (error) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to delete university: " +
                        (error.response?.data?.message || error.message),
                    "error"
                );
                throw error;
            } finally {
                loading.value = false;
            }
        };

        const FetchProvinces = async () => {
            try {
                const response = await axios.get("provinces");
                provinces.value = response.data.data;
            } catch (error) {
                console.error("Error fetching provinces:", error);
            }
        };

        const FetchFaculties = async () => {
            try {
                const response = await axios.get("faculties");
                faculties.value = response.data.data;
            } catch (error) {
                console.error("Error fetching faculties:", error);
            }
        };

        return {
            // State
            isEditMode,
            loading,
            createDialog,
            uniSearch,
            universities,
            university,
            provinces,
            faculties,
            totalItems,
            itemsPerPage,

            // Actions
            FetchUniversities,
            FetchUniversity,
            CreateUniversity,
            UpdateUniversity,
            DeleteUniversity,
            FetchProvinces,
            FetchFaculties,
        };
    }
);
