<template>
    <form-modal
        v-model:open="open"
        title="databases.create"
        form="databases-form"
        @close="handleClose"
        :loading="creating"
    >
        <template #form>
            <q-form
                id="databases-form"
                @submit.prevent.self="handleCreate"
                class="q-gutter-md"
            >
                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!-- ====== DB INSTANCE IDENTIFIER ======= -->
                    <q-input
                        v-model="newDatabase.db_instance_identifier"
                        :label="$t('databases.db_instance_identifier') + '*'"
                        :error-message="validation.db_instance_identifier?.[0]"
                        :error="'db_instance_identifier' in validation"
                        minlength="1"
                        maxlength="63"
                        dense
                        outlined
                        hide-bottom-space
                        :hint="
                            $t('databases.instance_id_details') +
                            '. e.g., my-database-1'
                        "
                    />

                    <!-- ====== DATABASE NAME ======= -->
                    <q-input
                        dense
                        v-model="newDatabase.db_name"
                        :label="$t('databases.db_name') + '*'"
                        :error-message="validation.db_name?.[0]"
                        :error="'db_name' in validation"
                        outlined
                        hide-bottom-space
                    />
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!--====== ENGINES =======-->
                    <custom-select
                        v-model="newDatabase.engine"
                        :label="$t('databases.engine') + '*'"
                        resource="rds-databases/engines"
                        option-label="label"
                        :error-message="validation.engine?.[0]"
                        :error="'engine' in validation"
                        outlined
                        hide-bottom-space
                    >
                        <template v-slot:option="scope">
                            <q-item v-bind="scope.itemProps">
                                <q-item-section>
                                    <q-item-label>{{
                                        scope.opt.label
                                    }}</q-item-label>
                                    <q-item-label caption>{{
                                        scope.opt.description
                                    }}</q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </custom-select>

                    <!--======== VPC SECURITY GROUP ======== -->
                    <custom-select
                        v-model="newDatabase.vpc_security_group"
                        :label="$t('security_groups.title') + '*'"
                        resource="security-groups"
                        option-label="name"
                        :error-message="validation.vpc_security_group?.[0]"
                        :error="'vpc_security_group' in validation"
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
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!-- ======= STORAGE TYPE ===== -->
                    <custom-select
                        v-model="newDatabase.storage_type"
                        :label="$t('databases.storage_type') + '*'"
                        resource="rds-databases/storage-types"
                        option-label="value"
                        option-value="value"
                        :error-message="validation.storage_type?.[0]"
                        :error="'storage_type' in validation"
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

                    <!-- ======= DB INSTANCE CLASS ====== -->
                    <custom-select
                        v-model="newDatabase.db_instance_class"
                        :label="$t('databases.db_instance_class') + '*'"
                        resource="rds-databases/instance-classes"
                        option-label="value"
                        :error-message="validation.db_instance_class?.[0]"
                        :error="'db_instance_class' in validation"
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
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!-- ====== MASTER USERNAME ======= -->
                    <q-input
                        dense
                        v-model="newDatabase.master_username"
                        :label="$t('databases.master_username') + '*'"
                        :error-message="validation.master_username?.[0]"
                        :error="'master_username' in validation"
                        outlined
                        hide-bottom-space
                        :hint="$t('databases.master_username_details')"
                    />

                    <!-- ====== MASTER PASSWORD ======= -->
                    <q-input
                        dense
                        v-model="newDatabase.master_password"
                        :label="$t('databases.master_password') + '*'"
                        :error-message="validation.master_password?.[0]"
                        :error="'master_password' in validation"
                        outlined
                        hide-bottom-space
                        :type="isPassword ? 'password' : 'text'"
                        :hint="$t('databases.master_password_security_hint')"
                    >
                        <template v-slot:append>
                            <q-icon
                                :name="
                                    isPassword ? 'visibility_off' : 'visibility'
                                "
                                class="cursor-pointer"
                                size="xs"
                                @click="isPassword = !isPassword"
                            />
                        </template>
                    </q-input>
                </div>

                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!-- ====== ALLOCATED STORAGE ======= -->
                    <q-input
                        type="number"
                        v-model="newDatabase.allocated_storage"
                        :label="$t('databases.allocated_storage') + '*'"
                        :error-message="validation.allocated_storage?.[0]"
                        :error="'allocated_storage' in validation"
                        min="20"
                        dense
                        outlined
                        hide-bottom-space
                        :hint="$t('databases.allocated_storage_hint')"
                    />

                    <!-- ====== BACKUP RETENTION ======= -->
                    <q-input
                        v-model="newDatabase.backup_retention_period"
                        :label="$t('databases.backup_retention_period')"
                        :error-message="validation.backup_retention_period?.[0]"
                        :error="'backup_retention_period' in validation"
                        dense
                        outlined
                        hide-bottom-space
                        type="number"
                        max="35"
                        min="0"
                        :hint="$t('databases.backup_retention_period_hint')"
                    />
                </div>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 md:gap-3 space-y-3 md:space-y-0"
                >
                    <!-- ====== MULTI AZ ======= -->
                    <q-toggle
                        v-model="newDatabase.multi_az"
                        :label="$t('databases.multi_az')"
                        color="primary"
                        class="q-mt-sm flex gap-1"
                    />

                   <!-- ====== STORAGE ENCRYPTED ======= -->
                    <q-toggle
                        v-model="newDatabase.storage_encrypted"
                        :label="$t('databases.encryption')"
                        color="primary"
                        class="q-mt-sm flex gap-1"
                    />
                </div>
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
const newDatabase = ref({
    multi_az: false,
    storage_encrypted: false,
});
const isPassword = ref(true);

const { create, creating, validation } = useResourceCreate("rds-databases");

const handleCreate = async () => {
    await create(newDatabase.value);
    handleClose();
    emit("created");
};

const handleClose = () => {
    newDatabase.value = {
        multi_az: false,
        publicly_accessible: false,
        storage_encrypted: false,
    };
    
    validation.value = {};
    open.value = false;
};
</script>
