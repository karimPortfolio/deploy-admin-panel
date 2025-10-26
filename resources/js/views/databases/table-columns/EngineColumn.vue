<template>
    <div>
        <div>
            <span :title="row.engine.description">
                {{ row.engine.label }}
            </span>
        </div>
        <div>
            <span class="font-medium me-1 text-gray-600 dark:text-gray-400"
                >
                {{ $t("databases.version") }}:
            </span
            >
            <span v-if="row.engine_version">
                {{ row.engine_version }}
            </span>
            <span v-else class="text-xs">N/A</span>
        </div>
    </div>
</template>
<script setup>

defineProps({
    row: {
        type: Object,
        required: true,
    },
});

</script>
