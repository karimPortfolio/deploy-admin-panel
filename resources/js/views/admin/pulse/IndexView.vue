<template>
    <q-page :style-fn="(offset) => ({ height: `calc(100vh - ${offset}px)` })">
        <iframe
            src="/admin/pulse"
            frameborder="0"
            :style="{ colorScheme: $q.dark.isActive ? 'dark' : 'light' }"
            class="h-full w-full"
        ></iframe>
    </q-page>
</template>
<script setup>
import { useQuasar } from 'quasar';
import { onMounted } from 'vue';

const $q = useQuasar();

const theme = localStorage.getItem('dark') || ($q.dark.isActive ? 'dark' : 'light');


onMounted(() => {
    localStorage.setItem('theme', theme);
})
</script>
