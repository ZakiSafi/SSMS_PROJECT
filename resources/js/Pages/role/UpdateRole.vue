<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import AppBar from "@/components/AppBar.vue";
import { useAuthRepository } from "@/store/AuthRepository";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const router = useRouter();
const route = useRoute();
const id = route.params.id;

const AuthRepository = useAuthRepository();
const roleName = ref("");
const roleDescription = ref("");

const pages = [
    { label: "dashboard", key: "dashboard", actions: ["view"] },
    {
        label: "provinces",
        key: "provinces",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "university",
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
        label: "student_statistic",
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

const permissions = ref({});

// initialize permissions
pages.forEach((page) => {
    permissions.value[page.key] = {};
    page.actions.forEach((action) => {
        permissions.value[page.key][action] = false;
    });
});

onMounted(async () => {
    await AuthRepository.fetchRole(id);

    roleName.value = AuthRepository.role.name;
    roleDescription.value = AuthRepository.role.description;

    // reset all permissions
    pages.forEach((page) => {
        page.actions.forEach((action) => {
            permissions.value[page.key][action] = false;
        });
    });

    // set from backend
    AuthRepository.role.permissions.forEach((perm) => {
        const [pageKey, action] = perm.split(".");
        if (permissions.value[pageKey]) {
            permissions.value[pageKey][action] = true;
        }
    });
});

const submitPermissions = async () => {
    const selectedPermissions = [];
    Object.entries(permissions.value).forEach(([pageKey, actions]) => {
        Object.entries(actions).forEach(([action, allowed]) => {
            if (allowed) {
                selectedPermissions.push(`${pageKey}.${action}`);
            }
        });
    });

    const payload = {
        name: roleName.value,
        description: roleDescription.value,
        permissions: selectedPermissions,
    };

    try {
        await AuthRepository.updateRole(id, payload);
        router.push({ name: "role-permission" });
    } catch (error) {
        console.error("Error updating role:", error);
    }
};
</script>

<template>
    <v-container
        fluid
        :dir="['fa', 'ps', 'ar'].includes(locale) ? 'rtl' : 'ltr'"
    >
        <AppBar :pageTitle="t('update')" />
        <v-divider :thickness="1" class="border-opacity-100" />

        <v-row class="pt-6">
            <v-col cols="12">
                <!-- Role Info -->
                <v-row class="mb-6">
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleName"
                            :label="t('name')"
                            variant="outlined"
                            density="compact"
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleDescription"
                            :label="t('description')"
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
                            {{ t("update") }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-container>
</template>
