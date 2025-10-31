<script setup>
import { ref, computed } from "vue";
import AppBar from "@/components/AppBar.vue";
import { useAuthRepository } from "@/store/AuthRepository";
import { useI18n } from "vue-i18n"; // <--- import i18n

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

const AuthRepository = useAuthRepository();

const roleName = ref("");
const roleDescription = ref("");

// Define pages and actions
const pages = [
    { label: "dashboard", key: "dashboard", actions: ["view"] },
    {
        label: "provinces",
        key: "provinces",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "universities",
        key: "university",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "faculties",
        key: "faculties",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "departments",
        key: "departments",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "teachers",
        key: "teachers",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "student_statistics",
        key: "student_statistic",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "current_students",
        key: "current_students",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "graduated_students",
        key: "graduated_students",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "settings",
        key: "settings",
        actions: ["view", "create", "edit", "delete"],
    },
];

// Reactive permissions object
const permissions = ref({});
pages.forEach((page) => {
    permissions.value[page.key] = {};
    page.actions.forEach((action) => {
        permissions.value[page.key][action] = false;
    });
});

const submitPermissions = async () => {
    const permissionArray = [];

    Object.entries(permissions.value).forEach(([pageKey, actions]) => {
        Object.entries(actions).forEach(([action, allowed]) => {
            if (allowed) {
                permissionArray.push({
                    name: `${pageKey}.${action}`,
                    description: `Allow ${action} ${pageKey.replace("_", " ")}`,
                });
            }
        });
    });

    const payload = {
        name: roleName.value,
        description: roleDescription.value,
        permissions: permissionArray,
    };

    try {
        await AuthRepository.createRole(payload);
        console.log("Role created successfully");

        // Reset form
        roleName.value = "";
        roleDescription.value = "";
        pages.forEach((page) => {
            page.actions.forEach((action) => {
                permissions.value[page.key][action] = false;
            });
        });
    } catch (error) {
        console.error("Error creating role:", error);
    }
};
</script>

<template>
    <v-container fluid :dir="dir">
        <AppBar :pageTitle="t('role_permissions')" />
        <v-divider :thickness="1" class="border-opacity-100" />

        <v-row class="pt-6">
            <v-col cols="12">
                <!-- Role Info -->
                <v-row class="mb-6">
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleName"
                            :label="t('role_name')"
                            variant="outlined"
                            density="compact"
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleDescription"
                            :label="t('role_description')"
                            variant="outlined"
                            density="compact"
                        />
                    </v-col>
                </v-row>

                <!-- Permission Cards -->
                <v-row>
                    <v-col
                        v-for="page in pages"
                        :key="page.key"
                        cols="12"
                        md="6"
                        lg="4"
                    >
                        <v-card elevation="1" class="pa-3" color="#f5f5f5">
                            <v-card-title
                                class="px-2 py-1 text-grey-darken-3 font-weight-bold text-subtitle-1"
                            >
                                {{ t(page.label) }}
                            </v-card-title>
                            <v-divider class="mb-3" />
                            <v-card-text class="px-2 pt-0 min-card-text">
                                <div class="d-flex flex-wrap gap-4">
                                    <div
                                        v-for="action in page.actions"
                                        :key="action"
                                        class="w-45"
                                    >
                                        <v-checkbox
                                            v-model="
                                                permissions[page.key][action]
                                            "
                                            :label="t(action)"
                                            hide-details
                                            dense
                                        />
                                    </div>
                                    <div
                                        v-for="n in 4 - page.actions.length"
                                        :key="'spacer-' + n"
                                        class="w-45"
                                    />
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Submit -->
                <v-row class="mt-6">
                    <v-col cols="10"></v-col>
                    <v-col cols="2" class="text-center">
                        <v-btn
                            color="primary"
                            size="large"
                            @click="submitPermissions"
                        >
                            {{ t("submit") }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>
.w-45 {
    width: 45%;
}
.gap-4 {
    gap: 1rem;
}
.min-card-text {
    min-height: 143px;
}
</style>
