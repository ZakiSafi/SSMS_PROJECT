<template>
    <v-dialog v-model="dialogVisible" max-width="800px" scrollable persistent>
        <v-card class="rounded-lg">
            <!-- Header Section -->
            <v-card-title
                class="d-flex justify-space-between align-center pa-6"
            >
                <div class="text-h5 font-weight-bold text-primary">
                    {{ university?.name || "University Details" }}
                </div>
                <v-btn
                    icon="mdi-close"
                    variant="text"
                    @click="dialogVisible = false"
                />
            </v-card-title>

            <v-divider class="mb-4" />

            <!-- Main Content -->
            <v-card-text class="pa-6">
                <!-- University Info -->
                <div class="mb-8">
                    <div class="d-flex mb-4">
                        <v-icon
                            icon="mdi-map-marker"
                            class="mr-2"
                            color="primary"
                        />
                        <div>
                            <div class="text-subtitle-1 font-weight-medium">
                                Location
                            </div>
                            <div class="text-body-1">
                                {{
                                    university?.province?.name ||
                                    "Not specified"
                                }}
                            </div>
                        </div>
                    </div>

                    <div class="d-flex">
                        <v-icon
                            icon="mdi-school"
                            class="mr-2"
                            color="primary"
                        />
                        <div>
                            <div class="text-subtitle-1 font-weight-medium">
                                Type
                            </div>
                            <div class="text-body-1">
                                {{ university?.type || "Not specified" }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Faculties Section -->
                <div>
                    <div class="text-h6 font-weight-medium mb-4">Faculties</div>

                    <v-alert
                        v-if="!university?.faculties?.length"
                        type="info"
                        variant="tonal"
                        class="mb-4"
                    >
                        No faculties available for this university
                    </v-alert>

                    <v-list v-else class="bg-transparent">
                        <v-list-item
                            v-for="faculty in university.faculties"
                            :key="faculty.id"
                            class="px-0"
                        >
                            <template #prepend>
                                <v-icon
                                    icon="mdi-book-open-blank-variant"
                                    color="secondary"
                                />
                            </template>
                            <v-list-item-title class="font-weight-medium">
                                {{ faculty.name }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list>
                </div>
            </v-card-text>

            <!-- Footer Actions -->
            <v-card-actions class="pa-6 pt-0">
                <v-spacer />
                <v-btn
                    color="primary"
                    variant="flat"
                    size="large"
                    @click="dialogVisible = false"
                >
                    Close
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    modelValue: Boolean,
    university: Object,
});

const emit = defineEmits(["update:modelValue"]);

const dialogVisible = computed({
    get: () => props.modelValue,
    set: (value) => emit("update:modelValue", value),
});
</script>

<style scoped>
.v-card {
    box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.1);
}

.v-list-item {
    border-radius: 8px;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.v-list-item:hover {
    background-color: rgba(var(--v-theme-primary), 0.05);
}
</style>
