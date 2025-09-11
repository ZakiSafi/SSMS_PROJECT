import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";
import { useNotificationStore } from "./notification";

export const useDepartmentRepository = defineStore("departmentRepository", {
    state() {
        return {
            isEditMode: ref(false),
            search: ref(""),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            itemsPerPage: ref(5),
            createDialog: ref(false),
            departmentSearch: ref(""),
            departments: reactive([]),
            department: reactive({}),
            faculties: reactive([]),
        };
    },

    actions: {
        async FetchDepartments({ page, itemsPerPage }) {
            this.loading = true;
            const response = await axios.get(
                `departments?page=${page}&perPage=${itemsPerPage}&name=${this.departmentSearch}`
            );
            this.departments = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },
        async FetchDepartment(id) {
            try {
                const response = await axios.get(`departments/${id}`);
                this.department = response.data.data;
                console.log(this.department);
            } catch (err) {
                // handle error if needed
            }
        },
        async CreateDepartment(formData) {
            try {
                const config = {
                    method: "POST",
                    url: "departments",
                    data: formData,
                };
                await axios(config);
                this.createDialog = false;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Department created successfully",
                    "success"
                );
                this.FetchDepartments({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                const serverMessage = err.response?.data?.message;
                const firstValidationError = Array.isArray(
                    err.response?.data?.errors
                )
                    ? err.response.data.errors[0]
                    : err.response?.data?.errors &&
                      typeof err.response.data.errors === "object"
                    ? Object.values(err.response.data.errors).flat()[0]
                    : null;
                const message =
                    firstValidationError || serverMessage || err.message;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    `Failed to create department: ${message}`,
                    "error"
                );
            }
        },

        async UpdateDepartment(id, formData) {
            try {
                const config = {
                    method: "PUT",
                    url: `departments/${id}`,
                    data: formData,
                };
                await axios(config);
                this.createDialog = false;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Department updated successfully",
                    "success"
                );
                this.FetchDepartments({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                const serverMessage = err.response?.data?.message;
                const firstValidationError = Array.isArray(
                    err.response?.data?.errors
                )
                    ? err.response.data.errors[0]
                    : err.response?.data?.errors &&
                      typeof err.response.data.errors === "object"
                    ? Object.values(err.response.data.errors).flat()[0]
                    : null;
                const message =
                    firstValidationError || serverMessage || err.message;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    `Failed to update department: ${message}`,
                    "error"
                );
            }
        },

        async DeleteDepartment(id) {
            try {
                const config = {
                    method: "DELETE",
                    url: `departments/${id}`,
                };
                await axios(config);
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Department deleted successfully",
                    "success"
                );
                this.FetchDepartments({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                const serverMessage = err.response?.data?.message;
                const firstValidationError = Array.isArray(
                    err.response?.data?.errors
                )
                    ? err.response.data.errors[0]
                    : err.response?.data?.errors &&
                      typeof err.response.data.errors === "object"
                    ? Object.values(err.response.data.errors).flat()[0]
                    : null;
                const message =
                    firstValidationError || serverMessage || err.message;
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    `Failed to delete department: ${message}`,
                    "error"
                );
            }
        },

        async FetchFaculties() {
            try {
                const response = await axios.get("faculties");
                this.faculties = response.data.data;
            } catch (err) {
                console.error("Failed to fetch faculties:", err);
            }
        },
    },
});
