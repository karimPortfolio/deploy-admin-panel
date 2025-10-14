<template>
    <div class="flex justify-between items-center">
        <div>
            <div
                class="flex gap-2 items-center text-primary mb-1"
            >
                <q-icon :name="icon" size="md" />
                <div>
                    <div class="text-xl font-medium">{{ $t(title) }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $t(subtitle) }}
                    </div>
                </div>
            </div>
        </div>
        <div v-if="action" >
            <q-btn
                :icon="actionIcon"
                :label="$q.screen.gt.sm ? $t(actionLabel) : $t('add')"
                @click="action"
                color="primary"
                class="text-white"
                unelevated
            />
        </div>
    </div>
</template>
<script setup>
defineProps({
    title: {
        type: String,
    },
    icon: {
        type: String,
    },
    subtitle: {
        type: String,
    },
    actionIcon: {
        type: String,
    },
    actionLabel: {
        type: String,
    },
    action: {
        type: Function,
    },
});
</script>
