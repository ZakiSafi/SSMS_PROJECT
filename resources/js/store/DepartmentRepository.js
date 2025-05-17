import { defineStore } from "pinia";
import {ref,reactive} from "vue";
import {axios} from '../axios';

export const useDepartmentRepository=defineStore("departmentRepository", {
    state() {
        return{
            isEditMode: ref(false),
            search: ref(""),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            itemsPerPage: ref(5),
            createDialog: ref(false),
            departmentSearch: ref(""),
            departments: reactive([]),
            department: reactive({}),
            faculties: reactive([])
        }},

        actions:{
            async FetchDepartments({ page, itemsPerPage }) {
                this.loading = true;
                const response = await axios.get(
                    `departments?page=${page}&perPage=${itemsPerPage}&name=${this.departmentSearch}`

                );
                this.departments = response.data.data;
                this.totalItems = response.data.meta.total;
                this.loading = false;
            },
            async FetchDepartment(id) {
                try {
                    const response = await axios.get(`departments/${id}`);
                    this.department = response.data.data;
                    console.log(this.department);
                } catch (err) {
                    // handle error if needed
                }
            },
            async CreateDepartment(formData) {
                try {
                    const config = {
                        method: "POST",
                        url: "departments",
                        data: formData,
                    };
                    await axios(config);
                    this.createDialog = false;
                    this.FetchDepartments({
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (err) {
                    // handle error if needed
                }
            },

            async UpdateDepartment(id,formData) {
                try {
                    const config = {
                        method: "PUT",
                        url: `departments/${id}`,
                        data: formData,
                    };
                    await axios(config);
                    this.createDialog = false;
                    this.FetchDepartments({
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (err) {
                    // handle error if needed
                }
            },

            async DeleteDepartment(id) {
                try {
                    const config = {
                        method: "DELETE",
                        url: `departments/${id}`,
                    };
                    await axios(config);
                    this.FetchDepartments({
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (err) {
                    // handle error if needed
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
            
        }

    },
    
)