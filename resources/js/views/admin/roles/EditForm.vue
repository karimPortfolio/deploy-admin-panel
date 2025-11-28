<template>
    <template>
        <form-modal
            v-model:open="open"
            title="roles.edit"
            form="role-form"
            @close="handleClose"
            :loading="updating"
            :modal-loading="loading"
        >
            <template #form>
                <q-form
                    id="role-form"
                    @submit.prevent.self="handleUpdate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="role.name"
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
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { useResourceShow } from "@/composables/useResourceShow";
import { ref, watch } from "vue";
import FormModal from "@/components/modals/FormModal.vue";

const props = defineProps({
    id: {
        type: [Number, String],
    }
});

const open = defineModel("open");
const emit = defineEmits(["updated"]);

const {
    data: role,
    fetch,
    loading
} = useResourceShow("admin/roles");

const { update, updating, validation } = useResourceUpdate("admin/roles");

const handleUpdate = async () => {
    await update(props.id, role.value);
    handleClose();
    emit("updated");
};

const handleClose = () => {
    role.value = {};
    validation.value = {};
    open.value = false;
};


watch(() => props.id, async (newId) => {
    if (newId) {
        await fetch(newId);
    }
});

</script>
