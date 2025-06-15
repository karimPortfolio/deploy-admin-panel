<template>
    <form-modal
        v-model:open="open"
        title="Create Server"
        form="servers-form"
        @close="handleClose"
        :loading="creating"
    >
        <template #form>
            <q-form
                id="servers-form"
                @submit.prevent.self="handleCreate"
                class="q-gutter-md"
            >
                <q-input
                    dense
                    v-model="newServer.name"
                    label="Name*"
                    :error-message="validation.name?.[0]"
                    :error="'name' in validation"
                    outlined
                    hide-bottom-space
                />

                <custom-select
                    v-model="newServer.instance_type"
                    label="Instance Type*"
                    resource="servers/instance-types"
                    option-label="value"
                    option-value="value"
                    :error-message="validation.instance_type?.[0]"
                    :error="'instance_type' in validation"
                    outlined
                    hide-bottom-space
                >
                    <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                            <q-item-section>
                                <q-item-label>{{
                                    scope.opt.value
                                }}</q-item-label>
                                <q-item-label caption>{{
                                    scope.opt.description
                                }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </custom-select>

                <div class="q-mb-md">
                    <div class="text-subtitle1 q-mb-sm">Operating System*</div>
                    
                    <div
                        class="os-family-grid q-field--outlined"
                        :class="{'q-field--error': 'os_family' in validation}"
                    >
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 q-col-gutter-md q-pa-md">
                            <div
                                v-for="option in osFamilyOptions"
                                :key="option.label"
                                class=""
                            >
                                <div
                                    class="cursor-pointer text-center q-pa-sm sm:q-pa-md border border-[#ddd] transition-all ease-in-out hover:border-primary-300 rounded-md"
                                    :class="{ 'border-primary-700  text-primary': newServer.os_family?.label === option.label}"
                                    @click="handleOsFamilyChange(option)"
                                >
                                    <q-icon :name="option.icon" :size="$q.screen.lt.md ? '20px':'45px'" />
                                    <div class="text-xs sm:mt-2" >{{ option.label }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="'os_family' in validation" class="text-negative text-caption q-mt-sm ms-4">
                        {{ validation.os_family?.[0] }}
                    </div>
                </div>

                <custom-select
                    v-model="newServer.security_group_id"
                    label="Security Group*"
                    resource="security-groups"
                    option-label="name"
                    :error-message="validation.security_group_id?.[0]"
                    :error="'security_group_id' in validation"
                    outlined
                    hide-bottom-space
                >
                    <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                            <q-item-section>
                                <q-item-label>{{
                                    scope.opt.name
                                }}</q-item-label>
                                <q-item-label caption>{{
                                    scope.opt.group_id
                                }}</q-item-label>
                            </q-item-section>
                        </q-item>
                    </template>
                </custom-select>

                <custom-select
                    v-model="newServer.vpc_id"
                    label="VPC*"
                    resource="vpcs"
                    option-label="vpc_id"
                    option-value="vpc_id"
                    :error-message="validation.vpc_id?.[0]"
                    :error="'vpc_id' in validation"
                    outlined
                    hide-bottom-space
                />

                <custom-select
                    v-model="newServer.ssh_key_id"
                    resource="ssh-keys"
                    label="SSH Key"
                    :error-message="validation.ssh_key_id?.[0]"
                    :error="'ssh_key_id' in validation"
                    outlined
                    hide-bottom-space
                />
            </q-form>
        </template>
    </form-modal>
</template>
<script setup>
import { useResourceCreate } from "@/composables/useResourceCreate";
import { ref } from "vue";
import FormModal from "@/components/modals/FormModal.vue";
import CustomSelect from "@/components/CustomSelect.vue";

const open = defineModel("open");
const emit = defineEmits(["created"]);
const newServer = ref({});

const { create, creating, validation } = useResourceCreate("servers");

const osFamilyOptions = [
    { label: "Linux", value:"amazon-linux", icon: "fab fa-amazon"},
    { label: "Ubuntu", value:"ubuntu", icon: "fab fa-ubuntu"},
    { label: "Debian", value:"debian", icon: "fab fa-debian"},
    { label: "Windows", value:"windows", icon: "fab fa-windows"},
    { label: "Red Hat", value:"redhat", icon: "fab fa-redhat" }
];


const handleOsFamilyChange = (value) => {
    newServer.value.os_family = value;
};

const handleCreate = async () => {
    await create(newServer.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newServer.value = {};
    validation.value = {};
    open.value = false;
};
</script>
