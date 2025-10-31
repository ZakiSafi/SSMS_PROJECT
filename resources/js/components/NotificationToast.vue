<!-- components/NotificationToast.vue -->
<template>
    <v-snackbar
        v-model="show"
        :timeout="timeout"
        :color="snackbarColor"
        location="top right"
    >
        {{ message }}

        <template #actions>
            <v-btn variant="text" @click="show = false"> Close </v-btn>
        </template>
    </v-snackbar>
</template>

<script setup>
import { ref, watch, computed } from "vue";

const props = defineProps({
    notification: {
        type: Object,
        default: () => ({}),
    },
});

const show = ref(false);
const message = ref("");
const type = ref("primary");
const timeout = ref(3000);

const snackbarColor = computed(() => {
    if (type.value === "success") return "green-darken-1";
    if (type.value === "error") return "red-darken-1";
    if (type.value === "warning") return "amber-darken-1";
    if (type.value === "info") return "indigo-darken-1";
    return "primary";
});

watch(
    () => props.notification,
    (newVal) => {
        if (newVal.message) {
            message.value = newVal.message;
            type.value = newVal.type || "primary";
            show.value = true;
        }
    },
    { deep: true }
);
</script>
