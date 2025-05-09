import { defineStore } from "pinia";
import { reactive, ref } from "vue";
import { axios } from "../axios";

export let useUniversityRepository = defineStore("universityRepository", {
    state() {
        return {
            isEditMode: ref(false),

            search: ref(""),
            serverItems: ref([]),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            selectedItems: ref([]),
            itemsPerPage: ref(5),
            createDialog: ref(false),

            uniSearch: ref(""),
            universities: reactive([]),
<<<<<<< HEAD
        };
=======
            university: reactive({}),
            provinces: ref([]),


        }

>>>>>>> b4df206cf88418209614f8ac3471f47bbbb90f82
    },
    actions: {
        async FetchUniversities({ page, itemsPerPage }) {
            this.loading = true;

            const response = await axios.get(
                `universities?page=${page}&perPage=${itemsPerPage}&search=${this.uniSearch}`
            );
            this.universities = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },
        async FetchUniversity(id) {
            // this.error = null;
            try {
                const response = await axios.get(`universties/${id}`);

                this.university = response.data.data;
                console.log(this.universty);
            } catch (err) {
                // this.error = err.message;
            }
        },
        async CreateUniversity(formData) {
            console.log(formData);
            try {
                // Adding a custom header to the Axios request
                const config = {
                    method: "POST",
                    url: "universities",

                    data: formData,
                };

                // Using Axios to make a GET request with async/await and custom headers
                const response = await axios(config);
                this.createDialog = false;
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // If there's an error, set the error in the stor
            }
        },
        async UpdateUniversity(id, data) {
            console.log(data);
            try {
                const config = {
                    method: "PUT",
                    url: "universities/" + id,

                    data: data,
                };

                // Using Axios to make a post request with async/await and custom headers
                const response = await axios(config);
                this.createDialog = false;
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // If there's an error, set the error in the store
                this.error = err;
            }
        },
        async DeleteUniversity(id) {
            this.isLoading = true;
            this.Expenses = [];
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "universties/" + id,
                };

                const response = await axios(config);

                this.expenseCategory = response.data.data;
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },
<<<<<<< HEAD
    },
=======

        async FetchProvinces() {
            try {
                const response = await axios.get("provinces");
                this.provinces = response.data.data; 
            } catch (err) {
                console.error("Failed to fetch provinces:", err);
            }
        },
        

    }

>>>>>>> b4df206cf88418209614f8ac3471f47bbbb90f82
});
