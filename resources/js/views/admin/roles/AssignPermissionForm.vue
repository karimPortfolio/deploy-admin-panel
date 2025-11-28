<template>
    <template>
        <form-modal
            v-model:open="open"
            title="roles.assign_permissions"
            form="assign-permissions-form"
            @close="handleClose"
            :loading="creating"
            :modal-loading="loading"
        >
            <template #form>
                <q-form
                    id="assign-permissions-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <q-tabs
                        v-model="tab"
                        dense
                        class="text-grey"
                        active-color="primary"
                        indicator-color="primary"
                        align="justify"
                    >
                        <q-tab
                            v-for="(perms, category) in data"
                            :key="category"
                            :name="category"
                            :label="category"
                        />
                    </q-tabs>

                    <q-separator />

                    <q-tab-panels v-model="tab" animated>
                        <q-tab-panel
                            v-for="(perms, category) in data"
                            :key="category"
                            :name="category"
                        >
                            <q-checkbox
                                v-for="permission in perms"
                                :key="permission.id"
                                v-model="permission.assigned"
                                :label="permission.name"
                                :value="permission.id"
                                dense
                                class="q-mb-sm flex items-center gap-3"
                            />
                        </q-tab-panel>
                    </q-tab-panels>
                </q-form>
            </template>
        </form-modal>
    </template>
</template>
<script setup>
import { useResourceCreate } from "@/composables/useResourceCreate";
import { useResourceShow } from "@/composables/useResourceShow";
import { ref, watch } from "vue";
import FormModal from "@/components/modals/FormModal.vue";

const props = defineProps({
    id: {
        type: [Number, String],
    }
});

const open = defineModel("open");
const emit = defineEmits(["created"]);
const tab = ref("Servers");
const permissions = ref([]);

const {
    data,
    fetch,
    loading
} = useResourceShow(() => `admin/roles/${props.id}/permissions`);

const { create, creating, validation } = useResourceCreate(() => `admin/roles/${props.id}/assign-permissions`);

const handleCreate = async () => {
    syncAssignedPermissions();
    await create({
        permissions: permissions.value,
    });
    handleClose();
    emit("created");
};

const syncAssignedPermissions = () => {
    for (const category in data.value) {
        for (const permission of data.value[category]) {
            if(permission.assigned) {
                permissions.value.push(permission.key);
            }
        }
    }
};

const handleClose = () => {
    validation.value = {};
    open.value = false;
};


watch(() => props.id, async (newId) => {
    if (newId) {
        await fetch();
    }
});

</script>
