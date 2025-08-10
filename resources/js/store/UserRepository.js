import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";
import { useRouter } from "vue-router";

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
            this.loading = true;
            const response = await axios.get(
                `users?page=${page}&perPage=${itemsPerPage}&name=${this.userSearch}`
            );
            this.users = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },

        async fetchUser(id) {
            try {
                const response = await axios.get(`users/${id}`);
                this.user = response.data.data;
                console.log(this.user);
            } catch (err) {
                console.error("Failed to fetch user:", err);
            }
        },

        async createUser(formData) {
            try {
                const config = {
                    method: "POST",
                    url: "users",
                    data: formData,
                };
                await axios(config);
                this.createDialog = false;
                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Failed to create user:", err);
            }
        },

        async updateUser(id, formData) {
            try {
                const config = {
                    method: "PUT",
                    url: `users/${id}`,
                    data: formData,
                };
                await axios(config);
                this.createDialog = false;
                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Failed to update user:", err);
            }
        },

        async deleteUser(id) {
            try {
                const config = {
                    method: "DELETE",
                    url: `users/${id}`,
                };
                await axios(config);
                this.fetchUsers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Failed to delete user:", err);
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
        async fetchRoless() {
            try {
                const response = await axios.get("role");
                this.roles = response.data.data;
            } catch (err) {
                // handle error if needed
            }
        },

       

         
    },
});
