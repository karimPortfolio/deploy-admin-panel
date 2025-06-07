<template>
    <q-dialog v-model="open">
        <q-card class="p-4 rounded-lg w-full md:max-w-2xl">
            <q-card-section class="row items-center q-pt-none q-mt-none">
                <div class="font-medium text-lg">
                    <template v-if="icon">
                        <q-icon :name="icon" class="q-mr-sm" size="sm" />
                    </template>
                    {{ title }}
                </div>
                <q-space />
                <q-btn icon="close" flat round dense v-close-popup />
            </q-card-section>
            <q-card-section>
                <slot name="content"></slot>
            </q-card-section>
            <!-- <q-card-actions align="right">
                <q-btn unelevated color="primary" @click="handleClose" label="Close"  />
            </q-card-actions> -->

            <q-inner-loading :showing="loading">
                <q-spinner-tail color="primary" size="40px" />
            </q-inner-loading>
        </q-card>
    </q-dialog>
</template>
<script setup>

defineProps({
    title: {
        type: String,
        required: true,
    },
    icon: {
        type: String,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const open = defineModel("open");
const emit = defineEmits(["close"]);

const handleClose = () => {
    emit("close");
};
</script>
