<template>
    <div class="flex justify-between items-center">
        <div>
            <div
                class="flex gap-2 items-center text-primary-800 dark:text-primary-300 mb-1"
            >
                <q-icon :name="icon" size="md" />
                <div class="text-2xl font-medium">{{ title }}</div>
            </div>
            <div class="text-xs text-gray-600 dark:text-gray-400">
                {{ caption }}
            </div>
        </div>
        <div>
            <q-btn
                :icon="actionIcon"
                :label="actionLabel"
                @click="action"
                class="dark:bg-primary-300 bg-primary-800 dark:text-slate-800 text-white"
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
    caption: {
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
