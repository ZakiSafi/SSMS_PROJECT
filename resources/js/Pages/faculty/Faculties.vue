<template>
    <CreateFaculty v-if="FacultyRepository.createDialog" />
    <div :dir="dir">
        <AppBar pageTitle="Faculty" />

        <!-- Divider between AppBar and content -->
        <v-divider :thickness="1" class="border-opacity-100 "></v-divider>

        <!-- Search & Buttons Section -->
        <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
            <div class="text-field w-25">
                <v-text-field color="primary" density="compact" variant="outlined" :label="t('search')"
                    append-inner-icon="mdi-magnify" hide-details v-model="FacultyRepository.facultySearch"
                    class="search-field"></v-text-field>
            </div>

            <div>
                &nbsp;
                <v-btn @click="CreateDialogShow" color="primary" variant="flat" class="px-6">
                    {{ $t('create') }}
                </v-btn>
            </div>
        </div>

        <!-- Data Table Section -->

        <v-data-table-server
         v-model:items-per-page="FacultyRepository.itemsPerPage"
         :headers="headers"
         :items-length="FacultyRepository.totalItems"
         :items="FacultyRepository.faculties"
         :loading="FacultyRepository.loading"
         :search="FacultyRepository.facultySearch"
         @update:options="FacultyRepository.FetchFaculties"
        class="w-100 mx-auto"
        hover
        >

        <!-- Action Slot -->
            <template v-slot:item.action="{ item }">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" v-bind="props" variant="text"></v-btn>
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title @click="edit(item)" class="cursor-pointer pb-3">
                                <v-icon color="tealColor">mdi-square-edit-outline</v-icon>
                                Edit
                            </v-list-item-title>
                            <v-list-item-title @click="deleteItem(item)" class="cursor-pointer">
                                <v-icon color="error">mdi-delete-outline</v-icon>
                                Delete
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
        </v-data-table-server>
    </div>

</template>
<script setup>
import AppBar from "@/components/AppBar.vue";
import { computed } from "vue";
import { useFacultyRepository } from "@/store/FacultyRepository";
import CreateFaculty from "./CreateFaculty.vue";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});


const FacultyRepository = useFacultyRepository();

const CreateDialogShow = () =>{
    FacultyRepository.faculty={}
    FacultyRepository.isEditMode = false;
    FacultyRepository.createDialog = true;
}

const edit= (item) =>{
    FacultyRepository.isEditMode= true
    FacultyRepository.faculty = {};
    FacultyRepository.FetchFaculty(item.id)
    .then(() => {
        FacultyRepository.createDialog = true;
    })
    .catch((error) => {
        console.error("Error fetching faculty:", error);
    });
}


const deleteItem = async (item) => {
   
    await FacultyRepository.DeleteFaculty(item.id);
};


const headers = [
  { title: "Name", key: "name", align: "start", sortable: false },
  { title: "University", key: "university.name", align: "start", sortable: false },
  { title: "Action", key: "action", align: "end", sortable: false },
];
</script>