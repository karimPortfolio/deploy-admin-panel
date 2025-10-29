<template>
    <div>
        <div>
            <span v-if="row.db_name" :title="row.db_name">
                {{ truncate(row.db_name, 10) }}
            </span>
            <span v-else class="text-xs">N/A</span>
        </div>
        <div>
            <span class="font-medium me-1 text-gray-600 dark:text-gray-400"
                >
                {{ $t("databases.DII") }}:
            </span
            >
            <span :title="row.db_instance_identifier">
                {{ truncate(row.db_instance_identifier, 10) }}
            </span>
        </div>
    </div>
</template>
<script setup>
import { useTextTruncate } from "@/composables/useTextTruncate";

defineProps({
    row: {
        type: Object,
        required: true,
    },
});

const { truncate } = useTextTruncate();
</script>
