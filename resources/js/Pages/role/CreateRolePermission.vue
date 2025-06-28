<script setup>
import { ref } from "vue";
import AppBar from "@/components/AppBar.vue";

const roleName = ref("");
const roleDescription = ref("");

// Define the pages and allowed actions
const pages = [
    { label: "Dashboard", key: "dashboard", actions: ["view"] },
    {
        label: "Provinces",
        key: "provinces",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Universities",
        key: "university",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Faculties",
        key: "faculties",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Departments",
        key: "departments",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Teachers",
        key: "teachers",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Student Statistics",
        key: "student_statistic",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Current Students",
        key: "current_students",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Graduated Students",
        key: "graduated_students",
        actions: ["view", "create", "edit", "delete"],
    },
    {
        label: "Settings",
        key: "settings",
        actions: ["view", "create", "edit", "delete"],
    },
];

// Set up reactive permissions model
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

    console.log("Sending payload:", payload);

    try {
        const res = await fetch("/api/assign-permissions", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(payload),
        });
        const result = await res.json();
        console.log("Backend response:", result);
    } catch (error) {
        console.error("Error submitting:", error);
    }
};
</script>

<template>
    <v-container fluid>
        <AppBar pageTitle="Role Permissions " />
        <!-- Divider between AppBar and content -->
        <v-divider :thickness="1" class="border-opacity-100"></v-divider>
        <v-row class="pt-6">
            <!-- Left Side Form -->
            <v-col cols="12" md="12">
                <!-- Role Info Inputs -->
                <v-row class="mb-6">
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleName"
                            label="Role Name"
                            variant="outlined"
                            density="compact"
                            dense
                            required
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <v-text-field
                            v-model="roleDescription"
                            label="Role Description"
                            variant="outlined"
                            density="compact"
                            dense
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
                                {{ page.label }}
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
                                            :label="
                                                action.charAt(0).toUpperCase() +
                                                action.slice(1)
                                            "
                                            hide-details
                                            dense
                                        />
                                    </div>

                                    <!-- Spacer to fill layout for cards with fewer checkboxes -->
                                    <div
                                        v-for="n in 4 - page.actions.length"
                                        :key="'spacer-' + n"
                                        class="w-45"
                                    >
                                        <!-- Empty slot for layout balance -->
                                    </div>
                                </div>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <!-- Submit Button -->
                <v-row class="mt-6">
                    <v-col cols="10" > </v-col>
                    <v-col cols="2" class="text-center">
                        <v-btn
                            color="primary"
                            size="large"
                            @click="submitPermissions"
                        >
                            Submit
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
