<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="FacultyRepository.createDialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                FacultyRepository.isEditMode
                                    ? $t("form.update")
                                    : $t("form.create")
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
                            @submit.prevent="save"
                        >
                            <RTLInput
                                v-model="formData.name"
                                :placeholder="$t('Name')"
                                input-class="compact"
                                :required="true"
                            />
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn
                            type="submit"
                            color="primary"
                            class="px-4"
                            @click="save"
                        >
                            {{
                                FacultyRepository.isEditMode
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
import { ref, reactive, computed } from "vue";
import { onMounted } from "vue";
import { useFacultyRepository } from "@/store/FacultyRepository";
import { useI18n } from "vue-i18n";
import RTLInput from "@/components/RTLInput.vue";

const { locale } = useI18n();
const dir = computed(() => (locale.value === "fa" || locale.value === "pa" ? "rtl" : "ltr"));

const FacultyRepository = useFacultyRepository();

onMounted(() => {
    FacultyRepository.FetchUniversities();
});

const formRef = ref(null);

const formData = reactive({
    id: FacultyRepository.faculty.id,
    name: FacultyRepository.faculty.name,
    university_id: FacultyRepository.faculty.university?.id || null,
});

const rules = {
    required: (value) => !!value || "This field is required.",
    name: (value) =>
        /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) ||
        "Please enter a valid name.",
};

const save = async () => {
    // Manual validation
    if (!formData.name) {
        return;
    }
    
    if (FacultyRepository.isEditMode) {
        await FacultyRepository.UpdateFaculty(formData, formData.id);
    } else {
        await FacultyRepository.CreateFaculty(formData);
    }
};
</script>
