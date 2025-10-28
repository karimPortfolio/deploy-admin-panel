<template>
    <q-btn @click.stop :icon="icon" padding="xs" size="sm" unelevated>
        <q-menu
            :offset="offset"
            :transition-show="transitionShow"
            :transition-hide="transitionHide"
            class="q-pa-xs rounded-lg dark:bg-slate-700 shadow-lg border dark:border-none min-w-48"
        >
            <q-list style="min-width: 100px" dense class="q-pa-xs">
                <slot name="items"></slot>
            </q-list>
        </q-menu>
    </q-btn>
</template>
<script setup>

defineProps({
    transitionShow: {
        type: String,
        default: 'jump-down',
    },
    transitionHide: {
        type: String,
        default: 'jump-up',
    },
    offset: {
        type: Array,
        default: () => [15, 6],
    },
    icon: {
        type: String,
        default: 'sym_r_more_horiz',
    },
});

</script>