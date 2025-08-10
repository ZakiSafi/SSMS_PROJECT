<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="UniversityRepository.createDialog"
            persistent
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title class="px-2 pt-4 d-flex justify-space-between">
                        <h2 class="font-weight-bold pl-4">
                            {{
                                UniversityRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("create")
                            }}
                        </h2>
                        <v-btn variant="text" @click="closeDialog(isActive)">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider class="border-opacity-100 mx-6"></v-divider>

                    <v-card-text>
                        <v-form ref="formRef" class="pt-4" v-model="formIsValid">
                            <v-text-field
                                v-model="formData.name"
                                variant="outlined"
                                :label="$t('form.name')"
                                class="pb-4"
                                density="compact"
                                :rules="[rules.required, rules.name]"
                            />

                            <v-select
                                v-model="formData.province_id"
                                :items="UniversityRepository.provinces"
                                item-value="id"
                                item-title="name"
                                variant="outlined"
                                :label="$t('Province')"
                                density="compact"
                                class="pb-4"
                                :rules="[rules.required]"
                            />

                            <v-select
                                v-model="formData.type"
                                :items="[
                                    { label: $t('public'), value: 'public' },
                                    { label: $t('private'), value: 'private' }
                                ]"
                                item-title="label"
                                item-value="value"
                                :label="$t('university type')"
                                variant="outlined"
                                density="compact"
                                class="pb-4"
                                :rules="[rules.required]"
                            />

                            <v-select
                                v-model="selectedFaculties"
                                :items="UniversityRepository.faculties"
                                item-title="name"
                                item-value="id"
                                multiple
                                chips
                                closable-chips
                                variant="outlined"
                                density="compact"
                                class="pb-4"
                                persistent-hint
                                :label="$t('Faculties')"
                            >
                                <template v-slot:selection="{ item, index }">
                                    <v-chip
                                        v-if="index < 2"
                                        closable
                                        @click:close="removeFaculty(item.value)"
                                    >
                                        {{ item.title }}
                                    </v-chip>
                                    <span
                                        v-if="index === 2"
                                        class="text-grey text-caption"
                                    >
                                        +{{ selectedFaculties.length - 2 }} more
                                    </span>
                                </template>
                            </v-select>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6 gap-2">
                        <v-btn
                            color="primary"
                            class="px-4"
                            @click="save"
                            :loading="isSaving"
                        >
                            {{
                                UniversityRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("form.submit")
                            }}
                        </v-btn>
                    </div>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import { useUniversityRepository } from "@/store/UniversityRepository";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" ? "rtl" : "ltr"));

const UniversityRepository = useUniversityRepository();
const formRef = ref(null);
const formIsValid = ref(false);
const isSaving = ref(false);
const loadingFaculties = ref(false);

// Main form data
const formData = reactive({
    id: null,
    name: "",
    province_id: null,
    type: null,
});

// Separate array for selected faculty IDs
const selectedFaculties = ref([]);

// Initialize form data
const resetForm = () => {
    formData.id = null;
    formData.name = "";
    formData.province_id = null;
    formData.type = null;
    selectedFaculties.value = [];
    formRef.value?.reset();
};

// Watch for university changes (edit mode)
watch(
    () => UniversityRepository.university,
    async (newVal) => {
        if (newVal && UniversityRepository.isEditMode) {
            // Load faculties if not already loaded
            if (UniversityRepository.faculties.length === 0) {
                loadingFaculties.value = true;
                await UniversityRepository.FetchFaculties();
                loadingFaculties.value = false;
            }

            // Set basic form data
            formData.id = newVal.id;
            formData.name = newVal.name;
            formData.province_id = newVal.province?.id || null;
            formData.type = newVal.type || null;
            
            // Set selected faculties (just IDs)
            selectedFaculties.value = newVal.faculties?.map(f => f.id) || [];
        } else {
            resetForm();
        }
    },
    { immediate: true, deep: true }
);

const rules = {
    required: (value) => !!value || t("validation.required"),
    name: (value) =>
        /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) || t("validation.valid_name"),
};

// Load initial data
const loadData = async () => {
    try {
        await UniversityRepository.FetchProvinces();
        await UniversityRepository.FetchFaculties();
    } catch (error) {
        console.error("Error loading data:", error);
    }
};

// Remove faculty from selection
const removeFaculty = (facultyId) => {
    selectedFaculties.value = selectedFaculties.value.filter(id => id !== facultyId);
};

// Save function
const save = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    isSaving.value = true;
    try {
        const payload = {
            ...formData,
            faculty_ids: selectedFaculties.value, // Always send faculty IDs
        };

        if (UniversityRepository.isEditMode) {
            await UniversityRepository.UpdateUniversity(formData.id, payload);
        } else {
            await UniversityRepository.CreateUniversity(payload);
        }

        closeDialog();
    } catch (error) {
        console.error("Error saving university:", error);
    } finally {
        isSaving.value = false;
    }
};

const closeDialog = (isActive = null) => {
    if (isActive?.value !== undefined) {
        isActive.value = false;
    } else {
        UniversityRepository.createDialog = false;
    }
    resetForm();
};

onMounted(loadData);
</script>

<style scoped>
.v-chip {
    margin-right: 4px;
    margin-bottom: 4px;
}
</style>