<template>
    <div class="h-screen grid grid-cols-2 overflow-hidden ">
        <!-- Left Image Section -->
        <div class="h-screen">
            <img src="../../../../public/assets/login.jpg" alt="Jawad" class="object-cover h-screen w-full" />
        </div>

        <!-- Right Login Form Section -->
        <div class="relative bg-white">
            <img src="../../../../public/assets/Vector.png" alt="Decoration" class="absolute bottom-0 left-0">
            <img src="../../../../public/assets/Vector (1).png" alt="Decoration" class="absolute right-0">
            <img src="../../../../public/assets/Group 688.png" alt="Decoration" class="absolute bottom-0 right-0">
            <v-form @submit.prevent="loginFunc" ref="formRef">
            <div class="mt-[64px] mx-auto flex flex-column items-center justify-center  " style="width: 400px;">
                <h1 class="text-center text-[64px] font-bold text-[#009EE2]">Welcome</h1>
                <p class="text-center text-medium-emphasis mb-10">Login with Email</p>

                
                <v-text-field
                    v-model="formData.email"
                    label="Email ID"
                    prepend-inner-icon="mdi-email-outline"
                    variant="outlined"
                    color="#009EE2"
                    density="comfortable"
                    class="w-full" 
                    rounded="lg"
                    bg-color="#fff"
                    flat
                />

                <v-text-field
                    v-model="formData.password"
                    label="Password"
                    :type="visible ? 'text' : 'password'"
                    prepend-inner-icon="mdi-lock-outline"
                    :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                    variant="outlined"
                    density="comfortable"
                    @click:append-inner="visible = !visible"
                    color="secondary"
                    class="w-full mb-6"
                    rounded="lg"
                    bg-color="#fff"
                    flat
                />

                <v-btn
                type="submit"
                    color="secondary"
                    size="large"
                    class="font-bold text-white w-full"
                    elevation="0"
                    rounded="lg"
                >
                    LOGIN
                </v-btn>
            
            </div>
            </v-form>

        </div>
    </div>
</template>

<script setup>
import { ref,reactive } from 'vue'
import { useAuthRepository } from '../../store/AuthRepository';
const AuthRepository = useAuthRepository();
const formData = reactive({
    email: "",
    password: "",
});
const visible = ref(false);
const formRef = ref(null);

const loginFunc = async () => {
    const isValid = await formRef.value.validate();
    if (isValid) {
        try {
            await AuthRepository.login(formData);
            console.log("Login successful", formData);
        } catch (error) {
            console.error("Login failed", error);
        }
    }
};
</script>
