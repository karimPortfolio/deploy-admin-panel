<template>
    <q-select
        v-if="filter.type === 'enum'"
        dense
        outlined
        emit-value
        map-options
        :label="filter.label"
        :options="filter.options"
        :model-value="modelValue"
        clearable
        @update:model-value="emitUpdate"
    />

    <q-toggle
        v-else-if="filter.type === 'boolean'"
        :label="filter.label"
        :model-value="modelValue"
        @update:model-value="emitUpdate"
    />

    <q-date
        v-else-if="filter.type === 'date'"
        dense
        outlined
        mask="YYYY-MM-DD"
        :label="filter.label"
        :model-value="modelValue"
        @update:model-value="emitUpdate"
        clearable
    />

    <custom-select
        v-else-if="filter.type === 'relation'"
        :model-value="modelValue"
        :label="filter.label"
        :resource="filter.resource"
        :option-label="filter.optionLabel || 'name'"
        :option-value="filter.optionValue || 'id'"
        dense
        map-options
        emit-value
        outlined
        @update:model-value="emitUpdate"
    />

    <q-input
        v-else
        dense
        clearable
        outlined
        :label="filter.label"
        :model-value="modelValue"
        @update:model-value="emitUpdate"
    />
</template>
<script setup>
import { ref, watchEffect } from "vue";
import { QInput, QSelect, QToggle, QDate } from "quasar";
import axios from "axios";
import CustomSelect from "@/components/CustomSelect.vue";

const props = defineProps({
    filter: Object,
    modelValue: [String, Number, Boolean, Object, Array],
});

const emit = defineEmits(["update:modelValue"]);

const emitUpdate = (val) => emit("update:modelValue", val);
</script>
