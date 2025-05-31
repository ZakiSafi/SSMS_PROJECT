<template>
    <v-layout class="rounded rounded-md" flat fluid >
        
        
        <v-navigation-drawer
        permanent
        floating
        :location="dir"
        >
        
        <NavigationDrawer/>
        
      </v-navigation-drawer>
      
      
      
      
      <v-main class="d-flex flex-col" style="min-height: 300px">
  
            <v-card
  
                variant="flat"
                class="min-h-screen d-flex flex-col m-4 ml-4 py-4 px-4"
  
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
import { useI18n } from "vue-i18n";
const { t, locale } = useI18n();
  
 
  const dir = computed(() => {
    if (locale.value !== "en") {
        return "right"; 
    }
    return "left";
});
  const route = useRoute();
  
  
  </script>
  
  
  
  <style scoped>
  
  
  </style>