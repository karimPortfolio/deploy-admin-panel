<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="w-full min-h-screen dark:bg-slate-900 sm:bg-gray-50 flex"
                >
                    <div class="absolute top-4 right-4">
                        <settings-dropdown />
                    </div>

                    <!-- Form Section -->
                    <div class="sm:q-pa-xl flex-1 flex flex-col">
                        <q-card
                            class="flex flex-col justify-center items-center my-auto sm:shadow-lg shadow-none sm:rounded-xl rounded-none sm:max-w-md max-w-full mx-auto w-full"
                        >
                            <q-card-section class="w-full text-center pt-8">
                                <!-- Add your logo here if needed -->
                                <h1 class="text-h4 font-bold text-primary mb-2">
                                    Welcome
                                </h1>
                                <p class="text-gray-600 text-sm">
                                    Please sign up to get started
                                </p>
                            </q-card-section>

                            <q-card-section class="w-full px-8 pt-4 pb-6">
                                <q-form
                                    @submit="(e) => e.preventDefault()"
                                    class="q-gutter-y-xs"
                                >
                                    <q-input
                                        v-model="credentials.name"
                                        label="Name*"
                                        lazy-rules
                                        :error-message="messages.name?.[0]"
                                        :error="'name' in messages"
                                        outlined
                                        class="rounded-lg"
                                    />

                                    <q-input
                                        v-model="credentials.company_name"
                                        label="Company Name"
                                        lazy-rules
                                        :error-message="messages.company_name?.[0]"
                                        :error="'company_name' in messages"
                                        outlined
                                        class="rounded-lg"
                                    />

                                    <q-input
                                        v-model="credentials.email"
                                        label="Email Address*"
                                        lazy-rules
                                        :error-message="messages.email?.[0]"
                                        :error="'email' in messages"
                                        outlined
                                        class="rounded-lg"
                                    />

                                    <q-input
                                        type="password"
                                        v-model="credentials.password"
                                        label="Password*"
                                        lazy-rules
                                        :error-message="messages.password?.[0]"
                                        :error="'password' in messages"
                                        outlined
                                        class="rounded-lg"
                                    />

                                    <q-input
                                        type="password"
                                        v-model="credentials.password_confirmation"
                                        label="Confirm Password*"
                                        lazy-rules
                                        :error-message="
                                            messages.password_confirmation?.[0]
                                        "
                                        :error="'password_confirmation' in messages"
                                        outlined
                                        class="rounded-lg"
                                    />

                                    <div
                                        class="flex justify-between items-center mb-2"
                                    >
                                        <q-checkbox
                                            v-model="credentials.accepted_terms"
                                            size="sm"
                                            class="text-gray-600 flex flex-nowrap text-sm"
                                        >
                                            <div>
                                                I've read and agree with the
                                                Terms and
                                                <span class="text-blue-600"
                                                    >Privacy Policy</span
                                                >.
                                            </div>
                                        </q-checkbox>
                                    </div>

                                    <q-btn
                                        label="Sign up"
                                        @click="handleRegister"
                                        class="w-full bg-primary text-white q-py-md rounded-lg"
                                        unelevated
                                    >
                                        <template v-slot:loading>
                                            <q-spinner-oval />
                                        </template>
                                    </q-btn>
                                </q-form>
                                 <div class="mt-6 text-center text-gray-600 dark:text-gray-500 text-sm" >
                                    Already have an account? <a href="/auth/login" class="text-primary hover:underline" >Sign in</a>
                                </div>
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
import SettingsDropdown from "./partials/SettingsDropdown.vue";
import { useAuthStore } from "@/stores/auth";
import { useQuasar } from "quasar";

const authStore = useAuthStore();

const credentials = ref({
    accepted_terms: false,
});

const $q = useQuasar();

const messages = ref({});
const loading = ref(false);

const appName = ref(null);

const handleRegister = async () => {
    loading.value = true;
    try {
        await authStore.register(credentials.value);
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
    const theme = localStorage.getItem("dark");
    if (theme === "auto") {
        $q.dark.set(theme);
        return;
    }
    
    const darkMode = JSON.parse(theme) ?? "auto";
    $q.dark.set(darkMode);
}

watch(
    () => authStore.registerValidationMessage,
    (newRegisterValidationMessage) => {
        console.log(newRegisterValidationMessage);
        if (newRegisterValidationMessage) {
            messages.value = newRegisterValidationMessage;
        }
    }
);

onMounted(() => {
    appName.value = import.meta.env.VITE_APP_NAME;
    handleDarkMode();
});
</script>
