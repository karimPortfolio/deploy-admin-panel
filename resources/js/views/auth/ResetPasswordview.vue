<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="w-full min-h-screen dark:bg-slate-900 sm:bg-gray-50 flex"
                >
                    <AuthPageHeader />

                    <!-- ============================/// FORM PART ///============================ -->
                    <div class="sm:q-pa-xl flex-1 flex flex-col">
                        <q-card
                            class="flex flex-col justify-center items-center my-auto sm:shadow-lg shadow-none sm:rounded-xl rounded-none sm:max-w-md max-w-full mx-auto w-full"
                        >
                            <q-card-section class="w-full text-center pt-8">
                                <div class="text-bold text-h4 text-primary">
                                    {{ $t("reset_password") }}
                                </div>
                                <div class="text-gray-600 text-sm">
                                    {{ $t("reset_password_subtitle") }}
                                </div>
                            </q-card-section>

                            <q-card-section class="w-full px-8 pt-4 pb-8">
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
                                        readonly
                                        disable
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
                                    >
                                        <template v-slot:prepend>
                                            <q-icon
                                                name="lock"
                                                color="primary"
                                            />
                                        </template>
                                    </q-input>
                                    <q-input
                                        type="password"
                                        v-model="
                                            credentials.password_confirmation
                                        "
                                        :label="$t('profile.password.confirm_password')"
                                        lazy-rules
                                        :error-message="
                                            messages.password_confirmation?.[0]
                                        "
                                        :error="
                                            'password_confirmation' in messages
                                        "
                                        outlined
                                    >
                                        <template v-slot:prepend>
                                            <q-icon
                                                name="lock"
                                                color="primary"
                                            />
                                        </template>
                                    </q-input>

                                    <q-btn
                                        :label="$t('reset_password')"
                                        @click="handleResetPassword"
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
import { onMounted, ref, watch } from "vue";
import { useQuasar } from "quasar";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
import SettingsDropdown from "./partials/SettingsDropdown.vue";
import AuthPageHeader from "./partials/AuthPageHeader.vue";
import { useI18n } from "vue-i18n";
import i18n from "../../plugins/i18n";
import { useMemoize } from "@vueuse/core";

const credentials = ref({});
const loading = ref(false);

const messages = ref({});

const route = useRoute();

const $q = useQuasar();

const authStore = useAuthStore();

const { t } = useI18n();

const handleResetPassword = async () => {
    loading.value = true;
    try {
        messages.value = {};
        const redirectTo = import.meta.env.VITE_LOGIN_URL;
        await authStore.resetPassword(credentials.value, redirectTo);
    } catch (err) {
        if (err.response?.status === 422) {
            messages.value = err.response.data.errors;
        }
    } finally {
        loading.value = false;
    }
};

const handleDarkMode = useMemoize(() => {
    const theme = localStorage.getItem("dark");
    if (theme === "auto") {
        $q.dark.set(theme);
        return;
    }

    const darkMode = JSON.parse(theme) ?? "auto";
    $q.dark.set(darkMode);
});

const handleInternationalization = useMemoize(() => {
    const language = localStorage.getItem("language") ?? "en";
    i18n.global.locale.value = language;
});

watch(
    () => authStore.errorMessages,
    (newErrorMessages) => {
        if (newErrorMessages) {
            messages.value = newErrorMessages;
        }
    }
);

onMounted(() => {
    credentials.value.email = route.query.email;
    credentials.value.token = route.query.token;
    handleDarkMode();
    handleInternationalization();
});
</script>
