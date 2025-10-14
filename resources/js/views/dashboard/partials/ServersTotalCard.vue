<template>
    <q-card class="shadow-none flex items-center gap-0 ps-3">
        <q-card-section>
            <div class="bg-primary-100 rounded-full w-fit p-4">
                <q-icon name="sym_r_host" size="sm" color="primary" />
            </div>
        </q-card-section>
        <q-card-section>
            <div class="font-bold text-xl">{{ data?.total }}</div>
            <div class="text-gray-500 dark:text-gray-400">{{ $t("servers.title") }}</div>
        </q-card-section>
        <q-inner-loading :showing="loading">
            <q-spinner-tail color="primary" size="40px" />
        </q-inner-loading>
    </q-card>
</template>
<script setup>
import { useResourceIndex } from "@/composables/useResourceIndex";
import { onMounted } from "vue";

const { data, fetch, loading } = useResourceIndex("dashboard/servers-count");

onMounted(() => fetch());
</script>
