<template>
  <v-card :dir="isRtl ? 'rtl' : 'ltr'" :elevation="0" class="rounded-xl">
    <template v-slot:prepend>
      <v-btn icon="mdi-drag" flat fluid class="drag" size="small" @click="toggleSidebar"></v-btn>
      <div class="text-[12px]">
        {{ pageTitle }}
        <span v-if="pageSubtitle" class="text-[14px] ">{{ pageSubtitle }}</span>
      </div>
    </template>

    <template v-slot:append>
      <div class="d-flex align-center">
        <!-- Language Switcher -->
        <v-menu transition="scale-transition">
          <template #activator="{ props }">
            <v-btn icon="mdi-web" flat class="icon bg-head mr-4" size="small" height="4.7vh" width="4.7vh"
              v-bind="props"></v-btn>
          </template>

          <v-list>
            <v-list-item v-for="item in items" :key="item.title" @click="changeLanguage(item.lang)">
              <div class="d-flex justify-between align-center gap-4">
                <v-list-item-icon>
                  <img :src="item.icon" alt="Language Icon" class="icon-size" height="22" width="22" />
                </v-list-item-icon>
                <v-list-item-title>{{ item.title }}</v-list-item-title>
              </div>
            </v-list-item>
          </v-list>
        </v-menu>

        <!-- Logout Button -->
        <v-dialog max-width="400">
          <template v-slot:activator="{ props: activatorProps }">
            <v-btn flat class="icon bg-head " size="small" height="4.7vh" width="4.7vh" icon="mdi-logout"
              v-bind="activatorProps">
            </v-btn>
          </template>

          <template v-slot:default="{ isActive }">
            <v-card>
              <v-card-title class="px-2 pt-2 d-flex justify-space-between">
                <h3 class="font-weight-bold pl-4">Logout</h3>
                <v-btn variant="text" class="font-weight-bold " @click="isActive.value = false">
                  <v-icon>mdi-close</v-icon></v-btn>
              </v-card-title>

              <div class="d-flex flex-column  justify-center align-center "><img
                  src="../../../public/assets/Park.jpg" alt=""
                  class="object-cover w-[6rem] h-[6rem] rounded-full  ">
                <h3 class="mt-6">Jawad@Gmail.com</h3>
              </div>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn text="Logout" @click="handleLogout"></v-btn>
              </v-card-actions>
            </v-card>
          </template>
        </v-dialog>
      </div>
    </template>
  </v-card>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  pageTitle: { type: String, default: "" },
  pageSubtitle: { type: String, default: "" }
});

const items = ref([
  { title: "english", lang: "en", icon: "/assets/english.png" },
  { title: "dari", lang: "fa", icon: "/assets/dari.png" }
]);

const toggleSidebar = () => console.log("Toggle sidebar");
const changeLanguage = (lang) => console.log("Change language to", lang);
const handleLogout = () => {
  console.log("Logging out...");
  // Add your actual logout logic here
};

const isRtl = ref(false);
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
