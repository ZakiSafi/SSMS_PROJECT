<template>
  <div dir="rtl">
    <v-dialog
      transition="dialog-top-transition"
      width="30rem"
      v-model="UserRepository.createDialog"
    >
      <template v-slot:default="{ isActive }">
        <v-card class="px-3">
          <v-card-title class="px-2 pt-4 d-flex justify-space-between">
            <h2 class="font-weight-bold pl-4">
              {{ UserRepository.isEditMode ? "Update" : "Create" }} User
            </h2>
            <v-btn variant="text" @click="isActive.value = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-card-title>

          <v-divider class="border-opacity-100 mx-6"></v-divider>

          <v-card-text>
            <v-form ref="formRef" class="pt-4">

              <!-- Avatar Preview & Upload Trigger -->
               
              <div class="photo-upload-container" >
                                    <v-file-input
                                        type="file"
                                        ref="inputRef"
                                        style="display: none"
                                        @change="onChangeImage"
                                    ></v-file-input>

                                    <img
                                        :src="imageSrc"
                                        class="photo-preview"
                                        v-show="imageSrc !== null"
                                    />

                                    <div class="photo-overlay">
                                        <button
                                            v-if="!imageSrc"
                                            type="button"
                                            @click="OpenWindow(inputRef)"
                                            class="overlay-button"
                                        >
                                            <v-icon
                                                size="x-large"
                                                color="blue-grey-lighten-2"
                                                >mdi-camera</v-icon
                                            >
                                        </button>
                                        <button
                                            v-if="imageSrc"
                                            type="button"
                                            @click="CloseWindow()"
                                            class="close-button"
                                        >
                                            <v-icon size="lg" color="red"
                                                >mdi-close</v-icon
                                            >
                                        </button>
                                        <button
                                            v-if="imageSrc"
                                            type="button"
                                            @click="OpenWindow(inputRef)"
                                            class="edit-button"
                                        >
                                            <v-icon size="small"
                                                >mdi-pencil</v-icon
                                            >
                                        </button>
                                    </div>
                                </div>
              <!-- Input Fields -->
              <v-text-field
                v-model="formData.name"
                label="Name"
                variant="outlined"
                class="pb-4"
                :rules="[rules.required]"
              ></v-text-field>

              <v-text-field
                v-model="formData.email"
                label="Email"
                variant="outlined"
                class="pb-4"
                :rules="[rules.required, rules.email]"
              ></v-text-field>

              <v-text-field
                v-model="formData.password"
                label="Password"
                type="password"
                variant="outlined"
                class="pb-4"
                :rules="[rules.required]"
              ></v-text-field>
            </v-form>
          </v-card-text>

          <div class="d-flex flex-row-reverse mb-6 mx-6">
            <v-btn color="primary" class="px-4" @click="save">
              {{ UserRepository.isEditMode ? "Update" : "Submit" }}
            </v-btn>
          </div>
        </v-card>
      </template>
    </v-dialog>
  </div>
</template>


<script setup>
import { ref, reactive } from "vue";
import { useUserRepository } from "@/store/UserRepository";

const UserRepository = useUserRepository();
const formRef = ref(null);


const formData = reactive({
  id: UserRepository.user.id,
  name: UserRepository.user.name,
  email: UserRepository.user.email,
  password: "",
  image: null,
  roll_permission_id: UserRepository.user.roll_permission_id || null,
});

// profile_picture configuration      |
let imageSrc = ref(UserRepository.user.profilePicture);
const inputRef = ref(null);
const onChangeImage = (e) => {
    imageSrc.value = URL.createObjectURL(e.target.files[0]);
    formData.profile_picture = e.target.files[0];
};
const OpenWindow = (action) => {
    if (action) {
        ref(action).value.click();
    }
};
const CloseWindow = () => {
    imageSrc.value = null;
    formData.profile_picture = null;
};
const rules = {
  required: (value) => !!value || "This field is required.",
  email: (value) => /.+@.+\..+/.test(value) || "Email must be valid",
};

const save = async () => {
  const isValid = await formRef.value.validate();
  if (isValid) {
    const payload = new FormData();
    payload.append("name", formData.name);
    payload.append("email", formData.email);
    payload.append("password", formData.password);
    if (formData.image) payload.append("image", formData.image);
    payload.append("roll_permission_id", formData.roll_permission_id);

    if (UserRepository.isEditMode) {
      await UserRepository.UpdateUser(formData.id, payload);
    } else {
      await UserRepository.CreateUser(payload);
    }
  }
};
</script>;

<style scoped>

.borderStyle {
    border: 1px solid #999;
}
.photo-upload-container {
    position: relative;
    display: inline-block;
    height: 8rem;
    width: 8rem;
    margin-left: 2rem;
    border-radius: 0.5rem;
    overflow: hidden;
    border: 1px solid gray;
}

.photo-preview {
    height: 100%;
    width: 100%;
    object-fit: cover;
}

.photo-overlay {
    position: absolute;
    top: 0;
    height: 100%;
    width: 100%;
    border-radius: 0.5rem;
    background-color: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
}

.overlay-button {
    border: none;
    background-color: transparent;
    cursor: pointer;
}

.close-button {
    position: absolute;
    bottom: -0.25rem;
    right: -0.25rem;
    border: none;
    background-color: transparent;
    /* color: #060505; */
    cursor: pointer;
}

.edit-button {
    position: absolute;
    top: -0.25rem;
    right: -0.25rem;
    border: none;
    background-color: transparent;
    color: #777777;
    cursor: pointer;
}
</style>


