<template>
    <div :dir="dir">
        <v-dialog
            transition="dialog-top-transition"
            width="40rem"
            v-model="UserRepository.createDialog"
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title
                        class="px-2 pt-4 d-flex justify-space-between"
                    >
                        <h2 class="font-weight-bold pl-4">
                            {{
                                UserRepository.isEditMode
                                    ? $t("update user")
                                    : $t("create user")
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
                                :placeholder="$t('name')"
                                input-class="compact"
                                :required="true"
                            />

                            <RTLInput
                                v-model="formData.email"
                                :placeholder="$t('email')"
                                type="email"
                                input-class="compact"
                                :required="true"
                            />

                            <RTLInput
                                v-model="formData.password"
                                :placeholder="$t('form.password')"
                                type="password"
                                input-class="compact"
                                :required="true"
                            />

                            <RTLSelect
                                v-model="formData.university_id"
                                :items="UserRepository.universities"
                                item-title="name"
                                item-value="id"
                                :placeholder="$t('university')"
                                select-class="compact"
                                :required="true"
                            />

                            <RTLSelect
                                v-model="formData.role_id"
                                :items="UserRepository.roles"
                                item-title="name"
                                item-value="id"
                                :placeholder="$t('role')"
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
                                UserRepository.isEditMode
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
import { ref, reactive, onMounted, computed } from "vue";
import { useUserRepository } from "@/store/UserRepository";
import { useI18n } from "vue-i18n";
import RTLInput from "@/components/RTLInput.vue";
import RTLSelect from "@/components/RTLSelect.vue";
const { t, locale } = useI18n();
const dir = computed(() => (locale.value === "fa" || locale.value === "pa" ? "rtl" : "ltr"));

const UserRepository = useUserRepository();
const formRef = ref(null);

const formData = reactive({
    id: UserRepository.user.id,
    name: UserRepository.user.name,
    email: UserRepository.user.email,
    password: UserRepository.user.password,
    university_id: UserRepository.user.university?.id || null,
    role_id: UserRepository.user.role?.id || null,
});

const rules = {
    required: (value) => !!value || t("validation.required"),
    email: (value) => /.+@.+\..+/.test(value) || t("validation.email_invalid"),
};

onMounted(() => {
    UserRepository.fetchUniversities();
    UserRepository.fetchRoles();
});

const save = async () => {
    // Manual validation since we're using custom components
    if (!formData.name || !formData.email || !formData.password || !formData.university_id || !formData.role_id) {
        return;
    }
    if (!/.+@.+\..+/.test(formData.email)) {
        return;
    }

    try {
        if (UserRepository.isEditMode) {
            await UserRepository.updateUser(formData.id, formData);
        } else {
            await UserRepository.createUser(formData);
        }
    } catch (error) {
        // Error is already handled by the repository
    }
};
</script>

<style scoped>
.borderStyle {
    border: 1px solid #999;
}
</style>
