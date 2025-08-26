import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";
import { useRouter } from "vue-router";
import { useNotificationStore } from "@/store/notification"; // Add this import

export const useUserRepository = defineStore("userRepository", {
    state() {
        return {
            isEditMode: ref(false),
            search: ref(""),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            itemsPerPage: ref(5),
            router: useRouter(),
            createDialog: ref(false),
            userSearch: ref(""),
            users: reactive([]),
            universities: reactive([]),
            user: reactive({}),
            role: reactive({}),
            roles: reactive([]),
            permissions: reactive([]),
            logs: reactive([]),
        };
    },

    actions: {
        async fetchUsers({ page, itemsPerPage }) {
            try {
                this.loading = true;
                const response = await axios.get(
                    `users?page=${page}&perPage=${itemsPerPage}&name=${this.userSearch}`
                );
                this.users = response.data.data;
                this.totalItems = response.data.meta.total;
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to fetch users: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
            } finally {
                this.loading = false;
            }
        },

        async fetchUser(id) {
            try {
                const response = await axios.get(`users/${id}`);
                this.user = response.data.data;
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to fetch user: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
            }
        },

        async createUser(formData) {
            try {
                this.loading = true;
                const config = {
                    method: "POST",
                    url: "users",
                    data: formData,
                };
                const response = await axios(config);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "User created successfully",
                    "success"
                );

                this.createDialog = false;
                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
                return response.data;
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to create user: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
                throw err; // Re-throw to handle in component if needed
            } finally {
                this.loading = false;
            }
        },

        async updateUser(id, formData) {
            try {
                this.loading = true;
                const config = {
                    method: "PUT",
                    url: `users/${id}`,
                    data: formData,
                };
                const response = await axios(config);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "User updated successfully",
                    "success"
                );

                this.createDialog = false;
                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
                return response.data;
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to update user: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
                throw err;
            } finally {
                this.loading = false;
            }
        },

        async deleteUser(id) {
            try {
                this.loading = true;
                const config = {
                    method: "DELETE",
                    url: `users/${id}`,
                };
                await axios(config);

                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "User deleted successfully",
                    "success"
                );

                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to delete user: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
            } finally {
                this.loading = false;
            }
        },

        async fetchUniversities() {
            try {
                const response = await axios.get("universities");
                this.universities = response.data.data;
            } catch (err) {
                // handle error if needed
            }
        },
        async fetchRoles() {
            try {
                const response = await axios.get("role");
                this.roles = response.data.data;
            } catch (err) {
                // handle error if needed
            }
        },

        async fetchLogs({ page, itemsPerPage }) {
            try {
                this.loading = true;
                const response = await axios.get(
                    `logs?page=${page}&perPage=${itemsPerPage}&name=${this.search}`
                );
                this.logs = response.data.data;
                this.totalItems = response.data.meta.total;
            } catch (err) {
                const notificationStore = useNotificationStore();
                notificationStore.showNotification(
                    "Failed to fetch users: " +
                        (err.response?.data?.message || err.message),
                    "error"
                );
            } finally {
                this.loading = false;
            }
        },
    },
});
