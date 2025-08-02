<template>
    <q-page class="q-pa-md">
        <page-header
            title="Profile"
            subtitle="Manage your personal informations and password here"
            icon="sym_r_person"
        />

        <div class="mt-4">
            <personal-informations-card
                v-model="userDetails"
                :loading="updating"
                :validation="validation"
                :uploadedPhoto="uploadedPhotoUrl"
                @save-details="handleProfileInfoSave"
                @open-file-selector="open"
                @reset-image="handleUserPhotoUploadReset"
            />
        </div>

        <div class="mt-4">
            <update-password-card
                v-model="userPassword"
                :loading="updatingPassword"
                :validation="passwordValidation"
                @save-password="handlePasswordUpdate"
            />
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { useAuthStore } from "@/stores/auth";
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { onMounted, ref } from "vue";
import PersonalInformationsCard from "./partials/PersonalInformationsCard.vue";
import UpdatePasswordCard from "./partials/UpdatePasswordCard.vue";
import { useFileDialog } from "@vueuse/core";

const { update, updating, validation } = useResourceUpdate(
    "user/profile-information",
    {
        config: {
            method: "POST",
            headers: {
                "Content-Type": "multipart/form-data",
            },
        },
    }
);

const {
    update: updatePassword,
    updating: updatingPassword,
    validation: passwordValidation,
} = useResourceUpdate("user/password");

const { open, reset, onChange } = useFileDialog({
    accept: "image/*",
    multiple: false,
});

const { user, fetchProfile } = useAuthStore();

const userDetails = ref({
    name: "",
    email: "",
});

const userPassword = ref({});
const uploadedPhotoUrl = ref(null);

onChange((files) => {
    if (!files || files.length === 0) return;
console.log('hello working');
    userDetails.value.photo = files[0];
    uploadedPhotoUrl.value = URL.createObjectURL(userDetails.value.photo);
});

const handleUserPhotoUploadReset = () => {
    reset();

    uploadedPhotoUrl.value = null;
    userDetails.value.photo = null;
};

const handleProfileInfoSave = async () => {
    await update(null, {
        ...userDetails.value,
        _method: "PUT"
    });

    handleSaved();
};

const handlePasswordUpdate = async () => {
    await updatePassword(null, userPassword.value);

    userPassword.value = {};
};

const handleSaved = () => {
    fetchProfile();
};

onMounted(() => {
    userDetails.value = { 
        name: user.name,
        email: user.email,
        company_name: user.company_name,
     };
});
</script>
