<template>
    <div v-if="row.instance_type">
        {{ row.instance_type.value }}
        <div>
            <span class="font-medium me-2 text-gray-600 dark:text-gray-400"
                >Image:</span
            >
            <span v-if="row.image_id">{{ truncate(row.image_id, 15) }}</span>
            <span v-else>N/A</span>
        </div>
        <div>
            <span class="font-medium me-2 text-gray-600 dark:text-gray-400"
                >OS:</span
            >
            <span v-if="row.os_family">
                <q-icon :name="row.os_family?.icon" />
                {{ row.os_family?.label }}
            </span>
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
