import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from '../axios';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useRouter } from "vue-router";

export const useAuthRepository= defineStore("authRepository",{
    state(){
        return {
            user: reactive({}),
            loading: ref(false),
            error: ref(null),
            router:useRouter(),
            isAuthenticated: ref(false),
        }
    },

    actions:{
        async login(credentials) {
    this.loading = true;
    this.error = null;

    try {
        const response = await axios.post('login', credentials);
        this.user = response.data.date;
        this.isAuthenticated = true;

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
            this.router.push("/home");
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

       

        
    }
})