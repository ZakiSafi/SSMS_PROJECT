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
                this.user = response.data.user;
                this.isAuthenticated = true;

                sessionStorage.setItem("token", response.data.token);
                sessionStorage.setItem(
                    "user",
                    JSON.stringify(response.data.user)
                );

                const meResponse = await axios.get("/me");

        const permissions = meResponse.data.data.permissions;
        console.log("Permissions:", permissions);
        const role = meResponse.data.data.role;

        sessionStorage.setItem("permissions", JSON.stringify(permissions));
        sessionStorage.setItem("role", JSON.stringify(role));

        this.permissions = permissions;
        this.role = role;
        this.user = meResponse.data;
                

                toast.success("Login successful!", {
                    position: "top-right",
                    autoClose: 3000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                });

        // Wait 1 second before redirect
        setTimeout(() => {
            this.router.push("/dashboard");
        }, 1000);
        
    } catch (error) {
        toast.error("Login failed! Please check your credentials.", {
            position: "top-right",
            autoClose: 3000,
            hideProgressBar: false,
            closeOnClick: true,
            pauseOnHover: true,
            draggable: true,
            progress: undefined,
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
            const formData = {
                token: sessionStorage.getItem("token"),
            };
            try {
                await axios.post("logout", formData);
                sessionStorage.removeItem("token");
                sessionStorage.removeItem("user");

                toast.success("Logout successful!", {
                    position: "top-right",
                    autoClose: 3000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
                });

                // Wait 1 second before redirect
                setTimeout(() => {
                    this.router.push("/");
                }, 1000);
            } catch (error) {
                toast.error("Logout failed! Please try again.", {
                    position: "top-right",
                    autoClose: 3000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined,
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

const meResponse = await axios.get("/me");

        const permissions = meResponse.data.data.permissions;
        console.log("Permissions:", permissions);
        const role = meResponse.data.data.role;

        sessionStorage.setItem("permissions", JSON.stringify(permissions));
        sessionStorage.setItem("role", JSON.stringify(role));

        this.permissions = permissions;
        this.role = role;
        this.user = meResponse.data;