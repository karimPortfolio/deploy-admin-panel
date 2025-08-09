<template>
    <q-page class="q-pa-md">
        <page-header
            title="Settings"
            subtitle="Manage your preferences here"
            icon="sym_r_settings"
        />

        <div class="mt-4">
            <q-card>
                <q-card-section class="pb-0">
                    <div class="font-medium">Preferences</div>
                </q-card-section>
                <q-separator class="mt-3 mx-4" />
                <q-card-section class="mt-0">
                    <q-form>
                        <div class="grid grid-cols-3">
                            <div>
                                <div class="text-sm">Dark Mode</div>
                                <div
                                    class="dark:text-gray-400 text-gray-600 text-xs"
                                >
                                    Choice between dark and light themes
                                </div>
                            </div>
                            <div class="col-span-2 flex gap-10 items-center">
                                <div class="os-family-grid q-field--outlined">
                                    <div
                                        class="grid sm:grid-cols-2 lg:grid-cols-3 q-col-gutter-md q-pa-md"
                                    >
                                        <div
                                            v-for="option in themeOptions"
                                            :key="option.id"
                                            class=""
                                        >
                                            <div
                                                class="cursor-pointer text-center q-pa-sm sm:q-pa-md border border-[#ddd] transition-all ease-in-out hover:ring-1 hover:ring-primary-300 rounded-md dark:border-gray-600"
                                                :class="{
                                                    'ring-2 ring-primary-700  text-primary':
                                                        userPreferences.theme ===
                                                        option.value,
                                                }"
                                                @click="
                                                    handleThemeChange(option)
                                                "
                                            >
                                                <q-img
                                                    :src="option.img"
                                                    class="w-28 object-cover"
                                                />
                                                <div class="text-xs sm:mt-2">
                                                    {{ option.label }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="'theme' in validation"
                                    class="text-negative text-caption q-mt-sm ms-4"
                                >
                                    {{ validation.theme?.[0] }}
                                </div>
                            </div>
                        </div>

                        <q-separator class="mt-4" />

                        <div class="mt-5 grid grid-cols-3">
                            <div>
                                <div class="text-sm">Language</div>
                                <div
                                    class="dark:text-gray-400 text-gray-600 text-xs"
                                >
                                    Choice your prefered language
                                </div>
                            </div>

                            <!-- ============ -->

                            <div class="ms-3">
                                <q-select
                                    v-model="userPreferences.language"
                                    :options="languagesOptions"
                                    map-options
                                    emit-value
                                    outlined
                                    hide-bottom-space
                                    disable
                                    dense
                                >
                                    <template v-slot:option="scope">
                                        <q-item v-bind="scope.itemProps">
                                            <q-item-section>
                                                <q-item-label
                                                    class="flex items-center gap-3"
                                                >
                                                    <q-img
                                                        :src="scope.opt.img"
                                                        class="w-6 h-6 rounded-full"
                                                    />
                                                    {{ scope.opt.label }}
                                                </q-item-label>
                                            </q-item-section>
                                        </q-item>
                                    </template>
                                </q-select>
                            </div>
                        </div>

                        <q-separator class="mt-4" />

                        <div class="mt-5 grid grid-cols-3">
                            <div>
                                <div class="text-sm">Notifications</div>
                                <div
                                    class="dark:text-gray-400 text-gray-600 text-xs"
                                >
                                    Manage your notification preferences
                                </div>
                            </div>

                            <!-- ================ -->

                            <div>
                                <q-toggle
                                    v-model="userPreferences.notification.email"
                                    label="Email Notifications"
                                    color="primary"
                                    class="q-mt-sm flex gap-1"
                                />
                                <q-toggle
                                    v-model="
                                        userPreferences.notification.system
                                    "
                                    label="In-App Notifications"
                                    color="primary"
                                    class="q-mt-sm flex gap-1 mt-0"
                                />
                            </div>
                        </div>

                        <q-separator class="mt-5" />

                        <div class="mt-6 flex justify-end">
                            <q-btn
                                label="Save"
                                icon="sym_r_save"
                                color="primary"
                                unelevated
                                :loading="updating"
                                @click="handlePreferencesUpdate"
                            />
                        </div>
                    </q-form>
                </q-card-section>
            </q-card>
        </div>
    </q-page>
</template>
<script setup>
import PageHeader from "@/components/PageHeader.vue";
import { useAuthStore } from "@/stores/auth";
import { onMounted, ref, watch } from "vue";
import { useResourceUpdate } from "@/composables/useResourceUpdate";

const { user, fetchProfile } = useAuthStore();
const { update, updating, validation } = useResourceUpdate(() => `user/preferences/${user.preferences[0].id}`);

const themeOptions = [
    {
        id: 1,
        label: "System",
        value: "auto",
        img: "/src/img/system-placeholder.png",
    },
    {
        id: 2,
        label: "Light",
        value: "false",
        img: "/src/img/light-mode-placeholder.png",
    },
    {
        id: 3,
        label: "Dark",
        value: "true",
        img: "/src/img/dark-mode-placeholder.png",
    },
];

const languagesOptions = [
    {
        id: 1,
        label: "English",
        value: "en",
        img: "/src/img/flags/english-img.png",
    },
    {
        id: 2,
        label: "French",
        value: "fr",
        img: "/src/img/flags/french-img.png",
    },
    {
        id: 3,
        label: "Arabic",
        value: "ar",
        img: "/src/img/flags/arabic-img.png",
    },
];

const userPreferences = ref({
    theme: 'auto',
    language: 'en',
    notification: {
        email: true,
        system: true,
    },
});

const handleThemeChange = (option) => {
    userPreferences.value.theme = option.value;
};

const handlePreferencesUpdate = async () => {
    await update(null, userPreferences.value);

    handleSaved();  
};

const handleSaved = async () => {
    await fetchProfile(true);

    setTimeout(() => {
        window.location.reload();
    }, 300);
};

watch(() => user.preferences, (newValue) => {
    if (newValue && newValue.length > 0) {
        userPreferences.value = { ...newValue[0].preferences };
    }
}, { immediate: true });
</script>
