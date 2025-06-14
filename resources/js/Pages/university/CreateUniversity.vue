<template>
    <div dir="rtl">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="UniversityRepository.createDialog"
            class="rtl-dialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                UniversityRepository.isEditMode
                                    ? "Update"
                                    : "Create"
                            }}
                        </h2>
                        <v-btn variant="text" @click="isActive.value = false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider class="border-opacity-100 mx-6"></v-divider>

                    <v-card-text>
                        <v-form
                            ref="formRef"
                            class="pt-4"
                            v-model="formIsValid"
                        >
                            <v-text-field
                                v-model="formData.name"
                                variant="outlined"
                                label="Name"
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
                                label="Province"
                                density="compact"
                                class="pb-4"
                                :rules="[rules.required]"
                            />

                            <v-select
                                v-model="formData.type"
                                :items="['public', 'private']"
                                item-value="type"
                                item-title="type"
                                label="University Type"
                                variant="outlined"
                                density="compact"
                                class="pb-4"
                                :rules="[rules.required]"
                            />

                            
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn color="primary" class="px-4" @click="save">
                            {{
                                UniversityRepository.isEditMode
                                    ? "Update"
                                    : "Submit"
                            }}
                        </v-btn>
                    </div>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from "vue";
import { useUniversityRepository } from "@/store/UniversityRepository";

const UniversityRepository = useUniversityRepository();
const formRef = ref(null);
const formIsValid = ref(false);

// Fetch provinces and other needed data
onMounted(() => {
    UniversityRepository.FetchProvinces();
    UniversityRepository.FetchUniversityTypes?.(); // Optional: only if needed
});

// Initial form data
const formData = reactive({
    id: UniversityRepository.university.id || null,
    name: UniversityRepository.university.name || "",
    province_id: UniversityRepository.university.province?.id || null,
    type: UniversityRepository.university.type || null,
    teachers: UniversityRepository.university.teachers || "",
});

// Validation rules
const rules = {
    required: (value) => !!value || "This field is required.",
    name: (value) =>
        /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) ||
        "Please enter a valid name.",
    numeric: (value) =>
        /^\d+$/.test(value) || "This field must be a valid number.",
};

// Save function
const save = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    if (UniversityRepository.isEditMode) {
        await UniversityRepository.UpdateUniversity(formData.id, formData);
    } else {
        await UniversityRepository.CreateUniversity(formData);
    }
};
</script>
