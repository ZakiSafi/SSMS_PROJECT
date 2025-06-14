<template>
    <div dir="rtl">
        <v-dialog
            transition="dialog-top-transition"
            width="50rem"
            v-model="FacultyRepository.createDialog"
            
        >
            <template v-slot:default="{ isActive }">
                <v-card class="px-3">
                    <v-card-title class="px-2 pt-4 d-flex justify-space-between">
                        <h2 class="font-weight-bold pl-4">
                            {{ FacultyRepository.isEditMode ? "Update" : "Create" }}
                        </h2>
                        <v-btn variant="text" @click="isActive.value = false">
                            <v-icon>mdi-close</v-icon>
                        </v-btn>
                    </v-card-title>
                    <v-divider class="border-opacity-100 mx-6"></v-divider>

                    <v-card-text>
                        <v-form ref="formRef" class="pt-4">
                            <v-row>
                                <v-col cols="12">
                                    <DatePicker
                                            v-model="formData.academic_year"
                                            
                                            format="jYYYY"
                                            type="year"
                                            placeholder="Select year"
                                            rounded
                                        />
                                </v-col>
                               <v-col cols="6">
                                 <v-text-field
                                v-model="formData.total_teachers"
                               variant="outlined"
                                label="Total Teachers"
                                class="pb-4"
                                density="compact"
                                :rules="[rules.required,rules.number]"
                            ></v-text-field>
                               </v-col>
                               <v-col cols="6">
                                 <v-select
                                v-model="formData.university_id"
                                :items="FacultyRepository.universities"
                                item-value="id"
                                item-title="name"
                               variant="outlined"
                                label="University"
                                density="compact"
                                class="pb-4"
                                :rules="[rules.required]"
                            ></v-select>
                               </v-col>
                                    
                            </v-row>
                            
                           
                            
                        </v-form>
                    </v-card-text>

                    <div class="d-flex flex-row-reverse mb-6 mx-6">
                        <v-btn color="primary" class="px-4" @click="save">
                            {{ FacultyRepository.isEditMode ? "Update" : "Submit" }}
                        </v-btn>
                    </div>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>


<script setup>
import { ref, reactive } from "vue";
import { onMounted } from "vue";
import { useFacultyRepository } from "@/store/FacultyRepository";
import DatePicker from "vue3-persian-datetime-picker";


const FacultyRepository = useFacultyRepository();

onMounted(()=>{

    FacultyRepository.FetchUniversities();
})

const formRef = ref(null);

const formData = reactive({
    id: FacultyRepository.teacher.id,
    academic_year:FacultyRepository.teacher.academic_year?.toString() || null,
    total_teachers: FacultyRepository.teacher.total_teachers,
    university_id: FacultyRepository.teacher.university?.id || null,
});


const rules = {
  required: (value) => !!value || "This field is required.",
  number: (value) =>
    /^[0-9]+$/.test(value) || "Please enter a valid number.",
};

const save = async () => {
    const isValid = await formRef.value.validate();
    if (isValid) {
        if (FacultyRepository.isEditMode) {
            await FacultyRepository.updateTeacher(formData,formData.id,);
        } else {
            await FacultyRepository.createTeacher(formData);
        }
    }
};






</script>