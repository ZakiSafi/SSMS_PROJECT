// stores/notification.js
import { defineStore } from "pinia";

export const useNotificationStore = defineStore("notification", {
    state: () => ({
        notification: {
            message: "",
            type: "primary",
        },
    }),
    actions: {
        showNotification(message, type = "primary") {
            this.notification = { message, type };
        },
        clearNotification() {
            this.notification = { message: "", type: "primary" };
        },
    },
});
