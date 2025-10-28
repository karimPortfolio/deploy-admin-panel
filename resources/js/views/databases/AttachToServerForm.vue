<template>
    <template>
        <form-modal
            v-model:open="open"
            title="attach_to_server"
            form="databases-attach-form"
            @close="handleClose"
            :loading="attaching"
        >
            <template #form>
                <q-form
                    id="databases-attach-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <custom-select
                        v-model="data.server"
                        :label="$t('databases.server') + '*'"
                        resource="rds-databases/servers"
                        option-label="name"
                        :error-message="validation.server_id?.[0]"
                        :error="'server_id' in validation"
                        outlined
                        hide-bottom-space
                    >
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section>
                                    <q-item-label>{{ scope.opt.name }}</q-item-label>
                                    <q-item-label caption>{{ scope.opt.instance_id }}</q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </custom-select>

                    <q-toggle
                        v-model="data.is_primary"
                        :label="$t('databases.primary')"
                        :error-message="validation.is_primary?.[0]"
                        :error="'is_primary' in validation"
                        hide-bottom-space
                        class="mt-4 flex gap-1"
                    />
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $t('databases.primary_note') }}</div>
                </q-form>
            </template>
        </form-modal>
    </template>
</template>
<script setup>
import { useResourceCreate } from "@/composables/useResourceCreate";
import { ref } from "vue";
import FormModal from "@/components/modals/FormModal.vue";
import CustomSelect from "@/components/CustomSelect.vue";

const props = defineProps({
    id: {
        type: [String, Number],
    },
});
const open = defineModel("open");
const emit = defineEmits(["attached"]);
const data = ref({
    is_primary: false,
});

const { create, creating: attaching, validation } = useResourceCreate("rds-databases/attachments");

const handleCreate = async () => {
    data.value.rds_database_id = props.id;
    await create(data.value);

    handleClose();
    emit("attached");
};

const handleClose = () => {
    validation.value = {};
    data.value = {
        is_primary: false,
    };
    open.value = false;
};
</script>
