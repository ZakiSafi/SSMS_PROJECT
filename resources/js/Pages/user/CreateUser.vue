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
                        <v-form ref="formRef" class="pt-4">
                            <v-text-field
                                v-model="formData.name"
                                :label="$t('name')"
                                variant="outlined"
                                :rules="[rules.required]"
                            ></v-text-field>

                            <v-text-field
                                v-model="formData.email"
                                :label="$t('email')"
                                variant="outlined"
                                :rules="[rules.required, rules.email]"
                            ></v-text-field>

                            <v-text-field
                                v-model="formData.password"
                                :label="$t('form.password')"
                                type="password"
                                variant="outlined"
                                :rules="[rules.required]"
                            ></v-text-field>

                            <v-select
                                v-model="formData.university_id"
                                :items="UserRepository.universities"
                                :label="$t('university')"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                :rules="[rules.required]"
                            ></v-select>

                            <v-select
                                v-model="formData.role_id"
                                :items="UserRepository.roles"
                                :label="$t('role')"
                                item-title="name"
                                item-value="id"
                                variant="outlined"
                                :rules="[rules.required]"
                            ></v-select>
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn color="primary" class="px-4" @click="save">
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
import { ref, reactive, onMounted } from "vue";
import { useUserRepository } from "@/store/UserRepository";

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
    required: (value) => !!value || "This field is required.",
    email: (value) => /.+@.+\..+/.test(value) || "Email must be valid",
};

onMounted(() => {
    UserRepository.fetchUniversities();
    UserRepository.fetchRoles();
});

const save = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

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
