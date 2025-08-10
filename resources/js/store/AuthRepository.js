// src/store/AuthRepository.js
import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useRouter } from "vue-router";

export const useAuthRepository = defineStore("authRepository", {
    state() {
        return {
            user: reactive({}),
            permissions: reactive([]),
            role: null,
            loading: ref(false),
            error: ref(null),
            router: useRouter(),
            isAuthenticated: ref(false),
        };
    },

    actions: {
        async login(credentials) {
            this.loading = true;
            this.error = null;

            try {
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
        },
    },
});
