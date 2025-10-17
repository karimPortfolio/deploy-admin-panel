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
                            <h1 class="text-3xl font-bold text-primary mb-2">{{ $t("forgot_password_title") }}</h1>
                            <p class="text-gray-600 dark:text-gray-400">{{ $t("forgot_password_subtitle") }}</p>
                        </q-card-section>

                        <!-- Form Section -->
                        <q-card-section class="w-full px-8 pt-4 pb-8">
                            <q-form @submit="handleLogin" class="q-gutter-y-md">
                                <q-input
                                    v-model="credentials.email"
                                    :label="$t('users.email')"
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
                                    :label="$t('reset_password')"
                                    @click="handleForgotPassword"
                                    class="w-full bg-primary text-white q-py-md rounded-lg"
                                    color="primary"
                                    :loading="loading"
                                    unelevated
                                />

                                <!-- Back to Login Link -->
                                <div class="text-center q-mt-md">
                                    <router-link :to="{name: 'auth.login'}" class="text-primary text-sm">
                                        {{ $t("back_to_login") }}
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
import AuthPageHeader from "./partials/AuthPageHeader.vue";
import i18n from "../../plugins/i18n";
import { useI18n } from "vue-i18n";

const credentials = ref({});
const loading = ref(false);

const messages = ref({});

const $q = useQuasar();

const authStore = useAuthStore();

const { t } = useI18n();

const handleForgotPassword = async () => {
    loading.value = true;
    try {
        await authStore.forgotPassword(credentials.value);
    } catch (err) {
        if (err.response?.status === 422) {
            messages.value = err.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
};

const handleDarkMode = () => {
    const theme = localStorage.getItem("dark");
    if (theme === "auto") {
        $q.dark.set(theme);
        return;
    }

    const darkMode = JSON.parse(theme) ?? "auto";
    $q.dark.set(darkMode);
}

const handleInternationalization = () => {
    const language = localStorage.getItem("language") ?? "en";
    i18n.global.locale.value = language;
};

watch(
    () => authStore.errorMessages,
    (newErrorMessages) => {
        if (newErrorMessages) {
            messages.value = newErrorMessages;
        }
    }
);

onMounted(() => {
    handleDarkMode();
    handleInternationalization();
});
</script>
