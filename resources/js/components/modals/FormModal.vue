<template>
    <q-dialog v-model="open">
        <q-card class="p-4 rounded-lg w-full md:max-w-2xl">
            <q-card-section class="row items-center q-pt-none q-mt-none">
                <div class="font-medium text-lg">
                    <template v-if="icon">
                        <q-icon :name="icon" class="q-mr-sm" size="sm" />
                    </template>
                    {{ truncate($t(title), 40) }}
                </div>
                <q-space />
                <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>
            <q-card-section>
                <slot name="form" :class="{ invisible: modalLoading }" ></slot>
            </q-card-section>
            <q-card-actions align="right" class="mt-4">
                <q-btn flat @click="handleClose" :label="$t('cancel')" />

                <q-btn
                    unelevated
                    :label="$t('save')"
                    :form="form"
                    type="submit"
                    icon="sym_r_save"
                    color="primary"
                    :loading="loading"
                />
            </q-card-actions>

            <q-inner-loading :showing="modalLoading">
                <q-spinner-tail color="primary" size="40px" />
            </q-inner-loading>
        </q-card>
    </q-dialog>
</template>
<script setup>
import { useTextTruncate } from "@/composables/useTextTruncate";

defineProps({
    title: {
        type: String,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    form: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
    },
    actionIcon: {
        type: String,
        default: "sym_r_add",
    },
    modalLoading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["close"]);
const open = defineModel("open");

const { truncate } = useTextTruncate();

const handleClose = () => {
    emit("close");
};
</script>
