<template>
    <v-toolbar density="compact" class="" color="white" :order="order">
        <header class="tool h-10" :dir="dir">
            <div>
                <v-btn icon="mdi mdi-menu" @click="toggleSidebar"></v-btn>
                <span dir="rtl" class="breadCrumbSub"> {{ subTitle }}</span>
                &nbsp; - &nbsp;
                <span dir="rtl" class="breadCrumbTitle"> {{ mainTitle }}</span>
            </div>

            <nav class="icon-bar" aria-label="Utility navigation">
                <!-- Fullscreen Button -->
                <div
                    class="icon-wrapper"
                    @click="toggleFullscreen"
                    style="cursor: pointer"
                    role="button"
                    aria-label="Toggle fullscreen"
                    title="Toggle fullscreen"
                >
                    <svg
                        width="20"
                        height="20"
                        viewBox="0 0 16 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink"
                    >
                        <rect
                            width="16"
                            height="16"
                            fill="url(#pattern0_5826_4251)"
                        />
                        <defs>
                            <pattern
                                id="pattern0_5826_4251"
                                patternContentUnits="objectBoundingBox"
                                width="1"
                                height="1"
                            >
                                <use
                                    xlink:href="#image0_5826_4251"
                                    transform="scale(0.01)"
                                />
                            </pattern>
                            <image
                                id="image0_5826_4251"
                                width="100"
                                height="100"
                                xlink:href="data:image/png;base64,..."
                            />
                        </defs>
                    </svg>
                </div>

                <!-- Language Switcher -->
                <div class="icon-wrapper">
                    <v-menu transition="scale-transition">
                        <template #activator="{ props }">
                            <v-btn
                                icon="mdi-web"
                                flat
                                fluid
                                class="icon bg-head"
                                size="small"
                                v-bind="props"
                            ></v-btn>
                        </template>

                        <v-list>
                            <v-list-item
                                v-for="item in items"
                                :key="item.title"
                                @click="changeLanguage(item.lang)"
                            >
                                <div
                                    class="d-flex justify-between align-center gap-4"
                                >
                                    <v-list-item-icon>
                                        <img
                                            :src="item.icon"
                                            alt="Language Icon"
                                            class="icon-size"
                                            width="22"
                                            height="22"
                                        />
                                    </v-list-item-icon>
                                    <v-list-item-title>{{
                                        item.title
                                    }}</v-list-item-title>
                                </div>
                            </v-list-item>
                        </v-list>
                    </v-menu>
                </div>
            </nav>
        </header>
    </v-toolbar>
</template>

<script setup>
import { ref, computed, watch } from "vue";
defineProps(["mainTitle", "subTitle"]);
const order = 0;
</script>

<style scoped>
.icon-bar {
    display: flex;
    gap: 0.5rem;
}

.icon-wrapper {
    background-color: #112f531a;
    padding: 0.4rem;
    border-radius: 0.5rem;
    display: flex;
    justify-content: center;
    align-items: center;
}

.tool {
    display: flex;
    width: 100%;
    justify-content: space-between;
}

.icon {
    width: 24px;
    height: 24px;
    color: #000;
}

.breadCrumbTitle {
    cursor: pointer;
}

.breadCrumbSub {
    opacity: 0.6;
    cursor: pointer;
}
</style>
