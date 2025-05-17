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
            facultySearch: ref(""),
            faculties: reactive([]),
            faculty: reactive({}),
            universities: reactive([]),
        }
    },
    actions:{
        async FetchFaculties({ page, itemsPerPage }){
            this.loading=true;
            const response=await axios.get(
                `faculties?page=${page}&perPage=${itemsPerPage}&name=${this.facultySearch}`
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

        async DeleteFaculty(id){
            try{
                const config={

                    method:"DELETE",
                    url:"faculties"
                };

                await axios(config);
                    this.FetchFaculties({
                        page: this.page,
                        itemsPerPage: this.itemsPerPage,
                    });
                } catch (err) {
                    // handle error if needed
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