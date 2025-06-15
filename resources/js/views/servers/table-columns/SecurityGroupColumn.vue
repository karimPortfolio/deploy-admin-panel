<template>
    <div v-if="row.security_group">
        {{ truncate(row.security_group.group_id, 15) }}
        <div>
            <span class="font-medium me-2 text-gray-600 dark:text-gray-400"
                >VPC:</span
            >
            <span v-if="row.vpc_id">{{ truncate(row.vpc_id, 10) }}</span>
            <span v-else>N/A</span>
        </div>
    </div>

    <div v-else>
        <div>N/A</div>
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
