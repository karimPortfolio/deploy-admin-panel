<template>
    <template>
        <form-modal
            v-model:open="open"
            title="Edit Security Group"
            form="ssh-key-form"
            @close="handleClose"
            :loading="updating"
            :modal-loading="loading"
        >
            <template #form>
                <q-form
                    id="ssh-key-form"
                    @submit.prevent.self="handleUpdate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="sshKey.name"
                        label="Name"
                        :error-message="validation.name?.[0]"
                        :error="'name' in validation"
                        outlined
                        hide-bottom-space
                    />
                </q-form>
            </template>
        </form-modal>
    </template>
</template>
<script setup>
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { useResourceShow } from "@/composables/useResourceShow";
import { ref, watch } from "vue";
import FormModal from "../../components/modals/FormModal.vue";

const props = defineProps({
    id: {
        type: [Number, String],
    }
});

const open = defineModel("open");
const emit = defineEmits(["updated"]);

const {
    data: sshKey,
    fetch,
    loading
} = useResourceShow("ssh-keys");

const { update, updating, validation } = useResourceUpdate("ssh-keys");

const handleUpdate = async () => {
    await update(props.id, sshKey.value);
    handleClose();
    emit("updated");
};

const handleClose = () => {
    sshKey.value = {};
    validation.value = {};
    open.value = false;
};


watch([() => props.id, open], async (newId) => {
    if (open && newId) {
        await fetch(newId);
    }
});

</script>
