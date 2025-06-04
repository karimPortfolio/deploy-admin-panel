<template>
    <div class="flex justify-between items-center">
        <div>
            <div
                class="flex gap-2 items-center text-primary mb-1"
            >
                <q-icon :name="icon" size="md" />
                <div>
                    <div class="text-xl font-medium">{{ title }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">
                        {{ subtitle }}
                    </div>
                </div>
            </div>
        </div>
        <div>
            <q-btn
                :icon="actionIcon"
                :label="$q.screen.gt.sm ? actionLabel : 'Add'"
                @click="action"
                color="primary"
                class="dark:text-slate-800 text-white"
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
