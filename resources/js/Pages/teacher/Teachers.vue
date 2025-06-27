<template>
    <CreateTeacher v-if="FacultyRepository.createDialog" />
    <div :dir="dir">
        <AppBar pageTitle="menu.teachers" />

        <!-- Divider between AppBar and content -->
        <v-divider :thickness="1" class="border-opacity-100 "></v-divider>

        <!-- Search & Buttons Section -->
        <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
            <div class="text-field w-25">
                <v-text-field color="primary" density="compact" variant="outlined" :label="t('search')"
                    append-inner-icon="mdi-magnify" hide-details v-model="FacultyRepository.search"
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
         :items="FacultyRepository.teachers"
         :loading="FacultyRepository.loading"
         :search="FacultyRepository.search"
         @update:options="FacultyRepository.fetchTeachers"
        class="w-100 mx-auto"
        hover
        >
        <!-- item per page controlling -->
        <template #bottom>
                <div class="d-flex align-center justify-end pa-2" >
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="FacultyRepository.itemsPerPage"
                        :items="[
                            { value: 5, text: '5' },
                            { value: 10, text: '10' },
                            { value: 25, text: '25' },
                            { value: 50, text: '50' },
                            { value: -1, text: $t('pagination.all') },
                        ]"
                        item-title="text"
                        item-value="value"
                        density="compact"
                        variant="outlined"
                        hide-details
                        style="max-width: 100px"
                    ></v-select>
                </div>
            </template>
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
                                {{$t('Edit')}}
                            </v-list-item-title>
                            <v-list-item-title @click="deleteItem(item)" class="cursor-pointer">
                                <v-icon color="error">mdi-delete-outline</v-icon>
                                 {{$t('Delete')}}
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
import CreateTeacher from "./CreateTeacher.vue";
import { useI18n } from "vue-i18n";
const { t,locale } = useI18n();
const dir = computed(() => {
  return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});


const FacultyRepository = useFacultyRepository();

const CreateDialogShow = () =>{
    FacultyRepository.teacher={}
    FacultyRepository.isEditMode = false;
    FacultyRepository.createDialog = true;
}

const edit= (item) =>{
    FacultyRepository.isEditMode= true
    FacultyRepository.teacher = {};
    FacultyRepository.fetchTeacher(item.id)
    .then(() => {
        FacultyRepository.createDialog = true;
    })
    .catch((error) => {
        console.error("Error fetching faculty:", error);
    });
}


const deleteItem = async (item) => {
   
    await FacultyRepository.deleteTeacher(item.id);
};


const headers = computed(()=>[
  { title: t("Academic Year"), key: "academic_year", align: "start", sortable: false },
  { title: t("University"), key: "university.name", align: "center", sortable: false },
  { title: t("Total Teachers"), key: "total_teachers", align: "center", sortable: false },
  { title: t("Action"), key: "action", align: "end", sortable: false },
]);
</script>