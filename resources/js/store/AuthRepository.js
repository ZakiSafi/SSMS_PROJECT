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

        async fetchRolePermissions({ page, itemsPerPage }) {
            this.loading = true;

            const response = await axios.get(
                `role?page=${page}&perPage=${itemsPerPage}&search=${this.search}`
            );
            this.roles = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },
        async fetchRolePermission(id) {
            // this.error = null;
            try {
                const response = await axios.get(`role/${id}`);

                this.permission = response.data.data;
                console.log(this.permission);
            } catch (err) {
                // this.error = err.message;
            }
        },
        async updateRole(id, data) {
            try {
                const response = await axios.put(
                    "role/" + id,
                    data
                );

                // Using Axios to make a post request with async/await and custom headers
                //        if (this.role && this.role.id === id) {

                // }
                await this.refreshPermissions();
                this.router.push("/role");
                this.fetchRolePermissions({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // If there's an error, set the error in the store
                this.error = err;
            }
        },
        async createRole(formData) {
            console.log(formData);
            try {
                // Adding a custom header to the Axios request
                const config = {
                    method: "POST",
                    url: "role",

                    data: formData,
                };

                // Using Axios to make a GET request with async/await and custom headers
                const response = await axios(config);
                toast.success("Permission Created successful!", {
                    position: "top-right",
                    autoClose: 4000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                });
                this.router.push("/rolePermissions");
                this.fetchRolePermissions({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error =
                    err.response?.data?.message ||
                    "Failed to create Permission. Please try again.";

                // Show toast
                toast.error(this.error, {
                    position: "top-right",
                    autoClose: 3000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                });
            }
        },
        async DeleteRolePermission(id) {
            this.isLoading = true;
            this.setting = [];
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "role_permissions/" + id,
                };

                const response = await axios(config);

                // this.setting = response.data.data;
                this.fetchRolePermissions({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
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
