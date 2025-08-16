<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="ProvinceRepository.createDialog"
            class="rtl-dialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card :dir="dir" class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                ProvinceRepository.isEditMode
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
                        <v-form ref="formRef" class="pt-4">
                            <v-text-field
                                v-model="formData.name"
                                variant="outlined"
                                :label="$t('form.name')"
                                class="pb-4"
                                density="compact"
                                :rules="[rules.required]"
                            ></v-text-field>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn color="primary" class="px-4" @click="save">
                            {{
                                ProvinceRepository.isEditMode
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
import { ref, reactive } from "vue";
import { useProvinceRepository } from "@/store/ProvinceRepository";
import { useI18n } from "vue-i18n";
import { computed } from "vue";

const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" ? "rtl" : "ltr"));
const ProvinceRepository = useProvinceRepository();
const formRef = ref(null);

const formData = reactive({
    id: ProvinceRepository.province.id,
    name: ProvinceRepository.province.name,
});

const rules = {
    required: (value) => !!value || t("validation.required"),
    name: (value) =>
        /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) || t("validation.valid_name"),
};

const save = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    try {
        if (ProvinceRepository.isEditMode) {
            await ProvinceRepository.UpdateProvince(formData.id, formData);
        } else {
            await ProvinceRepository.CreateProvince(formData);
        }
    } catch (error) {
        // Error is already handled by the repository
    }
};
</script>
