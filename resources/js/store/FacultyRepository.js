import { defineStore } from "pinia";
import {ref,reactive} from 'vue';
import {axios} from '../axios';

export  const useFacultyRepository=defineStore("facultyRepository",{
    state(){
        return{
            isEditMode: ref(false),
            search: ref(""),
            loadingTable: ref(true),
            loading: ref(false),
            totalItems: ref(0),
            itemsPerPage: ref(5),
            createDialog: ref(false),
            search: ref(""),
            faculties: reactive([]),
            teachers: reactive([]),
            teacher: reactive({}),
            faculty: reactive({}),
            universities: reactive([]),
        }
    },
    actions:{
        async FetchFaculties({ page, itemsPerPage }){
            this.loading=true;
            const response=await axios.get(
                `faculties?page=${page}&perPage=${itemsPerPage}&name=${this.search}`
            );

            this.faculties=response.data.data
            this.totalItems=response.data.meta.total
            this.loading=false

        },

        async FetchFaculty(id){
            try{
                const response= await axios.get(`faculties/${id}`);
                this.faculty=response.data.data
                console.log(this.faculty)
            }
            catch(err){
                console.log(err)
            }
        },

        async CreateFaculty(formData){
            try{
                const config={
                    method:"POST",
                    url:"faculties",
                    data:formData
                };

                await axios(config);
                this.createDialog=false
                this.FetchFaculties({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                // handle error if needed
            }

        },


        async UpdateFaculty(formData,id){
            try{
                const config={
                    method:"PUT",
                    url:`faculties/${id}`,
                    data:formData
                };

                await axios(config)
                this.createDialog=false
                this.FetchFaculties({
                    page: this.page,
                        itemsPerPage: this.itemsPerPage,
                });
            }

            catch(err){
                console.log(err)
            }
        },

        async DeleteFaculty(id) {
            this.isLoading = true;
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "faculties/" + id,
                };

                await axios(config);
                this.FetchFaculties({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },


        async fetchTeachers({ page, itemsPerPage } ){
            this.loading = true;
            try {
                const response = await axios.get(
                    `teachers?page=${page}&perPage=${itemsPerPage}&name=${this.search}`
                );
                this.teachers = response.data.data;
                this.totalItems = response.data.meta.total;
            } catch (err) {
                console.error("Error fetching teachers:", err);
                this.teachers = [];
            } finally {
                this.loading = false;
            }
        },

        async fetchTeacher(id) {
            try {
                const response = await axios.get(`teachers/${id}`);
                this.teacher = response.data.data;
            } catch (err) {
                console.error("Error fetching teacher:", err);
            }
        },

        async createTeacher(formData) {
            try {
                const config = {
                    method: "POST",
                    url: "teachers",
                    data: formData,
                };

                await axios(config);
                this.createDialog = false;
                this.fetchTeachers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Error creating teacher:", err);
            }
        },

        async updateTeacher(formData, id) {
            try {
                const config = {
                    method: "PUT",
                    url: `teachers/${id}`,
                    data: formData,
                };

                await axios(config);
                this.createDialog = false;
                this.fetchTeachers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                console.error("Error updating teacher:", err);
            }
        },

        async deleteTeacher(id) {
            this.loading = true;
            this.error = null;

            try {
                const config = {
                    method: "DELETE",
                    url: "teachers/" + id,
                };

                await axios(config);
                this.fetchTeachers({
                    page: this.page,
                    itemsPerPage: this.itemsPerPage,
                });
            } catch (err) {
                this.error = err;
            }
        },
        async FetchUniversities() {
            try {
                const response = await axios.get("universities");
                this.universities = response.data.data;
            } catch (err) {
                // handle error if needed
            }
        },
    }
})