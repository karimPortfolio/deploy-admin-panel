<template>
    <q-dialog v-model="open" persistent>
        <q-card class="delete-confirmation-modal">
            <q-card-section class="flex justify-center pb-2" >
                <q-icon :name="icon" :color="color" size="xl"  />
            </q-card-section>
            <q-card-section class="pb-3" >
                <div class="text-h6 text-center">
                    {{ $t(title) || $t('confirm_action') }}
                </div>
            </q-card-section>
            <q-card-section class="px-10 pb-5 pt-0">
                <p v-if="message" class="text-center">{{ $t(message) }}</p>
                <p v-else class="text-center">
                    {{ $t('confirm_action_paragraph') }}
                </p>
            </q-card-section>

            <q-card-actions align="between" class="flex-nowrap px-3 py-4" >
                <q-btn 
                    flat 
                    :label="$t('cancel')" 
                    v-close-popup 
                    class="w-1/2"
                    @click="onCancel"
                />
                <q-btn 
                    :loading="loading"
                    :disable="loading"
                    unelevated
                    :label="actionLabel || $t('delete')" 
                    :color="color" 
                    class="w-1/2"
                    @click="onConfirm"
                />
            </q-card-actions>
        </q-card>
    </q-dialog>
</template>
<script setup>

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    message: {
        type: String,
        default: ''
    },
    loading: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        default: 'warning'
    },
    color: {
        type: String,
        default: 'negative'
    },
    actionLabel: {
        type: String,
    }
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const open = defineModel('open');

function onConfirm() {
    emit('confirm');
}

function onCancel() {
    emit('cancel');
}
</script>

<style lang="scss" scoped>
.delete-confirmation-modal {
    width: 400px;
    max-width: 95vw;
    border-radius: 8px;
    
    .q-card__section {
        &.bg-negative {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
    }
    
    p {
        font-size: 16px;
        line-height: 1.5;
        margin: 0;
    }
}
</style>