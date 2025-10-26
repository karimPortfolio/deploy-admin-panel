<template>
    <div>
        <div>
            <span :title="row.storage_type.description">
                {{ row.storage_type.value }}
            </span>
        </div>
        <div>
            <span class="font-medium me-1 text-gray-600 dark:text-gray-400"
                >
                {{ $t("databases.size") }}:
            </span
            >
            <span>{{ row.allocated_storage }}</span>
        </div>
        <div>
            <span class="font-medium me-1 text-gray-600 dark:text-gray-400"
                >
                {{ $t("databases.encryption") }}:
            </span
            >
            <span v-if="row.storage_encrypted">{{ $t('active') }}</span>
            <span v-else>{{ $t('inactive') }}</span>
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
