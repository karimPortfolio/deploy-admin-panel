<template>
    <template>
        <form-modal
            v-model:open="open"
            title="roles.create"
            form="roles-form"
            @close="handleClose"
            :loading="creating"
        >
            <template #form>
                <q-form
                    id="roles-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="newRole.name"
                        :label="$t('name')"
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
import FormModal from "@/components/modals/FormModal.vue";

const open = defineModel("open");
const emit = defineEmits(["created"]);
const newRole = ref({});

const { create, creating, validation } = useResourceCreate("admin/roles");

const handleCreate = async () => {
    await create(newRole.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newRole.value = {};
    validation.value = {};
    open.value = false;
};
</script>
