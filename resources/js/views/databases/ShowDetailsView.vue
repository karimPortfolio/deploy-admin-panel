<template>
    <q-page class="q-pa-md">

        <!-- ======= INCLUDES ======== -->
         <confirmation-modal
            v-model:open="openDeleteConfirmationModal"
            title="databases.delete_association"
            icon="warning"
            color="negative"
            :loading="destroying"
            @confirm="handleDelete"
            @cancel="openDeleteConfirmationModal = false"
            />

         <confirmation-modal
            v-model:open="openUpdatePrimaryModal"
            title="databases.make_primary"
            icon="info"
            color="primary"
            :loading="updating"
            :actionLabel="$t('databases.make_primary')"
            @confirm="handleUpdatePrimary"
            @cancel="openUpdatePrimaryModal = false"
            />

        <!-- ======================== -->
        <q-card class="p-4">
            <q-card-section class="p-0 flex items-center gap-2 pb-4">
                <q-icon name="sym_r_database" color="primary" size="sm" />
                <h4 class="font-meduim text-lg">{{ $t("databases.database_details") }}</h4>
            </q-card-section>
            <q-card-section
                class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 sm:gap-y-0 p-0"
                >
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.db_instance_identifier") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.db_instance_identifier }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("name") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.db_name }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.engine") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.engine?.label }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.engine_version") }}</div>
                    <div v-if="database?.engine_version" class="text-gray-600 dark:text-gray-400">
                        {{ database?.engine_version }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400"> N/A </div> 
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.db_instance_class") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.db_instance_class?.value }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.storage_type") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.storage_type?.value }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.allocated_storage") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.allocated_storage }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.encryption") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.storage_encrypted ? $t("active") : $t("inactive") }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.availability_zone") }}</div>
                    <div
                        v-if="database?.availability_zone"
                        class="text-gray-600 dark:text-gray-400"
                        :title="database?.availability_zone"
                    >
                        {{ truncate(database?.availability_zone, 55) }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400">
                        N/A
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.publicly_accessible") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.publicly_accessible ? $t("active") : $t("inactive") }}
                    </div>
                </div>
                
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.multi_az") }}</div>
                    <div class="text-gray-600 dark:text-gray-400">
                        {{ database?.multi_az ? $t("active") : $t("inactive") }}
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("status") }}</div>
                    <q-badge
                        :text-color="database?.status?.color"
                        size="xs"
                        class="w-fit"
                    >
                        {{ database?.status?.label }}
                    </q-badge>
                </div>
                
                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.endpoint_address") }}</div>
                    <div
                        v-if="database?.endpoint_address"
                        class="text-gray-600 dark:text-gray-400"
                        :title="database?.endpoint_address"
                    >
                        {{ truncate(database?.endpoint_address, 55) }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400">
                        N/A
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.endpoint_port") }}</div>
                    <div
                        v-if="database?.endpoint_port"
                        class="text-gray-600 dark:text-gray-400"
                    >
                        {{ database?.endpoint_port }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400">
                        N/A
                    </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("databases.backup_retention_period") }}</div>
                    <div v-if="database?.backup_retention_period" class="text-gray-600 dark:text-gray-400">
                        {{ database?.backup_retention_period }}
                    </div>
                    <div v-else class="text-gray-600 dark:text-gray-400"> N/A </div>
                </div>

                <div class="q-gutter-xs pb-3 text-sm">
                    <div>{{ $t("creation_date") }}</div>
                    <div class="text-gray-600 dark:text-gray-400" :title="database.instance_create_time">
                        {{ database.created_at }}
                    </div>
                </div>
            </q-card-section>
            <q-card-section class="p-0">
                <q-inner-loading :showing="loading" class="rounded-lg" >
                    <q-spinner-tail color="primary" size="40px" />
                </q-inner-loading>
            </q-card-section>
        </q-card>

        <attached-servers-card
            :servers="database?.servers || []"
            :loading="loading"
            class="mt-4"
            @delete="handleOpenDeleteModal"
            @update="handleOpenUpdatePrimaryModal"
        />
    </q-page>
</template>
<script setup>
import { onMounted, ref } from "vue";
import { useResourceShow } from "@/composables/useResourceShow";
import { useResourceDestroy } from "@/composables/useResourceDestroy";
import { useResourceUpdate } from "@/composables/useResourceUpdate";
import { useTextTruncate } from "@/composables/useTextTruncate";
import { useRoute } from "vue-router";
import AttachedServersCard from "./partials/AttachedServersCard.vue";
import ConfirmationModal from "@/components/modals/ConfirmationModal.vue";

const { data: database, fetch, loading } = useResourceShow("rds-databases");

const {
    destroy,
    destroying,
} = useResourceDestroy("rds-databases/attachments");

const {
    update,
    updating,
} = useResourceUpdate('rds-databases/attachments', {
    config: {
        method: 'PATCH'
    }
});

const route = useRoute();
const { truncate } = useTextTruncate();

const databaseId = ref(null);
const openDeleteConfirmationModal = ref(false);
const openUpdatePrimaryModal = ref(false);

const itemToDelete = ref(null);
const itemToUpdate = ref(null);
const serverData = ref(null);

const handleOpenDeleteModal = (pivot) => {
    itemToDelete.value = pivot;
    openDeleteConfirmationModal.value = true;
};

const handleOpenUpdatePrimaryModal = (pivot, server) => {
    itemToUpdate.value = pivot;
    serverData.value = server;
    openUpdatePrimaryModal.value = true;
};

const handleDelete = async () => {
    await destroy(itemToDelete.value.id);

    openDeleteConfirmationModal.value = false;
    fetch(databaseId.value);
};

const handleUpdatePrimary = async () => {
    await update(itemToUpdate.value.id, {
        is_primary: true,
        server: serverData.value,
    });

    openUpdatePrimaryModal.value = false;
    fetch(databaseId.value);
};

onMounted(() => {
    databaseId.value = route.params.id;
    if (databaseId.value) {
        fetch(databaseId.value);
    }

});
</script>
