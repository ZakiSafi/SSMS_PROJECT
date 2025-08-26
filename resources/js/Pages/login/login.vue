<template>
    <div class="h-screen grid grid-cols-2 overflow-hidden" :dir="dir">
        <!-- Left Image Section -->
        <div class="h-screen">
            <img
                src="../../../../public/assets/Flux_Dev_A_modern_educational_dashboard_concept_logo_Left_side_1.jpg"
                alt="Jawad"
                class="object-cover h-screen w-full"
            />
        </div>

        <!-- Right Login Form Section -->
        <div class="relative bg-white">
            <!-- Language Switcher Button -->
            <div class="absolute top-4 right-0">
                <v-menu transition="scale-transition" offset-y>
                    <template #activator="{ props }">
                        <v-btn
                            color="primary"
                            variant="text"
                            size="small"
                            v-bind="props"
                            :title="$t('language_switcher')"
                            class="language-btn"
                        >
                            <span class="mr-1">{{
                                $t("select_language")
                            }}</span>
                            <v-icon left>mdi-web</v-icon>
                            <v-icon size="small">mdi-chevron-down</v-icon>
                        </v-btn>
                    </template>

                    <v-list density="compact" class="py-1">
                        <v-list-item
                            v-for="item in languageItems"
                            :key="item.lang"
                            @click="changeLanguage(item.lang)"
                            :class="{ 'active-language': locale === item.lang }"
                        >
                            <template #prepend>
                                <v-avatar size="24" class="mr-3">
                                    <img
                                        :src="item.icon"
                                        :alt="item.title"
                                        class="object-contain"
                                    />
                                </v-avatar>
                            </template>

                            <v-list-item-title class="text-capitalize">
                                {{ $t(item.title) }}
                            </v-list-item-title>

                            <template #append v-if="locale === item.lang">
                                <v-icon color="primary" size="small"
                                    >mdi-check</v-icon
                                >
                            </template>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>

            <!-- Decorations -->
            <img
                src="../../../../public/assets/Vector.png"
                alt="Decoration"
                class="absolute bottom-0 left-0"
            />
            <img
                src="../../../../public/assets/Group 688.png"
                alt="Decoration"
                class="absolute bottom-0 right-0"
            />

            <!-- Login Form -->
            <v-form
                @submit.prevent="loginFunc"
                ref="formRef"
                :validate-on="'submit'"
            >
                <div
                    class="mt-[120px] mx-auto flex flex-column items-center justify-center"
                    style="width: 400px"
                >
                    <h1
                        class="text-center text-[64px] font-bold text-[#009EE2]"
                    >
                        {{ $t("welcome") }}
                    </h1>
                    <p class="text-center text-medium-emphasis mb-10">
                        {{ $t("login_with_email") }}
                    </p>

                    <!-- Email Field -->
                    <v-text-field
                        v-model="formData.email"
                        :label="t('email_id')"
                        :prepend-inner-icon="
                            dir === 'ltr' ? 'mdi-email-outline' : ''
                        "
                        :append-inner-icon="
                            dir === 'rtl' ? 'mdi-email-outline' : ''
                        "
                        variant="outlined"
                        color="#009EE2"
                        density="comfortable"
                        class="w-full"
                        rounded="lg"
                        bg-color="#fff"
                        flat
                        :rules="emailRules"
                        required
                        :placeholder="t('Email')"
                        ref="emailField"
                        :dir="dir"
                        :class="dir === 'rtl' ? 'input-rtl' : ''"
                    />

                    <!-- Password Field -->
                    <v-text-field
                        v-model="formData.password"
                        :label="t('password')"
                        :type="visible ? 'text' : 'password'"
                        :prepend-inner-icon="
                            dir === 'ltr' ? 'mdi-lock-outline' : ''
                        "
                        :append-inner-icon="
                            dir === 'rtl'
                                ? 'mdi-lock-outline'
                                : visible
                                ? 'mdi-eye-off'
                                : 'mdi-eye'
                        "
                        variant="outlined"
                        density="comfortable"
                        @click:append-inner="visible = !visible"
                        color="secondary"
                        class="w-full mb-6"
                        rounded="lg"
                        bg-color="#fff"
                        flat
                        :rules="passwordRules"
                        required
                        :placeholder="t('enter your password')"
                        ref="passwordField"
                        :dir="dir"
                        :class="dir === 'rtl' ? 'input-rtl' : ''"
                    />

                    <!-- Submit Button -->
                    <v-btn
                        type="submit"
                        color="secondary"
                        size="large"
                        class="font-bold text-white w-full"
                        elevation="0"
                        rounded="lg"
                        :loading="AuthRepository.loading"
                        :disabled="AuthRepository.loading"
                    >
                        {{ $t("login") }}
                    </v-btn>
                </div>
            </v-form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from "vue";
