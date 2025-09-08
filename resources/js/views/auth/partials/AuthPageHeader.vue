<template>
    <div class="absolute w-full flex justify-between px-6 top-4">
        <div
            class="flex flex-nowrap items-center gap-3 text-center md:text-start"
        >
            <q-avatar size="lg">
                <img src="https://cdn.quasar.dev/logo-v2/svg/logo.svg" />
            </q-avatar>
            <span class="gt-lg inline text-2xl text-bold">
                {{ appName }}
            </span>
        </div>

        <SettingsDropdown />
    </div>
</template>
<script setup>
import { computed } from 'vue';
import SettingsDropdown from './SettingsDropdown.vue';

const appName = computed(() => import.meta.env.VITE_APP_NAME || "");

</script>
