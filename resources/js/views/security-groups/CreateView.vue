<template>
    <template>
        <form-modal
            v-model:open="open"
            title="Create Security Group"
            form="security-group-form"
            @close="handleClose"
            :loading="creating"
        >
            <template #form>
                <q-form
                    id="security-group-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="newSecurityGroup.name"
                        label="Name"
                        :error-message="validation.name?.[0]"
                        :error="'name' in validation"
                        outlined
                        hide-bottom-space
                    />

                    <q-input
                        type="textarea"
                        dense
                        v-model="newSecurityGroup.description"
                        label="Description"
                        :error-message="validation.description?.[0]"
                        :error="'description' in validation"
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
import FormModal from "@/components/modals/FormModal.vue";

const open = defineModel("open");
const emit = defineEmits(["created"]);
const newSecurityGroup = ref({});

const { create, creating, validation } = useResourceCreate("security-groups");

const handleCreate = async () => {
    await create(newSecurityGroup.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newSecurityGroup.value = {};
    validation.value = {};
    open.value = false;
};
</script>
