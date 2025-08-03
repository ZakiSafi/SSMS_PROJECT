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
            university: reactive({}),
            provinces: ref([]),
            faculties: ref([]),
        };
    },
    actions: {
        async FetchUniversities({ page, itemsPerPage }) {
            this.loading = true;

            const response = await axios.get(
                `universities?page=${page}&perPage=${itemsPerPage}&name=${this.uniSearch}`
            );
            this.universities = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },
        async FetchUniversity(id) {
            try {
                const response = await axios.get(`universities/${id}`);
                this.university = response.data.data;
                console.log(this.university);
            } catch (err) {
                // handle error if needed
            }
        },
        async CreateUniversity(formData) {
            try {
                const config = {
                    method: "POST",
                    url: "universities",
                    data: formData,
                };

                await axios(config);
                this.createDialog = false;
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // handle error if needed
            }
        },
        async UpdateUniversity(id, data) {
            try {
                const config = {
                    method: "PUT",
                    url: "universities/" + id,
                    data: data,
                };

                await axios(config);
                this.createDialog = false;
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },
        async DeleteUniversity(id) {
            this.isLoading = true;
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "universities/" + id,
                };

                await axios(config);
                this.FetchUniversities({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },
        async FetchProvinces() {
            try {
                const response = await axios.get("provinces");
                this.provinces = response.data.data;
            } catch (err) {
                console.error("Failed to fetch provinces:", err);
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
