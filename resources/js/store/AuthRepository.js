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
                const token = response.data.token;

                // Save token in sessionStorage
                sessionStorage.setItem("token", token);
                axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
                this.isAuthenticated = true;

                // 2. Get user + permissions
                const meResponse = await axios.get("/me");
                const { user, permissions, role } = meResponse.data.data;

                // Save to sessionStorage
                sessionStorage.setItem("user", JSON.stringify(user));
                sessionStorage.setItem("permissions", JSON.stringify(permissions));
                sessionStorage.setItem("role", JSON.stringify(role));

                // ✅ Update reactivity (mutate instead of replace)
                Object.assign(this.user, user); 
                this.permissions.splice(0, this.permissions.length, ...permissions);
                this.role = role;

                // 3. Success toast + redirect
                toast.success("Login successful!", { position: "top-right", autoClose: 3000 });
                setTimeout(() => {
                    this.router.push("/dashboard");
                }, 1000);

            } catch (error) {
                toast.error("Login failed! Please check your credentials.", { position: "top-right", autoClose: 3000 });
                this.error = error.response ? error.response.data.message : "An error occurred!";
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                await axios.post("logout", { token: sessionStorage.getItem("token") });
            } catch {
                // ignore logout errors
            }

            // Clear session + state
            sessionStorage.clear();
            Object.keys(this.user).forEach(key => delete this.user[key]);
            this.permissions.splice(0);
            this.role = null;
            this.isAuthenticated = false;

            toast.success("Logout successful!", { position: "top-right", autoClose: 3000 });
            this.router.push("/");
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
                await this.refreshPermissions();
                this.createDialog = false;
                this.fetchRoles({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Failed to update user:", err);
            }
        },

        async fetchLogs({ page, itemsPerPage }) {
            try {
                this.loading = true;
                const response = await axios.get(
                    `logs?page=${page}&perPage=${itemsPerPage}&user_id=${this.search}`
                );
                this.logs = response.data.data;
                this.totalItems = response.data.meta.total;
                this.loading = false;
            } catch (err) {
                console.error("Failed to fetch logs:", err);
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
