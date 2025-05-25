import {defineStore} from 'pinia';
import {reactive, ref} from 'vue';
import {axios} from '../axios';

export let useProvinceRepository = defineStore("", {
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

            provinceSearch: ref(""),
            provinces: reactive([]),
            province: reactive({}),


        }

    },

    actions: {
        async FetchProvinces({ page, itemsPerPage }) {
            this.loading = true;

            const response = await axios.get(
                `provinces?page=${page}&perPage=${itemsPerPage}&name=${this.provinceSearch}`
            );
            this.provinces = response.data.data;
            this.totalItems = response.data.meta.total;
            this.loading = false;
        },
        async FetchProvince(id) {
            try {
                const response = await axios.get(`provinces/${id}`);
                this.province = response.data.data;
                console.log(this.province);
            } catch (err) {
                // handle error if needed
            }
        },
        async CreateProvince(formData) {
            try {
                const config = {
                    method: "POST",
                    url: "provinces",
                    data: formData,
                };

                await axios(config);
                this.createDialog = false;
                this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // handle error if needed
            }
        },
        async UpdateProvince(id, data) {
            try {
                const config = {
                    method: "PUT",
                    url: "provinces/" + id,
                    data: data,
                };

                await axios(config);
                this.createDialog = false;
                this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },
        async DeleteProvince(id) {
            this.isLoading = true;
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "provinces/" + id,
                };

                await axios(config);
                this.FetchProvinces({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        }
    }


});
