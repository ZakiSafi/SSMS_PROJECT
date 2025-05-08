<template>
    
    <div class="all-expense rounded-xl" :dir="dir">
        <div class="card rounded-xl">
         
            <v-divider
                :thickness="1"
                class="border-opacity-100"
            ></v-divider>

            <div class="btn-search pt-12 pb-6">
                <div class="text-field w-25">
                    <v-text-field
                        :loading="loading"
                        color="primaryOld"
                        density="compact"
                        variant="outlined"
                        label="search"
                        append-inner-icon="mdi-magnify"
                        hide-details
                        v-model="UniversityRepository.uniSearch"
                    ></v-text-field>
                </div>
                <div class="btn">
                    <v-btn variant="outlined" color="primaryOld" class="px-6">
                        Filter
                    </v-btn>
                    &nbsp;
                    <v-btn
                        @click="CreateDialogShow"
                        color="primaryOld"
                        variant="flat"
                        text="create"
                        class="px-6"
                    >
                    </v-btn>
                </div>
            </div>
            <!-- v-table server  -->
            <div class="overflow-x-hidden">
                <v-app class="bg-white ">
                    <v-main class="main">
                        <v-row>
                            <v-col>
                                <v-data-table-server
                                :dir="dir"
                                    theme="cursor-pointer"
                                    v-model:items-per-page="
                                       UniversityRepository.itemsPerPage
                                    "
                                    :headers="headers"
                                    :items-length="UniversityRepository.totalItems"
                                    :items="UniversityRepository.universities"
                                    :loading="UniversityRepository.loading"
                                    :search="UniversityRepository.uniSearch"
                                    @update:options="
                                       UniversityRepository.FetchUniversities
                                    "
                                    :item-key="UniversityRepository.universities"
                                    hover
                                    class="w-100 mx-auto"
                                >
                                    <template v-slot:item.action="{ item }">
                                        <v-menu>
                                            <template
                                                v-slot:activator="{ props }"
                                            >
                                                <v-btn
                                                    icon="mdi-dots-vertical"
                                                    v-bind="props"
                                                    variant="text"
                                                ></v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item>
                                                    <v-list-item-title
                                                        @click="edit(item)"
                                                        class="cursor-pointer d-flex gap-3 justify-left pb-3"
                                                    >
                                                        <v-icon
                                                            color="tealColor"
                                                            >mdi-square-edit-outline</v-icon
                                                        >
                                                        Edit
                                                    </v-list-item-title>

                                                    <v-list-item-title
                                                        class="cursor-pointer d-flex gap-3"
                                                        @click="
                                                            deleteItem(item)
                                                        "
                                                    >
                                                        <v-icon color="error"
                                                            >mdi-delete-outline</v-icon
                                                        >
                                                        Delete
                                                    </v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </template>
                                </v-data-table-server>
                            </v-col>
                        </v-row>
                    </v-main>
                </v-app>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted ,computed} from "vue";
import { useUniversityRepository } from "@/store/UniversityRepository";
const UniversityRepository = useUniversityRepository();


// bulk delete

// delete and update Create
const CreateDialogShow = () => {
    (UniversityRepository.owner = {}),UniversityRepository.setEditMode(false);
   UniversityRepository.createDialog = true;
};

const edit = (item) => {
    console.log(item, "me");
   UniversityRepository.setEditMode(true);
   UniversityRepository.owner = {};
    if (Object.keys(UniversityRepository.owner).length === 0) {
       UniversityRepository.fetchOwner(item.id)
            .then(() => {
               UniversityRepository.createDialog = true;
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
            });
    }
};

const deleteItem = async (item) => {
    await UniversityRepository.DeleteOwner(item.id);
};
// header
const headers = [
    { title: "Name", key: "name", align: "start", sortable: false },
    { title: "pickup", key: "totalAmount", align: "center", sortable: false },
    { title:"phone", key: "phone", align: "center", sortable: false },

    { title: "action", key: "action", align: "end", sortable: false },
];
</script>