// src/store/AuthRepository.js
import { defineStore } from "pinia";
import { ref, reactive } from "vue";
import { axios } from "../axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useRouter } from "vue-router";

export const useAuthRepository = defineStore("authRepository", {
    state: () => ({
        user: {},            // reactive object
        permissions: reactive([]),    // reactive array
        loading: false,
        error: null,
        isAuthenticated: false,
        router: useRouter(),
        createDialog: ref(false),
        isEditMode: ref(false),
        search: ref(""),
        role: reactive({}),
    }),

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;

            try {
                // 1. Login request
                const response = await axios.post("login", credentials);

                const { user, token, role, permissions } = response.data;

                // Save state
                this.user = user;
                this.role = role;
                this.permissions = permissions;
                this.isAuthenticated = true;

                // Save to sessionStorage
                sessionStorage.setItem("token", token);
                sessionStorage.setItem("user", JSON.stringify(user));
                sessionStorage.setItem("role", JSON.stringify(role));
                sessionStorage.setItem(
                    "permissions",
                    JSON.stringify(permissions)
                );

                // Set token for axios
                axios.defaults.headers.common[
                    "Authorization"
                ] = `Bearer ${token}`;

                this.refreshPermissions();

                toast.success("Login successful!", {
                    position: "top-right",
                    autoClose: 500,
                });

                setTimeout(() => {
                    this.router.push("/dashboard");
                }, 500); //
            } catch (error) {
                toast.error("Login failed! Please check your credentials.", {
                    position: "top-right",
                    autoClose: 500,
                });

                this.error = error.response
                    ? error.response.data.message
                    : "An error occurred!";
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.error = null;
            try {
                await axios.post("logout", {
                    token: sessionStorage.getItem("token"),
                });

                // Clear storage
                sessionStorage.clear();
                this.user = {};
                this.role = null;
                this.permissions = [];
                this.isAuthenticated = false;

                toast.success("Logout successful!", {
                    position: "top-right",
                    autoClose: 500,
                });

                setTimeout(() => {
                    this.router.push("/");
                }, 500); // Redirect to login after 1 second
            } catch (error) {
                toast.error("Logout failed! Please try again.", {
                    position: "top-right",
                    autoClose: 500,
                });

                this.error = error.response
                    ? error.response.data.message
                    : "An error occurred!";
            } finally {
                this.loading = false;
            }
            if (storedRole) this.role = JSON.parse(storedRole);
        },

       async fetchRoless() {
            try {
                const response = await axios.get("role");
                this.roles = response.data.data;
            } catch (err) {
                // handle error if needed
            }
        },

        async fetchRoles({ page, itemsPerPage }) {
            try {
                this.loading = true;
                const response = await axios.get(
                    `role?page=${page}&perPage=${itemsPerPage}&name=${this.search}`
                );
                this.roles = response.data.data;
                this.totalItems = response.data.meta.total;
                this.loading = false;
            } catch (err) {
                console.error("Failed to fetch roles:", err);
            }
        },

        async fetchRole(id) {
            try {
                const response = await axios.get(`role/${id}`);
                this.role = response.data.data;
            } catch (err) {
                console.error("Failed to fetch role:", err);
            }
        },

        async createRole(formData) {
            try {
                this.loading = true;
                const config = {
                    method: "POST",
                    url: "role",
                    data: formData,
                };

                // Wait for creation to complete
                await axios(config);

                // Reset to first page to see the new role

                this.router.push("/role-permission");
                // Refresh the list
            } catch (err) {
                console.error("Failed to create role:", err);
            } finally {
                this.loading = false;
            }
        },

        async deleteRole(id) {
            try {
                const config = {
                    method: "DELETE",
                    url: `role/${id}`,
                };
                await axios(config);
                this.fetchRoles({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Failed to delete role:", err);
            }
        },
        async updateRole(id, formData) {
            try {
                const config = {
                    method: "PUT",
                    url: `role/${id}`,
                    data: formData,
                };
                await axios(config);
                

                this.createDialog = false;
                this.fetchRoles({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
                this.refreshPermissions();
            } catch (err) {
                console.error("Failed to update user:", err);
            }
        },

        async refreshPermissions() {
            const meResponse = await axios.get("/me");
            const permissions = meResponse.data.data.permissions;
            console.log("Permissions:", permissions);
            const role = meResponse.data.data.role;

            sessionStorage.setItem("permissions", JSON.stringify(permissions));
            sessionStorage.setItem("role", JSON.stringify(role));

            this.permissions = permissions;
            this.role = role;
            this.user = meResponse.data;

            sessionStorage.setItem("user", JSON.stringify(this.user));
        },



        loadFromSession() {
            const storedUser = sessionStorage.getItem("user");
            const storedPermissions = sessionStorage.getItem("permissions");
            const storedRole = sessionStorage.getItem("role");

            if (storedUser) Object.assign(this.user, JSON.parse(storedUser));
            if (storedPermissions) {
                this.permissions.splice(0, this.permissions.length, ...JSON.parse(storedPermissions));
            }
            if (storedRole) this.role = JSON.parse(storedRole);
        }
    },
});
