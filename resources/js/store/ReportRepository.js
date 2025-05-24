import { defineStore } from "pinia";
import {ref,reactive} from "vue";
import {axios} from '../axios';

export  const useReportRepository=defineStore("reportRepository",{
    state(){
        return{
            search:ref(""),
            totalItems: ref(0),
            itemsPerPage: ref(5),
             departments: reactive([]),
        }
    },
    actions:{
        async fetchJawad({page,itemsPerPage}){
            const response= await axios.get(`studentsTypeBased?page=${page}&perPage=${itemsPerPage}$name=${this.search}`);
            this.departments = response.data.data;
            this.totalItems = response.data.meta.total;
        }


    }

})