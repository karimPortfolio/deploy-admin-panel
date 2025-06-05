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
                <slot name="form"></slot>
            </q-card-section>
            <q-card-actions align="right" class="mt-4">
                <q-btn flat @click="handleClose" label="Cancel" />

                <q-btn
                    unelevated
                    label="Save"
                    form="security-group-form"
                    type="submit"
                    icon="sym_r_save"
                    color="primary"
                    :loading="loading"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
<script setup>

defineProps({
    title: {
        type: String,
        required: true
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon:{
        type: String,
    },
    actionIcon: {
        type: String,
        default: "sym_r_add"
    }
})

const emit = defineEmits(["close"]);
const open = defineModel("open");

const handleClose = () => {
    emit("close");
};

</script>