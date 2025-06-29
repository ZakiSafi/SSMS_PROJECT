<template>
    <div :dir="dir">
        <AppBar pageTitle="Universities" />
        <!-- Divider between AppBar and content -->
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>
        <!-- Search & Buttons Section -->
        <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
            <div class="text-field w-25">
                <v-text-field
                    :loading="loading"
                    color="primary"
                    density="compact"
                    variant="outlined"
                    :label="t('search')"
                    append-inner-icon="mdi-magnify"
                    hide-details
                    v-model="UserRepository.search"
                    class="search-field"
                ></v-text-field>
            </div>

            <RouterLink to="/CreateRole">
                <div class="btn">
                    <v-btn
                        @click="CreateDialogShow"
                        color="primary"
                        variant="flat"
                        class="px-6"
                    >
                        {{ $t("create") }}
                    </v-btn>
                </div>
            </RouterLink>
        </div>

        <!-- Data Table Section -->
        <v-data-table-server
            :dir="dir"
            v-model:items-per-page="UserRepository.itemsPerPage"
            :headers="headers"
            :items-length="UserRepository.totalItems"
            :items="UserRepository.roles"
            :loading="UserRepository.loading"
            :search="UserRepository.search"
            @update:options="UserRepository.fetchRoles"
            class="w-100 mx-auto"
            hover
        >
            <template #bottom>
                <div class="d-flex align-center justify-end pa-2">
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
                                class="cursor-pointer d-flex gap-3 pb-3"
                            >
                                <v-icon color="tealColor"
                                    >mdi-square-edit-outline</v-icon
                                >
                                {{ $t("Edit") }}
                            </v-list-item-title>
                            <v-list-item-title
                                @click="deleteItem(item)"
                                class="cursor-pointer d-flex gap-3"
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
        </v-data-table-server>
    </div>
</template>

<script setup>
import AppBar from "@/components/AppBar.vue";
import CreateRolePermission from "./CreateRolePermission.vue";
import { computed } from "vue";
import { useUserRepository } from "../../store/UserRepository";
import { useI18n } from "vue-i18n";
import { RouterLink } from "vue-router";
const { t, locale } = useI18n();
const dir = computed(() => {
    return locale.value === "fa" ? "rtl" : "ltr"; // Correctly set "rtl" and "ltr"
});

const UserRepository = useUserRepository();

const CreateDialogShow = () => {
    UserRepository.university = {};
    UserRepository.isEditMode = false;
    UserRepository.createDialog = true;
};

const edit = (item) => {
    UserRepository.isEditMode = true;
    UserRepository.university = {};
    UserRepository.FetchUniversity(item.id)
        .then(() => {
            UserRepository.createDialog = true;
        })
        .catch((error) => {
            console.error("Error fetching data:", error);
        });
};

const deleteItem = async (item) => {
    await UserRepository.DeleteUniversity(item.id);
};

const headers = computed(() => [
    { title: t("Name"), key: "name", align: "start", sortable: false },
    {
        title: t("description"),
        key: "description",
        align: "center",
        sortable: false,
    },
    { title: t("Action"), key: "action", align: "end", sortable: false },
]);
</script>

<style scoped></style>