import { useAuthRepository } from "../../store/AuthRepository";
import { useI18n } from "vue-i18n";

const { t, locale } = useI18n();
const AuthRepository = useAuthRepository();
const formData = reactive({ email: "", password: "" });
const visible = ref(false);
const formRef = ref(null);
const isRtl = ref(false);
const emailField = ref(null);
const passwordField = ref(null);

// Change direction based on language
const dir = computed(() => (locale.value === "en" ? "ltr" : "rtl"));

// Initialize language
onMounted(() => {
    const savedLang = localStorage.getItem("locale");
    if (savedLang) {
        locale.value = savedLang;
        isRtl.value = savedLang !== "en";
    }
});

// Language switcher
const languageItems = ref([
    { title: "english", lang: "en", icon: "/assets/english.png" },
    { title: "dari", lang: "fa", icon: "/assets/dari.png" },
    { title: "pashto", lang: "ps", icon: "/assets/ps.png" },
]);

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang);
    isRtl.value = lang !== "en";
};

// Validation rules
const emailRules = [
    (v) => !!v || t("validations.email_required"),
    (v) => /.+@.+\..+/.test(v) || t("validations.email_invalid"),
];

const passwordRules = [
    (v) => !!v || t("validations.password_required"),
    (v) => (v && v.length >= 6) || t("validations.password_min"),
];

// Check tampering
const checkFormTampering = () => {
    try {
        if (
            !document.contains(emailField.value?.$el) ||
            !document.contains(passwordField.value?.$el)
        ) {
            return true;
        }

        const emailEl = emailField.value?.$el.querySelector("input");
        const passwordEl = passwordField.value?.$el.querySelector("input");

        if (!emailEl || !passwordEl) return true;

        if (
            emailEl.disabled ||
            passwordEl.disabled ||
            emailEl.readOnly ||
            passwordEl.readOnly
        ) {
            return true;
        }

        const emailStyle = window.getComputedStyle(emailEl);
        const passwordStyle = window.getComputedStyle(passwordEl);

        if (
            emailStyle.display === "none" ||
            emailStyle.visibility === "hidden" ||
            passwordStyle.display === "none" ||
            passwordStyle.visibility === "hidden"
        ) {
            return true;
        }

        return false;
    } catch (error) {
        console.error("Error checking form tampering:", error);
        return true;
    }
};

// Login
const loginFunc = async () => {
    if (checkFormTampering()) {
        alert(
            "Security violation detected. Please refresh the page and try again."
        );
        return;
    }

    formRef.value?.resetValidation();
    const { valid } = await formRef.value?.validate();
    if (!valid) return;

    try {
        await AuthRepository.login(formData);
    } catch (error) {
        console.error("Login failed", error);
    }
};
</script>

<style>
/* Align input text to the right for RTL */
.input-rtl input {
    text-align: right !important;
}
/* Align label text in RTL mode */
.input-rtl .v-label {
    right: 16px !important;
    left: auto !important;
    text-align: right !important;
}
</style>
