<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="w-full min-h-screen dark:bg-slate-900 sm:bg-gray-50 flex"
                >
                    <div class="absolute top-4 right-4">
                        <SettingsDropdown />
                    </div>

                    <!-- Form Section -->
                    <div class="sm:q-pa-xl flex-1 flex flex-col">
                        <q-card
                            class="flex flex-col justify-center items-center my-auto sm:shadow-lg shadow-none sm:rounded-xl rounded-none sm:max-w-md max-w-full mx-auto w-full"
                        >
                            <q-card-section class="w-full text-center pt-8">
                                <!-- Add your logo here if needed -->
                                <h1 class="text-h4 font-bold text-primary mb-2">
                                    Welcome Back
                                </h1>
                                <p class="text-gray-600 text-sm">
                                    Please sign in to continue
                                </p>
                            </q-card-section>

                            <q-card-section class="w-full px-8 pt-4 pb-8">
                                <q-form
                                    @submit="(e) => e.preventDefault()"
                                    class="q-gutter-y-sm"
                                >
                                    <q-input
                                        v-model="credentials.email"
                                        label="Email Address"
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
                                        label="Password"
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
                                            label="Remember me"
                                            class="text-gray-600 flex flex-nowrap"
                                        />
                                        <a
                                            href="/auth/forget-password"
                                            class="text-sm text-primary hover:text-primary-dark"
                                        >
                                            Forgot password?
                                        </a>
                                    </div>

                                    <q-btn
                                        label="Sign in"
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
import { ref, watch } from "vue";
import { api } from "@/boot/api";
import { useQuasar } from "quasar";
import { useAuthStore } from "@/stores/auth";
import { useRoute } from "vue-router";
import SettingsDropdown from "./partials/SettingsDropdown.vue";

const credentials = ref({
    remember: false,
});
const loading = ref(false);

const messages = ref({});

const route = useRoute();

const $q = useQuasar();

const authStore = useAuthStore();

const handleAuth = async () => {
    try {
        const response = await api.get(import.meta.env.VITE_SANCTUM_URL);
    } catch (err) {
        $q.notify({
            message: "Error",
            caption:
                err.response.data.message ??
                "Something went wrong. Please try again.",
            type: "negative",
        });
    }
};

const handleLogin = async () => {
    loading.value = true;
    try {
        await handleAuth();
        const redirectTo = route.query.redirect_to;
        await authStore.login(credentials.value, redirectTo);
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

watch(
    () => authStore.errorMessages,
    (newErrorMessages) => {
        if (newErrorMessages) {
            messages.value = newErrorMessages;
        }
    }
);
</script>
