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
                                    Reset Password
                                </div>
                                <div class="text-gray-600 text-sm">
                                    Enter your new password below
                                </div>
                            </q-card-section>

                            <q-card-section class="w-full px-8 pt-4 pb-8">
                                <q-form
                                    @submit="(e) => e.preventDefault()"
                                    class="q-gutter-y-sm"
                                >
                                    <q-input
                                        v-model="credentials.email"
                                        label="Email"
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
                                        label="Password"
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
                                        label="Password Confirmation"
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
                                        label="Reset Password"
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

const credentials = ref({});
const loading = ref(false);

const messages = ref({});

const route = useRoute();

const $q = useQuasar();

const authStore = useAuthStore();

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

onMounted(() => {
    credentials.value.email = route.query.email;
    credentials.value.token = route.query.token;
    handleDarkMode();
});
</script>
