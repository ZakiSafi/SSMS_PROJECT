<template>
    <v-layout class="rounded rounded-md side">
        <v-navigation-drawer
            v-model="drawer"
            :rail="rail"
            permanent

            floating

            :location="dir"

            class="sidebar"

        >

            <!-- <NavigationDrawer  :dir="isRtl ? 'rtl' : 'ltr'"  /> -->

        </v-navigation-drawer>



        <v-main class="d-flex flex-col" style="min-height: 300px">

            <v-card

                variant="flat"

                elevation="1"

                :style="vCardStyle"

                class="min-h-screen d-flex flex-col m-4 ml-4 py-4 px-4 rounded-xl side"

            >

                <router-view></router-view>

            </v-card>

        </v-main>

    </v-layout>

</template>



<script setup>

import { ref, watch, computed } from "vue";

import { useRoute } from "vue-router"; // Import to get the current route

import NavigationDrawer from "../components/NavigationDrawer.vue";

// import { useI18n } from "vue-i18n";

// const {t, locale } = useI18n();

// import { useAuthRepository } from "@/repositories/AuthRepository.js";

// const isRtl = ref(locale.value === "fa"); // Assuming 'fa' is the code for Dari

// watch(locale, (newLocale) => {

//     isRtl.value = newLocale === "fa";

// });

// const authRepo = useAuthRepository();

// const drawer = ref(true);

// const rail = ref(authRepo.rail);



// watch(

//     () => authRepo.rail,

//     (newValue) => {

//         rail.value = newValue;

//     }

// );



// const dir = computed(() => {

//     if (locale.value === "fa") {

//         return "right"; // Reverse the order for Farsi

//     }



//     return "left";

// });



// Use Vue Router's useRoute to determine the current route

const route = useRoute();

const vCardStyle = computed(() => {

    console.log(route.path);

    return route.path === "/dashboard" // Replace 'dashboard' with the actual name of your route

        ? "background-color:#f8f8f8"

        : "background-color:white";

});

</script>



<style scoped>
.side 
{
    background-color: #770707 !important;
}
.scrollable-content {

    max-height: 80vh;

    overflow-y: auto;

    /* direction: ltr; */

}

.scrollable-content::-webkit-scrollbar {

    width: 4px;

    display: none;

}



.scrollable-content::-webkit-scrollbar-track {

    background: #f1f1f1;

}



.scrollable-content::-webkit-scrollbar-thumb {

    background: #888;

    border-radius: 4px;

}



.scrollable-content::-webkit-scrollbar-thumb:hover {

    background: #555;

}

</style>