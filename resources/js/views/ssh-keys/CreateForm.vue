<template>
    <template>
        <form-modal
            v-model:open="open"
            title="Create SSH Key"
            form="ssh-key-form"
            @close="handleClose"
            :loading="creating"
        >
            <template #form>
                <q-form
                    id="ssh-key-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="newSshKey.name"
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
import { useResourceCreate } from "@/composables/useResourceCreate";
import { ref } from "vue";
import FormModal from "../../components/modals/FormModal.vue";

const open = defineModel("open");
const emit = defineEmits(["created"]);
const newSshKey = ref({});

const { create, creating, validation } = useResourceCreate("ssh-keys");

const handleCreate = async () => {
    await create(newSshKey.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newSshKey.value = {};
    validation.value = {};
    open.value = false;
};
</script>
