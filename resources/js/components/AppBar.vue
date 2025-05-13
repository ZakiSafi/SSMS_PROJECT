<template>
  <v-card :dir="isRtl ? 'rtl' : 'ltr'" :elevation="0" class="rounded-xl">
    <template v-slot:prepend>
      <v-btn
        icon="mdi-drag"
        flat
        fluid
        class="drag"
        size="small"
        @click="toggleSidebar"
      ></v-btn>
      <div class="text-[12px]">
        {{ pageTitle }}
        <span v-if="pageSubtitle" class="text-[14px]">{{ pageSubtitle }}</span>
      </div>
    </template>

    <template v-slot:append>
      <div class="d-flex gap-4">
        <div class="d-flex justify-space-around">
          <v-menu transition="scale-transition">
            <template #activator="{ props }">
              <v-btn
                icon="mdi-web"
                flat
                fluid
                class="icon bg-head"
                size="small"
                height="4.7vh"
                width="4.7vh"
                v-bind="props"
              ></v-btn>
            </template>

            <v-list>
              <v-list-item
                v-for="item in items"
                :key="item.title"
                @click="changeLanguage(item.lang)"
              >
                <div class="d-flex justify-between align-center gap-4">
                  <v-list-item-icon>
                    <img
                      :src="item.icon"
                      alt="Language Icon"
                      class="icon-size"
                      height="22"
                      width="22"
                    />
                  </v-list-item-icon>
                  <v-list-item-title>{{ item.title }}</v-list-item-title>
                </div>
              </v-list-item>
            </v-list>
          </v-menu>
        </div>

      </div>
    </template>
  </v-card>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
  pageTitle: {
    type: String,
    default: ""
  },
  pageSubtitle: {
    type: String,
    default: ""
  }
});

const items = ref([
  { title: 'english', lang: "en", icon: "/assets/english.png" },
  { title: 'dari', lang: "fa", icon: "/assets/dari.png" },
]);

// Example functions - keep your actual implementations
const toggleSidebar = () => console.log("Toggle sidebar");
const toggleFullscreen = () => console.log("Toggle fullscreen");
const changeLanguage = (lang) => console.log("Change language to", lang);
const isRtl = ref(false);
</script>

<style scoped>

.icon {
  margin-right: 1px;
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
</style>