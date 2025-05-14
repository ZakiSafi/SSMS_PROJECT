<template>
  <v-layout class="rounded rounded-md" flat fluid >
      
      
      <v-navigation-drawer
      permanent
      floating
      >
      
      <NavigationDrawer/>
      
    </v-navigation-drawer>
    
    
    
    
    <v-main class="d-flex flex-col bg-g" style="min-height: 300px">

          <v-card

              variant="flat"
             
              class="min-h-screen d-flex flex-col m-4 ml-4 py-4 px-4 "

          >

              <router-view></router-view>

          </v-card>

      </v-main>

  </v-layout>

</template>



<script setup>

import { ref, watch, computed } from "vue";

import { useRoute } from "vue-router"; // Import to get the current route

import NavigationDrawer from "./components/NavigationDrawer.vue";

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

// const vCardStyle = computed(() => {

//   console.log(route.path);

//   return route.path === "/dashboard" // Replace 'dashboard' with the actual name of your route

//       ? "background-color:#f8f8f8"

//       : "background-color:white";

// });

</script>



<style scoped>


</style>