<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="DepartmentRepository.createDialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                DepartmentRepository.isEditMode
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
                            <RTLSelect
                                v-model="formData.faculty_id"
                                :items="DepartmentRepository.faculties"
                                item-value="id"
                                item-title="name"
                                :placeholder="$t('Faculty')"
                                select-class="compact"
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
                                DepartmentRepository.isEditMode
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
import { useDepartmentRepository } from "@/store/DepartmentRepository";
import { useI18n } from "vue-i18n";
import RTLInput from "@/components/RTLInput.vue";
import RTLSelect from "@/components/RTLSelect.vue";

const { locale } = useI18n();
const dir = computed(() => (locale.value === "fa" || locale.value === "pa" ? "rtl" : "ltr"));

const DepartmentRepository = useDepartmentRepository();
onMounted(() => {
    DepartmentRepository.FetchFaculties();
});
const formRef = ref(null);

const formData = reactive({
    id: DepartmentRepository.department.id,
    name: DepartmentRepository.department.name,
    faculty_id: DepartmentRepository.department.faculty?.id || null,
});

const rules = {
    required: (value) => !!value || "This field is required.",
    name: (value) =>
        /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) ||
        "Please enter a valid name.",
};

const save = async () => {
    // Manual validation
    if (!formData.name || !formData.faculty_id) {
        return;
    }
    
    if (DepartmentRepository.isEditMode) {
        await DepartmentRepository.UpdateDepartment(formData.id, formData);
    } else {
        await DepartmentRepository.CreateDepartment(formData);
    }
};
</script>
