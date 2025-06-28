<template>
    <CreateUser v-if="UserRepository.createDialog" />
    <div :dir="dir">
        <AppBar :pageTitle="$t('users')" />

        <v-divider :thickness="1" class="border-opacity-100" />

        <!-- Search and Create Button -->
        <div class="btn-search d-flex justify-space-between pt-12 pb-6">
            <div class="text-field w-25">
                <v-text-field
                    color="primary"
                    density="compact"
                    variant="filled"
                    :label="$t('search')"
                    append-inner-icon="mdi-magnify"
                    hide-details
                    v-model="UserRepository.userSearch"
                    class="search-field"
                />
            </div>
            <div>
                <v-btn
                    @click="createDialogShow"
                    color="primary"
                    variant="flat"
                    class="px-6"
                >
                    {{ $t("create") }}
                </v-btn>
            </div>
        </div>

        <!-- Data Table -->
        <v-data-table-server
            v-model:items-per-page="UserRepository.itemsPerPage"
            :headers="headers"
            :items-length="UserRepository.totalItems"
            :items="UserRepository.users"
            :loading="UserRepository.loading"
            :search="UserRepository.userSearch"
            @update:options="UserRepository.fetchUsers"
            class="w-100 mx-auto"
            hover
        >
        
            <!-- Action Slot -->
            <template v-slot:item.action="{ item }">
                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn
                            icon="mdi-dots-vertical"
                            v-bind="props"
                            variant="text"
                        />
                    </template>
                    <v-list>
                        <v-list-item>
                            <v-list-item-title
                                @click="edit(item)"
                                class="cursor-pointer pb-3"
                            >
                                <v-icon color="tealColor"
                                    >mdi-square-edit-outline</v-icon
                                >
                                {{ $t("Edit") }}
                            </v-list-item-title>
                            <v-list-item-title
                                @click="deleteItem(item)"
                                class="cursor-pointer pb-3"
                            >
                                <v-icon color="error"
                                    >mdi-delete-outline</v-icon
                                >
                                {{ $t("Delete") }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </template>
             <template #bottom>
                <div class="d-flex align-center justify-end pa-2" >
                    <span class="mx-2">{{
                        $t("pagination.items_per_page")
                    }}</span>
                    <v-select
                        v-model="UserRepository.itemsPerPage"
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
        </v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import CreateUser from "./CreateUser.vue";
import { useUserRepository } from "@/store/UserRepository";
import { useI18n } from "vue-i18n";
import { computed } from "vue";
const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" ? "rtl" : "ltr"));

const UserRepository = useUserRepository();

const createDialogShow = () => {
    UserRepository.department = {};
    UserRepository.isEditMode = false;
    UserRepository.createDialog = true;
};

const edit = (item) => {
    UserRepository.isEditMode = true;
    UserRepository.user = {};
    UserRepository.fetchUser(item.id)
        .then(() => {
            UserRepository.createDialog = true;
        })
        .catch((error) => {
            console.error("Error fetching data:", error);
        });
};

const deleteItem = async (item) => {
    await UserRepository.deleteUser(item.id);
};

const headers = computed(() => [
    { title: t("name"), key: "name", align: "start", sortable: false },
    { title: t("email"), key: "email", align: "center", sortable: false },
    {
        title: t("university"),
        key: "university.name",
        align: "center",
        sortable: false,
    },
    { title: t("action"), key: "action", align: "end", sortable: false },
]);
</script>

<style scoped></style>
