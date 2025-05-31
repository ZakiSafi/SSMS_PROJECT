<template>
    <v-card :dir="isRtl ? 'rtl' : 'ltr'" :elevation="0" class="rounded-xl">
        <template v-slot:prepend>
            <h1 class="text-[18px]">
                {{ $t(pageTitle) }}
                <span v-if="pageSubtitle" class="text-[14px]">{{
                    $t(pageSubtitle)
                }}</span>
            </h1>
        </template>

        <template v-slot:append>
            <div class="d-flex align-center gap-4">
                <!-- Language Switcher -->
                <v-menu transition="scale-transition">
                    <template #activator="{ props }">
                        <v-btn
                            :icon="$t('mdi-web')"
                            flat
                            class="icon bg-head mx-4"
                            size="small"
                            height="4.7vh"
                            width="4.7vh"
                            v-bind="props"
                            :title="$t('language_switcher')"
                        ></v-btn>
                    </template>

                    <v-list>
                        <v-list-item
                            v-for="item in items"
                            :key="$t(item.title)"
                            @click="changeLanguage(item.lang)"
                        >
                            <div
                                class="d-flex justify-between align-center gap-4"
                            >
                                <v-list-item-icon>
                                    <img
                                        :src="item.icon"
                                        :alt="$t('language_icon')"
                                        class="icon-size"
                                        height="22"
                                        width="22"
                                    />
                                </v-list-item-icon>
                                <v-list-item-title>{{
                                    $t(item.title)
                                }}</v-list-item-title>
                            </div>
                        </v-list-item>
                    </v-list>
                </v-menu>

                <!-- Logout Button -->
                <v-dialog max-width="400">
                    <template v-slot:activator="{ props: activatorProps }">
                        <v-btn
                            flat
                            class="icon bg-head"
                            size="small"
                            height="4.7vh"
                            width="4.7vh"
                            :icon="$t('mdi-logout')"
                            v-bind="activatorProps"
                            :title="$t('logout_button')"
                        >
                        </v-btn>
                    </template>

                    <template v-slot:default="{ isActive }">
                        <v-card>
                            <v-card-title
                                class="px-2 pt-2 d-flex justify-space-between"
                            >
                            <h3 class="font-weight-bold pl-4">{{ $t("logout") }}</h3>

                                <v-btn
                                    variant="text"
                                    class="font-weight-bold"
                                    @click="isActive.value = false"
                                    :aria-label="$t('close_dialog')"
                                >
                                    <v-icon>{{ $t('mdi-close') }}</v-icon></v-btn
                                >
                            </v-card-title>

                            <div
                                class="d-flex flex-column justify-center align-center"
                            >
                                <img
                                    src="../../../public/assets/IMG_20230530_185847_332.jpg"
                                    :alt="$t('user_avatar')"
                                    class="object-cover w-[6rem] h-[6rem] rounded-full"
                                />
                                <h3 class="mt-6">Zakiullahsafi0@gmail.com</h3>
                            </div>

                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    :text="$t('logout')"
                                    @click="handleLogout"
                                ></v-btn>
                            </v-card-actions>
                        </v-card>
                    </template>
                </v-dialog>
            </div>
        </template>
    </v-card>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useAuthRepository } from "../store/AuthRepository";
const AuthRepository = useAuthRepository();
import { useI18n } from "vue-i18n";
const { locale } = useI18n();

const props = defineProps({
    pageTitle: { type: String, default: "" },
    pageSubtitle: { type: String, default: "" },
});

const items = ref([
    { title: "english", lang: "en", icon: "/assets/english.png" },
    { title: "dari", lang: "fa", icon: "/assets/dari.png" },
]);

const isRtl = ref(false);

// Initialize language from localStorage
onMounted(() => {
    const savedLang = localStorage.getItem("locale");
    if (savedLang) {
        locale.value = savedLang;
        isRtl.value = savedLang !== "en";
    }
});

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem("locale", lang);
    isRtl.value = lang !== "en"; // Make direction RTL for fa and ps
};

const handleLogout = () => {
    console.log(t('logging_out_message'));
    AuthRepository.logout();
};
</script>

<style scoped>
.icon {
    border-radius: 9px !important;
}

.icon .v-icon {
    color: white !important;
}

.drag {
    background-color: inherit;
    padding: 12px !important;
}

.drag:hover {
    background-color: inherit;
}

.icon-size {
    border-radius: 50%;
    /* Make it circular */
    object-fit: cover;
}

.logout-avatar {
    padding: 0 !important;
    min-width: auto;
}
</style>
