<template>
    <q-card class="shadow-none flex flex-nowrap items-center gap-0 ps-3">
        <q-card-section class="p-0">
            <div class="bg-green-100 rounded-full w-fit p-4">
                <q-icon name="sym_r_security" size="sm" color="green" />
            </div>
        </q-card-section>
        <q-card-section class="p-0 ps-3">
            <div class="font-bold text-xl">{{ data?.total }}</div>
            <div class="text-gray-500 dark:text-gray-400">{{ truncate($t("security_groups.title"),15) }}</div>
        </q-card-section>
        <q-inner-loading :showing="loading">
            <q-spinner-tail color="primary" size="40px" />
        </q-inner-loading>
    </q-card>
</template>
<script setup>
import { useResourceIndex } from "@/composables/useResourceIndex";
import { onMounted } from "vue";
import { useTextTruncate } from "@/composables/useTextTruncate";

const { data, fetch, loading } = useResourceIndex("admin/dashboard/security-groups-count");
const { truncate } = useTextTruncate();

onMounted(() => fetch());
</script>
