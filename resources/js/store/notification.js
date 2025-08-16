// stores/notification.js
import { defineStore } from "pinia";

export const useNotificationStore = defineStore("notification", {
    state: () => ({
        notification: {
            message: "",
            type: "success",
        },
    }),
    actions: {
        showNotification(message, type = "success") {
            this.notification = { message, type };
        },
        clearNotification() {
            this.notification = { message: "", type: "success" };
        },
    },
});
