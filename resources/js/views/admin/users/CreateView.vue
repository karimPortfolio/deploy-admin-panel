<template>
    <template>
        <form-modal
            v-model:open="open"
            title="Create New User"
            form="user-creation-form"
            @close="handleClose"
            :loading="creating"
        >
            <template #form>
                <q-form
                    id="user-creation-form"
                    @submit.prevent.self="handleCreate"
                    class="q-gutter-md"
                >
                    <q-input
                        dense
                        v-model="newUser.name"
                        label="Name*"
                        :error-message="validation.name?.[0]"
                        :error="'name' in validation"
                        outlined
                        hide-bottom-space
                    />

                    <q-input
                        dense
                        v-model="newUser.company_name"
                        label="Company Name"
                        :error-message="validation.company_name?.[0]"
                        :error="'company_name' in validation"
                        outlined
                        hide-bottom-space
                    />
                    <q-input
                        dense
                        v-model="newUser.email"
                        label="Email*"
                        :error-message="validation.email?.[0]"
                        :error="'email' in validation"
                        outlined
                        hide-bottom-space
                    />

                    <q-select
                        dense
                        v-model="newUser.role"
                        :options="rolesOptions"
                        label="Role"
                        :error-message="validation.role?.[0]"
                        :error="'role' in validation"
                        outlined
                        hide-bottom-space
                    />

                    <q-toggle
                        v-model="newUser.is_active"
                        label="Active Account"
                        color="primary"
                        dense
                        class="flex"
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
const newUser = ref({
    is_active: true,
});

const rolesOptions = [
    { label: "Admin", value: "admin" },
    { label: "User", value: "user" },
];

const { create, creating, validation } = useResourceCreate("admin/users");

const handleCreate = async () => {
    await create(newUser.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newUser.value = {};
    validation.value = {};
    open.value = false;
};
</script>
