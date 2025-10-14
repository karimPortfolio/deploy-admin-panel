<template>
    <q-layout>
        <q-page-container>
            <q-page>
                <div
                    class="min-h-screen flex items-center justify-center bg-gray-100"
                >
                    <div class="text-center px-4 relative">
                        <div class="mb-8 relative">
                            <h1
                                class="text-9xl font-bold text-primary opacity-80 transform hover:scale-105 transition-transform duration-300"
                            >
                                404
                            </h1>
                        </div>

                        <h2
                            class="text-4xl font-bold text-gray-800 tracking-wider mb-4 transform hover:scale-105 transition-transform duration-300"
                        >
                            {{ $t("not_found") }}
                        </h2>

                        <p
                            class="text-gray-600 mb-8 max-w-md mx-auto leading-relaxed"
                        >
                            {{ $t("not_found_subtitle") }}
                        </p>

                        <router-link
                            :to="{ name: homeUrl }"
                            class="inline-flex items-center px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-700 transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5"
                        >
                            <i class="fas fa-home mr-2"></i>
                            {{ $t("back_home") }}
                        </router-link>
                    </div>
                </div>
            </q-page>
        </q-page-container>
    </q-layout>
</template>
<script setup>
import { useAuthStore } from '@/stores/auth';
import { useQuasar } from 'quasar';
import i18n from '../../plugins/i18n';
import { onMounted } from 'vue';
import langEng from "quasar/lang/en-US";
import langDe from "quasar/lang/de";
import langFr from "quasar/lang/fr";
import langEs from "quasar/lang/es";

const { homeUrl } = useAuthStore();

const quasarLangs = {
    en: langEng,
    de: langDe,
    fr: langFr,
    es: langEs,
};

const $q = useQuasar();

const handleInternationalization = () => {
    const lang = localStorage.getItem("language") || "en";
    if (lang) {
        i18n.global.locale.value = lang;
        $q.lang.set(quasarLangs[lang]);
    }
};


onMounted(() => {
    handleInternationalization();
});
</script>