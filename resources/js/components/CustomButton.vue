<template>
    <q-btn
        :label="label"
        :icon="icon"
        :color="color"
        :size="size"
        :round="round"
        :flat="flat"
        :outline="outline"
        :loading="loading"
        @click="$emit('click')"
        class="q-mr-sm transition-all duration-200 hover:shadow-md focus:ring-2 focus:ring-opacity-50"
        :class="buttonClasses"
    >
        <template v-if="$slots.default">
            <slot />
        </template>
        <q-tooltip v-if="tooltip" :anchor="tooltipAnchor" :self="tooltipSelf">
            {{ tooltip }}
        </q-tooltip>
        <q-spinner v-if="loading" color="primary" size="20px" />
    </q-btn>
</template>
<script setup>
import { computed } from 'vue';

const props = defineProps({
     label: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: ''
    },
    size: {
        type: String,
        default: 'md'
    },
    round: {
        type: Boolean,
        default: false
    },
    flat: {
        type: Boolean,
        default: false
    },
    outline: {
        type: Boolean,
        default: false
    },
    loading: {
        type: Boolean,
        default: false
    },
    tooltip: {
        type: String,
        default: ''
    },
    tooltipAnchor: {
        type: String,
        default: 'top middle'
    },
    tooltipSelf: {
        type: String,
        default: 'bottom middle'
    }
});
    

const buttonClasses = computed(() => ({
    'rounded-lg': !props.round,
    'rounded-full': props.round,
    'hover:bg-opacity-90': !props.flat && !props.outline,
    'hover:bg-opacity-10': props.flat || props.outline,
    'focus:ring-blue-300': props.color === 'primary',
    'focus:ring-red-300': props.color === 'negative',
    'focus:ring-green-300': props.color === 'positive',
    'focus:ring-orange-300': props.color === 'warning',
    'px-4 py-2': props.size === 'md' && !props.round,
    'font-medium': true,
    'border-gray-200': props.outline,
}));
</script>
