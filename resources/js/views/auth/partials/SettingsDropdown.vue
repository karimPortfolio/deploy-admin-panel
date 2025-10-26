<template>
    <div class="flex justify-end">
        <q-btn
            dense
            flat
            icon="sym_r_settings"
            class="hover:bg-gray-100 dark:hover:bg-gray-600"
        >
            <q-menu
                :offset="[0, 6]"
                class="q-pa-xs rounded-lg dark:bg-slate-700 shadow-md border dark:border-0 w-52"
            >
                <div
                    class="p-2 flex flex-nowrap items-center justify-between text-sm"
                >
                    {{ $t("dark_mode") }}:
                    <div>
                        <q-btn
                            dense
                            flat
                            size="sm"
                            padding="sm"
                            :icon="$q.dark.isActive ? 'light_mode' : 'dark_mode'"
                            :label="$q.dark.isActive ? $t('light') : $t('dark')"
                            @click="handleDarkToggling"
                        />
                    </div>
                </div>
                <div
                    class="p-2 flex flex-nowrap items-center justify-between text-sm"
                >
                    {{ $t("settings.language.label") }}:
                    <q-select
                        v-model="$i18n.locale"
                        :options="languagesOptions"
                        dense
                        outlined
                        borderless
                        options-dense
                        class="ml-2 w-24 text-sm rounded-lg"
                        @update:model-value="handleLanguageChange"
                    >
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section>
                                    <q-item-label
                                        class="flex items-center gap-3"
                                    >
                                        <q-avatar size="24px">
                                            <q-img
                                                :src="scope.opt.img"
                                                class="w-6 h-6 object-cover rounded-full"
                                            />
                                        </q-avatar>
                                        {{ scope.opt.label }}
                                    </q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>
                </div>
            </q-menu>
        </q-btn>
    </div>
</template>
<script setup>
import { useQuasar } from "quasar";
import i18n from "../../../plugins/i18n";
import { useI18n } from "vue-i18n";

const { t } = useI18n();
const $q = useQuasar();

const languagesOptions = [
    {
        id: 1,
        label: t("settings.language.english"),
        value: "en",
        img: "/src/img/flags/english-img.png",
    },
    {
        id: 2,
        label: t("settings.language.french"),
        value: "fr",
        img: "/src/img/flags/french-img.png",
    },
    {
        id: 3,
        label: t("settings.language.german"),
        value: "de",
        img: "/src/img/flags/german-img.png",
    },
    {
        id: 4,
        label: t("settings.language.spanish"),
        value: "es",
        img: "/src/img/flags/spain-img.png",
    },
];

const handleLanguageChange = (value) => {
    localStorage.setItem("language", value.value);
    i18n.global.locale.value = value.value;
};

const handleDarkToggling = () => {
    $q.dark.toggle();
    localStorage.setItem("dark", $q.dark.isActive);
};
</script>
