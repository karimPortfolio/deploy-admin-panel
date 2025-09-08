<template>
    <q-layout>
        <q-page-container>
            <q-page class="w-full min-h-screen dark:bg-slate-900 sm:bg-gray-50 flex">

                <AuthPageHeader />

                <div class="sm:q-pa-xl flex-1 flex flex-col">
                    <!-- Card Container -->
                    <q-card class="flex flex-col justify-center items-center my-auto sm:shadow-lg shadow-none sm:rounded-xl rounded-none sm:max-w-md max-w-full mx-auto w-full">
                        <!-- Logo/Brand Section -->
                        <q-card-section class="text-center pt-8">
                            <h1 class="text-3xl font-bold text-primary mb-2">Forgot Password</h1>
                            <p class="text-gray-600 dark:text-gray-400">Enter your email to reset your password</p>
                        </q-card-section>

                        <!-- Form Section -->
                        <q-card-section class="w-full px-8 pt-4 pb-8">
                            <q-form @submit="handleLogin" class="q-gutter-y-md">
                                <q-input
                                    v-model="credentials.email"
                                    label="Email"
                                    lazy-rules
                                    :error-message="messages.email?.[0]"
                                    :error="'email' in messages"
                                    outlined
                                    class="rounded-lg"
                                >
                                    <template v-slot:prepend>
                                        <q-icon name="mail" color="primary"/>
                                    </template>
                                </q-input>

                                <q-btn
                                    label="Reset Password"
                                    @click="handleForgetPassword"
                                    class="w-full bg-primary text-white q-py-md rounded-lg"
                                    color="primary"
                                    :loading="loading"
                                    unelevated
                                />

                                <!-- Back to Login Link -->
                                <div class="text-center q-mt-md">
                                    <router-link :to="{name: 'auth.login'}" class="text-primary text-sm">
                                        Back to Login
                                    </router-link>
                                </div>
                            </q-form>
                        </q-card-section>
                    </q-card>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>
<script setup>
import { onMounted, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { useAuthStore } from "@/stores/auth";
import SettingsDropdown from "./partials/SettingsDropdown.vue";
import ImagesCarousel from "./partials/ImagesCarousel.vue";
import AuthPageHeader from "./partials/AuthPageHeader.vue";

const credentials = ref({});
const loading = ref(false);

const messages = ref({});

const $q = useQuasar();

const authStore = useAuthStore();

const handleForgetPassword = async () => {
    loading.value = true;
    try {
        await authStore.forgotPassword(credentials.value);
    } catch (err) {
        if (err.response?.status === 422) {
            messages.value = err.response.data.errors;
        }
        $q.notify({
            message: "Error",
            caption:
                err.response?.data?.message ??
                "Something went wrong. Please try again.",
            type: "negative",
        });
    } finally {
        loading.value = false;
    }
};
const handleDarkMode = () => {
    const userPereference = localStorage.getItem("dark");
    $q.dark.set(JSON.parse(userPereference) || 'auto');
}

watch(
    () => authStore.errorMessages,
    (newErrorMessages) => {
        if (newErrorMessages) {
            messages.value = newErrorMessages;
        }
    }
);

onMounted(() => handleDarkMode());
</script>
