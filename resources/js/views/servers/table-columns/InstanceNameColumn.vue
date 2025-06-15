<template>
    <div>
        {{ truncate(row.name, 30) }}
        <div>
            <span class="font-medium me-2 text-gray-600 dark:text-gray-400"
                >Instance ID:</span
            >
            <span v-if="row.instance_id" >{{ truncate(row.instance_id, 15) }}</span>
            <span v-else >N/A</span>
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
