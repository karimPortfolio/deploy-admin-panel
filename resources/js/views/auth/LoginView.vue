<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="w-full min-h-screen dark:bg-slate-900 sm:bg-gray-50 flex"
                >
                    <AuthPageHeader />

                    <!-- Form Section -->
                    <div class="sm:q-pa-xl flex-1 flex flex-col">
                        <q-card
                            class="flex flex-col justify-center items-center my-auto sm:shadow-lg shadow-none sm:rounded-xl rounded-none sm:max-w-md max-w-full mx-auto w-full"
                        >
                            <q-card-section class="w-full text-center pt-8">
                                <h1 class="text-h4 font-bold text-primary mb-2">
                                    {{ $t("login_page_title") }}
                                </h1>
                                <p class="text-gray-600 text-sm">
                                    {{ $t("login_page_subtitle") }}
                                </p>
                            </q-card-section>

                            <q-card-section class="w-full px-8 pt-4 pb-6">
                                <q-form
                                    @submit="(e) => e.preventDefault()"
                                    class="q-gutter-y-sm"
                                >
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
                                            <q-icon
                                                name="email"
                                                color="primary"
                                            />
                                        </template>
                                    </q-input>

                                    <q-input
                                        type="password"
                                        v-model="credentials.password"
                                        :label="$t('users.password')"
                                        lazy-rules
                                        :error-message="messages.password?.[0]"
                                        :error="'password' in messages"
                                        outlined
                                        class="rounded-lg"
                                    >
                                        <template v-slot:prepend>
                                            <q-icon
                                                name="lock"
                                                color="primary"
                                            />
                                        </template>
                                    </q-input>

                                    <div
                                        class="flex justify-between items-center mb-2"
                                    >
                                        <q-checkbox
                                            v-model="credentials.remember"
                                            size="sm"
                                            :label="$t('remember_me')"
                                            class="text-gray-600 flex flex-nowrap"
                                        />
                                        <router-link
                                            :to="{ name: 'auth.forgot.password' }"
                                            class="text-sm text-primary hover:text-primary-dark"
                                        >
                                            {{ $t("forget_password") }}?
                                        </router-link>
                                    </div>

                                    <q-btn
                                        :label="$t('login')"
                                        @click="handleLogin"
                                        :loading="loading"
                                        class="w-full bg-primary text-white q-py-md rounded-lg"
                                        unelevated
                                    >
                                        <template v-slot:loading>
                                            <q-spinner-oval />
                                        </template>
                                    </q-btn>
                                </q-form>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>
<script setup>
import { computed, onMounted, ref, watch } from "vue";
import { api } from "@/boot/api";
import { useQuasar } from "quasar";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
import SettingsDropdown from "./partials/SettingsDropdown.vue";
import AuthPageHeader from "./partials/AuthPageHeader.vue";
import i18n from "../../plugins/i18n";
import { useI18n } from "vue-i18n";

const credentials = ref({
    remember: false,
});
const loading = ref(false);

const messages = ref({});

const route = useRoute();

const $q = useQuasar();

const authStore = useAuthStore();

const { t } = useI18n();

const handleDarkMode = () => {
    const theme = localStorage.getItem("dark");
    if (theme === "auto") {
        $q.dark.set(theme);
        return;
    }

    const darkMode = JSON.parse(theme) ?? "auto";
    $q.dark.set(darkMode);
};

const handleInternationalization = () => {
    const language = localStorage.getItem("language") ?? "en";
    i18n.global.locale.value = language;
};

const handleAuth = async () => {
    try {
        await api.get(import.meta.env.VITE_SANCTUM_URL);
    } catch (err) {
        $q.notify({
            message: t("error"),
            caption:
                err.response.data.message ??
                t("something_went_wrong_error_msg"),
            type: "negative",
        });
    }
};

const handleLogin = async () => {
    loading.value = true;
    try {
        await handleAuth();
        const redirectTo = decodeURIComponent(route.query.redirect_to);
        await authStore.login(credentials.value, redirectTo);
    } catch (err) {
        if (err.response?.status === 422) {
            messages.value = err.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
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
