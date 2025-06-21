<template>
    <div :dir="dir">
      <v-dialog
        transition="dialog-top-transition"
        width="50rem"
        v-model="UniversityRepository.createDialog"
      >
        <template v-slot:default="{ isActive }">
          <v-card class="px-3">
            <v-card-title class="px-2 pt-4 d-flex justify-space-between">
              <h2 class="font-weight-bold pl-4">
                {{ UniversityRepository.isEditMode ? $t('form.update') : $t('create') }}
              </h2>
              <v-btn variant="text" @click="isActive.value = false">
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
  
                <v-combobox
                  v-model="formData.type"
                  :items="['public', 'private']"
                  :item-title="(item) => $t(`${item}`)"
                  :label="$t('university type')"
                  variant="outlined"
                  density="compact"
                  class="pb-4"
                  :rules="[rules.required]"
                />
              </v-form>
            </v-card-text>
  
            <div class="d-flex flex-row-reverse mb-6 mx-6">
              <v-btn color="primary" class="px-4" @click="save">
                {{ UniversityRepository.isEditMode ? $t('form.update') : $t('form.submit') }}
              </v-btn>
            </div>
          </v-card>
        </template>
      </v-dialog>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, onMounted, computed } from "vue";
  import { useUniversityRepository } from "@/store/UniversityRepository";
  import { useI18n } from "vue-i18n";
  
  const { t, locale } = useI18n();
  const dir = computed(() => locale.value === "fa" ? "rtl" : "ltr");
  
  const UniversityRepository = useUniversityRepository();
  const formRef = ref(null);
  const formIsValid = ref(false);
  
  onMounted(() => {
    UniversityRepository.FetchProvinces();
  });
  
  const formData = reactive({
    id: UniversityRepository.university.id || null,
    name: UniversityRepository.university.name || "",
    province_id: UniversityRepository.university.province?.id || null,
    type: UniversityRepository.university.type || null,
  });
  
  const rules = {
    required: (value) => !!value || t('validation.required'),
    name: (value) =>
      /^[a-zA-Z\u0600-\u06FF\s]*$/.test(value) ||
      t('validation.valid_name'),
  };
  
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