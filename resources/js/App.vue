<template>
    <router-view></router-view>
    <NotificationToast :notification="notificationStore.notification" />
</template>

<script setup>
import NotificationToast from "@/components/NotificationToast.vue";
import { useNotificationStore } from "@/store/notification";

const notificationStore = useNotificationStore();
</script>
