<template>
    <v-card>
        <v-layout>
            <v-navigation-drawer
                v-model="drawer"
                :rail="rail"
                permanent
                :width="200"
                :rail-width="56"

                class="custom-drawer pt-6"
                @click="rail = false"
            >


                <v-list density="compact" nav>
                    <v-list-item
                        prepend-icon="mdi-home-city"
                        title="Home"
                        value="home"
                    >
                        <template v-slot:append>
                            <v-btn
                                prepend-icon="mdi-chevron-left"
                                variant="text"
                                @click.stop="rail = !rail"
                            ></v-btn> </template
                    ></v-list-item>
                    <v-list-item
                        prepend-icon="mdi-home-city"
                        title="Department"
                        value="Department"
                        to="/department"
                    ></v-list-item>
                    <v-list-item
                        prepend-icon="mdi-account"
                        title="My Account"
                        value="account"
                    ></v-list-item>
                    <v-list-item
                        prepend-icon="mdi-account-group-outline"
                        title="Users"
                        value="users"
                    ></v-list-item>
                </v-list>
                <v-divider></v-divider>
                <v-list-item
                    prepend-avatar="https://randomuser.me/api/portraits/men/85.jpg"
                    title="Jawad Mujaddidi"
                    nav
                >
                </v-list-item>
            </v-navigation-drawer>
        </v-layout>
    </v-card>
</template>

<script setup>
import { ref } from "vue";

const drawer = ref(true);
const rail = ref(false);
</script>

<style scoped>
.custom-drawer {
    background-color: #f5f5f5; /* Light background for a clean look */
    border-right: 1px solid #ddd; /* Subtle border for separation */
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1); /* Add a slight shadow for depth */
}

.v-list-item {
    transition: all 0.3s ease; /* Smooth hover effect */
}

.v-list-item:hover {
    background-color: #e0e0e0; /* Highlight on hover */
    border-radius: 8px; /* Rounded corners for a modern look */
}

.v-btn {
    color: #1976d2; /* Primary color for buttons */
}
</style>
