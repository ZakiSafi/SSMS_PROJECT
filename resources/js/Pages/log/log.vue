<template>
    <CreateStudentStatistic v-if="UserRepository.createDialog" />
  
    <div :dir="dir">
      <AppBar :pageTitle="t('logs')" />
  
      <v-divider :thickness="1" class="border-opacity-100" />
  
      <!-- Search & Create Button -->
      <div class="btn-search pt-6 pb-6 d-flex justify-space-between">
        <div class="text-field w-25">
          <v-text-field color="primary" density="compact" variant="outlined" :label="t('search')"
            append-inner-icon="mdi-magnify" hide-details v-model="UserRepository.search" />
        </div>
  
        <v-btn @click="showCreateDialog" color="primary" variant="flat" class="px-6">
          {{ t('create') }}
        </v-btn>
      </div>
  
      <!-- Data Table -->
      <v-data-table-server :dir="dir" v-model:items-per-page="UserRepository.itemsPerPage"
        :headers="headers" :items-length="UserRepository.totalItems"
        :items="UserRepository.logs" :loading="UserRepository.loading"
        :search="UserRepository.search" @update:options="UserRepository.fetchLogs"
        class="w-100 mx-auto" hover>
        
        <template #bottom>
          <div class="d-flex align-center justify-end pa-2">
            <span class="mx-2">{{ t("pagination.items_per_page") }}</span>
            <v-select
              v-model="UserRepository.itemsPerPage"
              :items="[
                { value: 5, text: '5' },
                { value: 10, text: '10' },
                { value: 25, text: '25' },
                { value: 50, text: '50' },
                { value: -1, text: t('pagination.all') },
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
  import { computed } from "vue";
  import AppBar from "@/components/AppBar.vue";
 import { useUserRepository } from "@/store/UserRepository";
  const UserRepository = useUserRepository();
  import { useI18n } from "vue-i18n";
  const { t, locale } = useI18n();
  
  const dir = computed(() => {
    return locale.value === "en" ? "ltr" : "rtl";
  });
  
  const showCreateDialog = () => {
    UserRepository.statistic = {};
    UserRepository.isEditMode = false;
    UserRepository.createDialog = true;
  };
  
  const edit = (item) => {
    UserRepository.isEditMode = true;
    UserRepository.statistic = {};
    UserRepository.fetchStatistic(item.id)
      .then(() => {
        UserRepository.createDialog = true;
      })
      .catch((error) => {
        console.error("Error fetching statistic:", error);
      });
  };
  
  const deleteItem = async (item) => {
    await UserRepository.deleteStatistic(item.id);
  };
  
  const headers = computed(() => [
    { title: t("user"), key: "user.name", align: "start", sortable: false },
    { title: t("action_type"), key: "action_type" },
    { title: t("action_description"), key: "action_description" },
    { title: t("table_name"), key: "table_name" },
    { title: t("record_id"), key: "record_id" },
    { title: t("ip_address"), key: "ip_address" },
  ]);
  </script>
  
  <style scoped></style>