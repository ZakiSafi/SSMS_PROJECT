<!-- components/NotificationToast.vue -->
<template>
    <v-snackbar
        v-model="show"
        :timeout="timeout"
        :color="type"
        location="top right"
    >
        {{ message }}

        <template #actions>
            <v-btn variant="text" @click="show = false"> Close </v-btn>
        </template>
    </v-snackbar>
</template>

<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    notification: {
        type: Object,
        default: () => ({}),
    },
});

const show = ref(false);
const message = ref("");
const type = ref("success");
const timeout = ref(3000);

watch(
    () => props.notification,
    (newVal) => {
        if (newVal.message) {
            message.value = newVal.message;
            type.value = newVal.type || "success";
            show.value = true;
        }
    },
    { deep: true }
);
</script>
